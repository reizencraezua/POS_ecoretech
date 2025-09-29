<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('unit_name', 'like', "%{$search}%")
                ->orWhere('unit_description', 'like', "%{$search}%");
        }

        if ($request->has('type')) {
            $query->where('unit_type', $request->type);
        }

        $units = $query->orderBy('unit_name')->paginate(15);

        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_symbol' => 'nullable|string|max:10',
            'unit_type' => 'required|in:quantity,area,length,weight',
            'unit_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Unit::create($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit created successfully.');
    }

    public function show(Unit $unit)
    {
        $unit->load(['products', 'services']);
        return view('admin.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'unit_name' => 'required|string|max:255',
            'unit_symbol' => 'nullable|string|max:10',
            'unit_type' => 'required|in:quantity,area,length,weight',
            'unit_description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $unit->update($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
