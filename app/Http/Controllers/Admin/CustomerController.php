<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Customer::onlyTrashed()
            : Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $customers = $query->withCount(['orders', 'quotations'])
            ->orderBy('customer_id', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // If it's an AJAX request, return only the table content
        if ($request->ajax()) {
            return view('admin.customers.partials.customers-table', compact('customers', 'showArchived'));
        }

        return view('admin.customers.index', compact('customers', 'showArchived'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        // Debug: Log the request details
        \Log::info('Customer store request', [
            'is_ajax' => $request->ajax(),
            'wants_json' => $request->wantsJson(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'data' => $request->all()
        ]);
        
        try {
            $validated = $request->validate([
                'customer_firstname' => 'required|string|max:255',
                'customer_middlename' => 'nullable|string|max:255',
                'customer_lastname' => 'required|string|max:255',
                'business_name' => 'nullable|string|max:255',
                'customer_address' => 'required|string',
                'customer_email' => 'nullable|email|unique:customers,customer_email',
                'contact_person1' => 'required|string|max:255',
                'contact_number1' => 'required|string|regex:/^[0-9]{11}$/',
                'contact_person2' => 'nullable|string|max:255',
                'contact_number2' => 'nullable|string|regex:/^[0-9]{11}$/',
                'tin' => 'nullable|string|max:50',
            ], [
                'contact_number1.regex' => 'Primary contact number must be exactly 11 digits.',
                'contact_number2.regex' => 'Secondary contact number must be exactly 11 digits.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $customer = Customer::create($validated);

        // Check if this is an AJAX request (from modal)
        if ($request->ajax() || $request->wantsJson()) {
            $response = [
                'success' => true,
                'customer' => [
                    'customer_id' => $customer->customer_id,
                    'display_name' => $customer->display_name,
                    'customer_firstname' => $customer->customer_firstname,
                    'customer_lastname' => $customer->customer_lastname,
                    'business_name' => $customer->business_name,
                ],
                'message' => 'Customer created successfully.'
            ];
            
            \Log::info('Customer store AJAX response', $response);
            return response()->json($response);
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['quotations', 'orders.payments']);
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'customer_firstname' => 'required|string|max:255',
            'customer_middlename' => 'nullable|string|max:255',
            'customer_lastname' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'customer_address' => 'required|string',
            'customer_email' => 'nullable|email|unique:customers,customer_email,' . $customer->customer_id . ',customer_id',
            'contact_person1' => 'required|string|max:255',
            'contact_number1' => 'required|string|regex:/^[0-9]{11}$/',
            'contact_person2' => 'nullable|string|max:255',
            'contact_number2' => 'nullable|string|regex:/^[0-9]{11}$/',
            'tin' => 'nullable|string|max:50',
        ], [
            'contact_number1.regex' => 'Primary contact number must be exactly 11 digits.',
            'contact_number2.regex' => 'Secondary contact number must be exactly 11 digits.',
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer archived successfully.');
    }

    public function archive(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer archived successfully.');
    }

    public function restore($customerId)
    {
        $customer = Customer::withTrashed()->findOrFail($customerId);
        $customer->restore();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer restored successfully.');
    }
}
