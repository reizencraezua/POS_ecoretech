<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('supplier_email', 'like', "%{$search}%")
                  ->orWhere('supplier_contact', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(15);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email|unique:suppliers,supplier_email',
            'supplier_contact' => 'required|string|max:20',
            'supplier_address' => 'required|string',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_email' => 'nullable|email|unique:suppliers,supplier_email,' . $supplier->supplier_id . ',supplier_id',
            'supplier_contact' => 'required|string|max:20',
            'supplier_address' => 'required|string',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier deleted successfully.');
    }
}
