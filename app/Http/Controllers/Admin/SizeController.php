<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $query = Size::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('size_name', 'like', "%{$search}%")
                ->orWhere('size_description', 'like', "%{$search}%");
        }

        if ($request->has('type')) {
            $query->where('size_type', $request->type);
        }

        $sizes = $query->orderBy('size_name')->paginate(15);

        return view('admin.sizes.index', compact('sizes'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255',
            'size_type' => 'required|in:apparel,print,other',
            'size_description' => 'nullable|string',
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
        return view('admin.sizes.edit', compact('size'));
    }

    public function update(Request $request, Size $size)
    {
        $validated = $request->validate([
            'size_name' => 'required|string|max:255',
            'size_type' => 'required|in:apparel,print,other',
            'size_description' => 'nullable|string',
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
            ->with('success', 'Size deleted successfully.');
    }
}
