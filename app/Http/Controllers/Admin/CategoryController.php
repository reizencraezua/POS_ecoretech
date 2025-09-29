<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Size;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('category_name', 'like', "%{$search}%")
                ->orWhere('category_description', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('category_name')->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $sizes = Size::with('unit')->where('is_active', true)->orderBy('unit_id')->orderBy('size_name')->get();
        return view('admin.categories.create', compact('sizes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'category_description' => 'nullable|string',
            'category_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,size_id',
        ]);

        $category = Category::create($validated);

        // Attach selected sizes
        if ($request->has('size_ids')) {
            $category->sizes()->attach($request->size_ids);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $category->load(['products', 'services', 'sizes']);
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $sizes = Size::with('unit')->where('is_active', true)->orderBy('unit_id')->orderBy('size_name')->get();
        $category->load('sizes');
        return view('admin.categories.edit', compact('category', 'sizes'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
            'category_description' => 'nullable|string',
            'category_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean',
            'size_ids' => 'nullable|array',
            'size_ids.*' => 'exists:sizes,size_id',
        ]);

        $category->update($validated);

        // Sync selected sizes
        if ($request->has('size_ids')) {
            $category->sizes()->sync($request->size_ids);
        } else {
            $category->sizes()->detach();
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
