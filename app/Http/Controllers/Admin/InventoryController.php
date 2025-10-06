<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\StockUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inventory::with('supplier');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('inventory_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('supplier', function($supplierQuery) use ($searchTerm) {
                      $supplierQuery->where('supplier_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $inventories = $query->orderBy('created_at', 'desc')->paginate(15);
        $inventories->appends($request->query());

        $criticalInventories = Inventory::where('is_active', true)
            ->whereRaw('stocks <= critical_level')
            ->count();

        return view('admin.inventory.index', compact('inventories', 'criticalInventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.inventory.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'stocks' => 'required|integer|min:0',
            'critical_level' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'unit' => 'nullable|string|max:50'
        ]);

        try {
            DB::beginTransaction();

            $inventory = Inventory::create([
                'inventory_id' => Inventory::generateInventoryId(),
                'name' => $request->name,
                'description' => $request->description,
                'stocks' => $request->stocks,
                'stock_in' => $request->stocks,
                'critical_level' => $request->critical_level,
                'supplier_id' => $request->supplier_id,
                'unit' => $request->unit,
                'last_updated' => now()
            ]);

            DB::commit();

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create inventory item. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('supplier', 'stockUsages');
        return view('admin.inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::all();
        return view('admin.inventory.edit', compact('inventory', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'critical_level' => 'required|integer|min:1',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'unit' => 'nullable|string|max:50',
            'is_active' => 'boolean'
        ]);

        try {
            $inventory->update([
                'name' => $request->name,
                'description' => $request->description,
                'critical_level' => $request->critical_level,
                'supplier_id' => $request->supplier_id,
                'unit' => $request->unit,
                'is_active' => $request->has('is_active'),
                'last_updated' => now()
            ]);

            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update inventory item. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return redirect()->route('admin.inventory.index')
                ->with('success', 'Inventory item deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete inventory item. Please try again.');
        }
    }

    /**
     * Add stock to inventory.
     */
    public function addStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $inventory->updateStock($request->quantity, 'add');
            
            return redirect()->back()
                ->with('success', "Added {$request->quantity} units to {$inventory->name}.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to add stock. Please try again.');
        }
    }

    /**
     * Use stock from inventory.
     */
    public function useStock(Request $request, Inventory $inventory)
    {
        $request->validate([
            'quantity_used' => 'required|integer|min:1|max:' . $inventory->stocks,
            'purpose' => 'nullable|string|max:255',
            'used_by' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Create stock usage record
            StockUsage::create([
                'inventory_id' => $inventory->id,
                'quantity_used' => $request->quantity_used,
                'purpose' => $request->purpose,
                'used_by' => $request->used_by,
                'used_at' => now()
            ]);

            // Update inventory stock
            $inventory->updateStock($request->quantity_used, 'subtract');

            DB::commit();

            return redirect()->back()
                ->with('success', "Used {$request->quantity_used} units from {$inventory->name}.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to use stock. Please try again.');
        }
    }

    /**
     * Get critical level inventories.
     */
    public function critical()
    {
        $inventories = Inventory::with('supplier')
            ->where('is_active', true)
            ->whereRaw('stocks <= critical_level')
            ->orderBy('stocks', 'asc')
            ->get();

        return view('admin.inventory.critical', compact('inventories'));
    }
}
