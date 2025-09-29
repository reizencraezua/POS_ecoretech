<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountRule;
use Illuminate\Http\Request;

class DiscountRuleController extends Controller
{
    public function index()
    {
        $discountRules = DiscountRule::orderBy('min_quantity')->get();
        return view('admin.discount-rules.index', compact('discountRules'));
    }

    public function create()
    {
        return view('admin.discount-rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1|gte:min_quantity',
            'discount_percentage' => 'required_without:discount_amount|nullable|numeric|min:0|max:100',
            'discount_amount' => 'required_without:discount_percentage|nullable|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        // Ensure only one discount type is set
        if ($validated['discount_type'] === 'percentage') {
            $validated['discount_amount'] = 0;
        } else {
            $validated['discount_percentage'] = 0;
        }

        DiscountRule::create($validated);

        return redirect()->route('admin.discount-rules.index')
            ->with('success', 'Discount rule created successfully.');
    }

    public function show(DiscountRule $discountRule)
    {
        return view('admin.discount-rules.show', compact('discountRule'));
    }

    public function edit(DiscountRule $discountRule)
    {
        return view('admin.discount-rules.edit', compact('discountRule'));
    }

    public function update(Request $request, DiscountRule $discountRule)
    {
        $validated = $request->validate([
            'rule_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1|gte:min_quantity',
            'discount_percentage' => 'required_without:discount_amount|nullable|numeric|min:0|max:100',
            'discount_amount' => 'required_without:discount_percentage|nullable|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
        ]);

        // Ensure only one discount type is set
        if ($validated['discount_type'] === 'percentage') {
            $validated['discount_amount'] = 0;
        } else {
            $validated['discount_percentage'] = 0;
        }

        $discountRule->update($validated);

        return redirect()->route('admin.discount-rules.index')
            ->with('success', 'Discount rule updated successfully.');
    }

    public function destroy(DiscountRule $discountRule)
    {
        $discountRule->delete();

        return redirect()->route('admin.discount-rules.index')
            ->with('success', 'Discount rule archived successfully.');
    }

    public function archive(DiscountRule $discountRule)
    {
        $discountRule->delete();

        return redirect()->route('admin.discount-rules.index')
            ->with('success', 'Discount rule archived successfully.');
    }

    public function restore($ruleId)
    {
        $discountRule = DiscountRule::withTrashed()->findOrFail($ruleId);
        $discountRule->restore();

        return redirect()->route('admin.discount-rules.index')
            ->with('success', 'Discount rule restored successfully.');
    }

    public function toggleStatus(DiscountRule $discountRule)
    {
        $discountRule->update(['is_active' => !$discountRule->is_active]);

        $status = $discountRule->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Discount rule {$status} successfully.");
    }
}
