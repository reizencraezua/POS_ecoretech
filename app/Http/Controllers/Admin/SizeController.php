<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Size::onlyTrashed()->with('unit')
            : Size::with('unit');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('size_name', 'like', "%{$search}%")
                ->orWhere('size_value', 'like', "%{$search}%")
                ->orWhereHas('unit', function ($q) use ($search) {
                    $q->where('unit_name', 'like', "%{$search}%");
                });
        }

        if ($request->has('unit')) {
            $query->where('unit_id', $request->unit);
        }

        $sizes = $query->orderBy('unit_id')->orderBy('size_name')->paginate(15)->appends($request->query());
        $units = Unit::orderBy('unit_name')->get();

        return view('admin.sizes.index', compact('sizes', 'units', 'showArchived'));
    }

    public function create()
    {
        $units = Unit::orderBy('unit_name')->get();
        return view('admin.sizes.create', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255',
            'size_value' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,unit_id',
            'is_active' => 'boolean',
        ]);

        Size::create($validated);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size created successfully.');
    }

    public function show(Size $size)
    {
        $size->load(['products', 'services']);
        return view('admin.sizes.show', compact('size'));
    }

    public function edit(Size $size)
    {
        $units = Unit::orderBy('unit_name')->get();
        return view('admin.sizes.edit', compact('size', 'units'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255',
            'size_value' => 'required|string|max:255',
            'unit_id' => 'required|exists:units,unit_id',
            'is_active' => 'boolean',
        ]);

        $size->update($validated);

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size updated successfully.');
    }

    public function destroy(Size $size)
    {
        $size->delete();

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size archived successfully.');
    }

    public function archive(Size $size)
    {
        $size->delete();

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size archived successfully.');
    }

    public function restore($sizeId)
    {
        $size = Size::withTrashed()->findOrFail($sizeId);
        $size->restore();

        return redirect()->route('admin.sizes.index')
            ->with('success', 'Size restored successfully.');
    }
}
