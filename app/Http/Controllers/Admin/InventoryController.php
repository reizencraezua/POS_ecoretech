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
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Inventory::withTrashed()->whereNotNull('deleted_at')->with('supplier')
            : Inventory::with('supplier');

        $inventories = $query->orderBy('created_at', 'desc')->paginate(15);

        $criticalInventories = Inventory::where('is_active', true)
            ->whereRaw('stocks <= critical_level')
            ->count();

        return view('admin.inventories.index', compact('inventories', 'criticalInventories', 'showArchived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.inventories.create', compact('suppliers'));
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

            return redirect()->route('admin.inventories.index')
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
        return view('admin.inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::all();
        return view('admin.inventories.edit', compact('inventory', 'suppliers'));
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

            return redirect()->route('admin.inventories.index')
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
            return redirect()->route('admin.inventories.index')
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

        return view('admin.inventories.critical', compact('inventories'));
    }

    /**
     * Archive an inventory item.
     */
    public function archive(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return redirect()->route('admin.inventories.index')
                ->with('success', 'Inventory item archived successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to archive inventory item. Please try again.');
        }
    }

    /**
     * Restore an archived inventory item.
     */
    public function restore($id)
    {
        try {
            $inventory = Inventory::withTrashed()->findOrFail($id);
            $inventory->restore();
            return redirect()->route('admin.inventories.index')
                ->with('success', 'Inventory item restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore inventory item. Please try again.');
        }
    }

    /**
     * Search inventories for API.
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $inventories = Inventory::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('inventory_id', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'inventory_id', 'name', 'stocks', 'unit']);

        return response()->json($inventories);
    }

    /**
     * Search suppliers for API.
     */
    public function searchSuppliers(Request $request)
    {
        $query = $request->get('q', '');
        
        $suppliers = Supplier::where('supplier_name', 'like', "%{$query}%")
            ->limit(10)
            ->get(['supplier_id', 'supplier_name']);

        return response()->json($suppliers);
    }
}
