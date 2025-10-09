<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LayoutFeeSetting;
use Illuminate\Http\Request;

class LayoutFeeController extends Controller
{
    public function index()
    {
        $settings = LayoutFeeSetting::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.layout-fees.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.layout-fees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'setting_name' => 'required|string|max:255|unique:layout_fee_settings,setting_name',
            'layout_fee_amount' => 'required|numeric|min:0',
            'layout_fee_type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate all others
        if ($validated['is_active'] ?? false) {
            LayoutFeeSetting::where('is_active', true)->update(['is_active' => false]);
        }

        LayoutFeeSetting::create($validated);

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting created successfully.');
    }

    public function show(LayoutFeeSetting $layoutFee)
    {
        return view('admin.layout-fees.show', compact('layoutFee'));
    }

    public function edit(LayoutFeeSetting $layoutFee)
    {
        return view('admin.layout-fees.edit', compact('layoutFee'));
    }

    public function update(Request $request, LayoutFeeSetting $layoutFee)
    {
        $validated = $request->validate([
            'setting_name' => 'required|string|max:255|unique:layout_fee_settings,setting_name,' . $layoutFee->setting_id . ',setting_id',
            'layout_fee_amount' => 'required|numeric|min:0',
            'layout_fee_type' => 'required|in:fixed,percentage',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If this is set as active, deactivate all others
        if ($validated['is_active'] ?? false) {
            LayoutFeeSetting::where('is_active', true)
                ->where('setting_id', '!=', $layoutFee->setting_id)
                ->update(['is_active' => false]);
        }

        $layoutFee->update($validated);

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting updated successfully.');
    }

    public function destroy(LayoutFeeSetting $layoutFee)
    {
        $layoutFee->delete();

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting deleted successfully.');
    }

    public function activate(LayoutFeeSetting $layoutFee)
    {
        // Deactivate all others first
        LayoutFeeSetting::where('is_active', true)->update(['is_active' => false]);

        // Activate this one
        $layoutFee->update(['is_active' => true]);

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting activated successfully.');
    }

    public function archive(LayoutFeeSetting $layoutFee)
    {
        $layoutFee->delete();

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting archived successfully.');
    }

    public function restore($id)
    {
        $layoutFee = LayoutFeeSetting::withTrashed()->findOrFail($id);
        $layoutFee->restore();

        return redirect()->route('admin.layout-fees.index')
            ->with('success', 'Layout fee setting restored successfully.');
    }
}
