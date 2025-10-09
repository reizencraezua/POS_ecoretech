<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\DiscountRule;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is cashier or admin
        $user = auth('web')->user();
        if (!$user || (!$user->isCashier() && !$user->isAdmin())) {
            abort(403, 'Access denied. Cashier or Admin role required.');
        }

        $query = Quotation::with(['customer', 'details']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $quotations = $query->latest('quotation_date')->paginate(15)->appends($request->query());

        return view('cashier.quotations.index', compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::orderBy('customer_firstname')->get();
        $products = Product::with(['category.sizes'])->orderBy('product_name')->get();
        $services = Service::with(['category.sizes'])->orderBy('service_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();
        $discountRules = DiscountRule::active()->validAt()->orderBy('min_quantity')->get();

        return view('cashier.quotations.create', compact('customers', 'products', 'services', 'units', 'discountRules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,customer_id',
                'quotation_date' => 'required|date',
                'valid_until' => 'required|date|after:quotation_date',
                'items' => 'required|array|min:1',
                'items.*.type' => 'required|in:product,service',
                'items.*.id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit' => 'required|string',
                'items.*.size' => 'nullable|string',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.layout' => 'nullable|in:on,1,true,false,0',
                'items.*.layoutPrice' => 'nullable|numeric|min:0',
            ]);

            // Create quotation
            $quotation = Quotation::create([
                'quotation_id' => Quotation::generateQuotationId(),
                'customer_id' => $validated['customer_id'],
                'quotation_date' => $validated['quotation_date'],
                'valid_until' => $validated['valid_until'],
                'status' => Quotation::STATUS_PENDING,
                'total_amount' => 0, // Will be calculated after details
            ]);

            // Process quotation details
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $baseAmount = $item['quantity'] * $item['price'];
                $layoutPrice = in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]) ? ($item['layoutPrice'] ?? 0) : 0;
                $subtotal = $baseAmount + $layoutPrice;
                $totalAmount += $subtotal;

                $quotation->details()->create([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'size' => $item['size'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'layout' => in_array($item['layout'] ?? false, ['on', '1', 'true', 1, true]),
                    'layout_price' => $layoutPrice,
                    'product_id' => $item['type'] === 'product' ? $item['id'] : null,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                ]);
            }

            // Update total amount
            $quotation->update(['total_amount' => $totalAmount]);

            return redirect()->route('cashier.quotations.index')
                ->with('success', 'Quotation created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating quotation: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create quotation. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'details.product', 'details.service']);
        return view('cashier.quotations.show', compact('quotation'));
    }

    /**
     * Update quotation status
     */
    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $quotation->update(['status' => $request->status]);

        $statusMessage = ucfirst($request->status);
        return redirect()->back()
            ->with('success', "Quotation {$statusMessage} successfully.");
    }

    /**
     * Check if quotation has layout items
     */
    public function checkLayout(Quotation $quotation)
    {
        $hasLayout = $quotation->details()->where('layout', true)->exists();
        
        return response()->json(['hasLayout' => $hasLayout]);
    }

    /**
     * Convert quotation to job order
     */
    public function convertToJob(Request $request)
    {
        try {
            $validated = $request->validate([
                'quotation_id' => 'required|exists:quotations,quotation_id',
                'employee_id' => 'required|exists:employees,employee_id',
                'layout_employee_id' => 'nullable|exists:employees,employee_id',
                'order_date' => 'required|date',
                'deadline_date' => 'required|date|after:order_date',
                'payment_method' => 'nullable|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment_term' => 'nullable|in:Downpayment,Initial,Full',
                'amount_paid' => 'nullable|numeric|min:0',
                'reference_number' => 'nullable|string|max:255',
                'payment_remarks' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Get quotation with details
            $quotation = Quotation::with(['customer', 'details.product', 'details.service'])->findOrFail($validated['quotation_id']);

            // Check if quotation is approved
            if ($quotation->status !== 'approved') {
                throw new \Exception('Only approved quotations can be converted to job orders.');
            }

            // Create order from quotation
            $order = Order::create([
                'order_id' => Order::generateOrderId(),
                'customer_id' => $quotation->customer_id,
                'employee_id' => $validated['employee_id'],
                'layout_employee_id' => $validated['layout_employee_id'] ?? null,
                'order_date' => $validated['order_date'],
                'deadline_date' => $validated['deadline_date'],
                'order_status' => Order::STATUS_ON_PROCESS,
                'total_amount' => 0, // Will be calculated after details
            ]);

            // Convert quotation details to order details
            $totalAmount = 0;
            foreach ($quotation->details as $detail) {
                $baseAmount = $detail->quantity * $detail->price;
                $layoutPrice = $detail->layout ? $detail->layout_price : 0;
                $subtotal = $baseAmount + $layoutPrice;
                $totalAmount += $subtotal;

                $order->details()->create([
                    'quantity' => $detail->quantity,
                    'unit' => $detail->unit,
                    'size' => $detail->size,
                    'price' => $detail->price,
                    'subtotal' => $subtotal,
                    'layout' => $detail->layout,
                    'layout_price' => $layoutPrice,
                    'product_id' => $detail->product_id,
                    'service_id' => $detail->service_id,
                ]);
            }

            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            // Create initial payment if provided
            if (!empty($validated['amount_paid']) && $validated['amount_paid'] > 0) {
                $amountPaid = (float) $validated['amount_paid'];
                $remaining = max(0, $totalAmount - $amountPaid);
                $change = $amountPaid > $totalAmount ? ($amountPaid - $totalAmount) : 0;

                Payment::create([
                    'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                    'payment_date' => now()->format('Y-m-d'),
                    'payment_method' => $validated['payment_method'] ?? 'Cash',
                    'payment_term' => $validated['payment_term'] ?? 'Initial',
                    'amount_paid' => $amountPaid,
                    'change' => $change,
                    'balance' => $remaining,
                    'reference_number' => $validated['reference_number'] ?? null,
                    'remarks' => $validated['payment_remarks'] ?? null,
                    'order_id' => $order->order_id,
                ]);
            }

            // Update quotation status to closed
            $quotation->update(['status' => 'closed']);

            DB::commit();

            return redirect()->route('cashier.orders.index')
                ->with('success', 'Quotation converted to job order successfully. Order ID: ' . $order->order_id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting quotation to job order: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to convert quotation to job order: ' . $e->getMessage())
                ->withInput();
        }
    }
}
