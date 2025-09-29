<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Quotation::onlyTrashed()->with(['customer', 'details'])
            : Quotation::with(['customer', 'details']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->appends($request->query());

        return view('admin.quotations.index', compact('quotations', 'showArchived'));
    }

    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();

        return view('admin.quotations.create', compact('customers', 'products', 'services', 'discountRules'));
    }

    public function store(Request $request)
    {
        try {
            // Debug: Log the incoming request data
            \Log::info('Quotation creation request data:', $request->all());
            \Log::info('Quotation creation attempt started');

            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'notes' => 'nullable|string',
                'terms_and_conditions' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit' => 'nullable|string',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
                'items.*.discountAmount' => 'nullable|numeric|min:0',
                'items.*.discountRule' => 'nullable|string',
            ]);

            $totalAmount = 0;

            $quotation = Quotation::create([
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'notes' => $validated['notes'],
                'terms_and_conditions' => $validated['terms_and_conditions'],
                'status' => 'Pending',
                'total_amount' => 0, // Will be calculated after details
            ]);

            // Process quotation details
            foreach ($validated['items'] as $item) {
                // Calculate base amount (Quantity × Price)
                $baseAmount = $item['quantity'] * $item['price'];

                // Apply discount to base amount
                $discountAmount = $item['discountAmount'] ?? 0;
                $subtotal = $baseAmount - $discountAmount;

                // Add layout price if checked (after discount)
                $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
                if ($layoutChecked) {
                    $subtotal += $item['layoutPrice'] ?? 0;
                }

                $subtotal = max(0, $subtotal); // Ensure subtotal is not negative
                $totalAmount += $subtotal; // No VAT for quotations

                $quotation->details()->create([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Update quotation with final total amount
            $quotation->update(['total_amount' => $totalAmount]);

            return redirect()->route('admin.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in quotation creation:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating quotation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create quotation: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'details.product', 'details.service']);
        return view('admin.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::orderBy('service_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        $quotation->load(['details']);

        return view('admin.quotations.edit', compact('quotation', 'customers', 'products', 'services', 'discountRules'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'quotation_date' => 'required|date',
            'notes' => 'nullable|string',
            'terms_and_conditions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:product,service',
            'items.*.id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'nullable|string',
            'items.*.size' => 'nullable|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.layout' => 'nullable|in:on,1,true,false,0',
            'items.*.layoutPrice' => 'nullable|numeric|min:0',
            'items.*.discountAmount' => 'nullable|numeric|min:0',
            'items.*.discountRule' => 'nullable|string',
        ]);

        $totalAmount = 0;

        $quotation->update([
            'customer_id' => $validated['customer_id'],
            'quotation_date' => $validated['quotation_date'],
            'notes' => $validated['notes'],
            'terms_and_conditions' => $validated['terms_and_conditions'],
        ]);

        // Delete existing quotation details
        $quotation->details()->delete();

        // Create new quotation details
        foreach ($validated['items'] as $item) {
            // Calculate base amount (Quantity × Price)
            $baseAmount = $item['quantity'] * $item['price'];

            // Apply discount to base amount
            $discountAmount = $item['discountAmount'] ?? 0;
            $subtotal = $baseAmount - $discountAmount;

            // Add layout price if checked (after discount)
            $layoutChecked = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]);
            if ($layoutChecked) {
                $subtotal += $item['layoutPrice'] ?? 0;
            }

            $subtotal = max(0, $subtotal); // Ensure subtotal is not negative
            $totalAmount += $subtotal; // No VAT for quotations

            $quotation->details()->create([
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'size' => $item['size'],
                'price' => $item['price'],
                'subtotal' => $subtotal,
                'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                'service_id' => $item['type'] === 'service' ? $item['id'] : null,
            ]);
        }

        // Update quotation with final total amount
        $quotation->update(['total_amount' => $totalAmount]);

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation updated successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Closed',
        ]);

        $quotation->update(['status' => $validated['status']]);

        return back()->with('success', 'Quotation status updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation archived successfully.');
    }

    public function archive(Quotation $quotation)
    {
        $quotation->delete();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation archived successfully.');
    }

    public function restore($quotationId)
    {
        $quotation = Quotation::withTrashed()->findOrFail($quotationId);
        $quotation->restore();

        return redirect()->route('admin.quotations.index')
            ->with('success', 'Quotation restored successfully.');
    }
}
