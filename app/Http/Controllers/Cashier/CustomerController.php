<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                  ->orWhere('customer_lastname', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%")
                  ->orWhere('contact_number1', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->paginate(15)->appends($request->query());

        return view('cashier.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('cashier.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_firstname' => 'required|string|max:255',
            'customer_lastname' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'contact_number1' => 'required|string|max:20',
            'contact_number2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        Customer::create($validated);

        return redirect()->route('cashier.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders.payments', 'orders.deliveries']);
        return view('cashier.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('cashier.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        // Check for admin password
        if (!$this->verifyAdminPassword($request)) {
            return redirect()->back()
                ->withErrors(['admin_password' => 'Invalid admin password.'])
                ->withInput();
        }

        $validated = $request->validate([
            'customer_firstname' => 'required|string|max:255',
            'customer_lastname' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'contact_number1' => 'required|string|max:20',
            'contact_number2' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ]);

        $customer->update($validated);

        return redirect()->route('cashier.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    private function verifyAdminPassword(Request $request)
    {
        $adminPassword = $request->input('admin_password');
        if (!$adminPassword) {
            return false;
        }

        // Get admin user (assuming admin user ID is 1)
        $admin = \App\Models\User::find(1);
        if (!$admin) {
            return false;
        }

        return Hash::check($adminPassword, $admin->password);
    }
}
