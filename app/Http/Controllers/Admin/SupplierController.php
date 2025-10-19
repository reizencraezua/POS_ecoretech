<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Supplier::onlyTrashed()
            : Supplier::query();

        $suppliers = $query->latest()->paginate(15);

        return view('admin.suppliers.index', compact('suppliers', 'showArchived'));
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
            'supplier_contact' => 'required|string|regex:/^[0-9]{11}$/',
            'supplier_address' => 'required|string',
        ], [
            'supplier_contact.regex' => 'Contact number must be exactly 11 digits.',
        ]);

        Supplier::create($validated);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier)
    {
        // Load supplier with related data
        $supplier->load([
            'inventories' => function($query) {
                $query->withTrashed()->orderBy('created_at', 'desc');
            }
        ]);

        // Get all transactions related to this supplier
        $transactions = collect();

        // 1. Inventory transactions (stock ins)
        $inventoryTransactions = $supplier->inventories->map(function($inventory) {
            return [
                'type' => 'inventory',
                'date' => $inventory->created_at,
                'description' => 'Stock In: ' . $inventory->name,
                'quantity' => $inventory->stock_in,
                'unit' => $inventory->unit,
                'amount' => null,
                'status' => $inventory->is_active ? 'active' : 'inactive',
                'reference' => $inventory->inventory_id,
                'details' => $inventory->description
            ];
        });

        // 2. Stock usage transactions (when inventory is used in orders)
        $stockUsages = \App\Models\StockUsage::whereHas('inventory', function($query) use ($supplier) {
            $query->where('supplier_id', $supplier->supplier_id);
        })->with(['inventory', 'order.customer'])->get();

        $stockUsageTransactions = $stockUsages->map(function($usage) {
            return [
                'type' => 'stock_usage',
                'date' => $usage->created_at,
                'description' => 'Stock Used: ' . $usage->inventory->name,
                'quantity' => $usage->quantity_used,
                'unit' => $usage->inventory->unit,
                'amount' => null,
                'status' => 'used',
                'reference' => $usage->order ? $usage->order->order_id : 'N/A',
                'details' => $usage->order ? 'Order: ' . $usage->order->order_id . ' - ' . $usage->order->customer->display_name : 'Order not found'
            ];
        });

        // 3. Orders that include products from this supplier (simplified for now)
        $orderTransactions = collect();

        // Combine all transactions and sort by date
        $transactions = $transactions
            ->merge($inventoryTransactions)
            ->merge($stockUsageTransactions)
            ->merge($orderTransactions)
            ->sortByDesc('date')
            ->values();

        return view('admin.suppliers.show', compact('supplier', 'transactions'));
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
            'supplier_contact' => 'required|string|regex:/^[0-9]{11}$/',
            'supplier_address' => 'required|string',
        ], [
            'supplier_contact.regex' => 'Contact number must be exactly 11 digits.',
        ]);

        $supplier->update($validated);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier updated successfully.');
    }


    public function archive(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier archived successfully.');
    }

    public function restore($supplierId)
    {
        $supplier = Supplier::withTrashed()->findOrFail($supplierId);
        $supplier->restore();

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier restored successfully.');
    }
}
