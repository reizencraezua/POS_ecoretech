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
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Category::onlyTrashed()
            : Category::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('category_name', 'like', "%{$search}%")
                ->orWhere('category_description', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('category_name')->paginate(15)->appends($request->query());

        return view('admin.categories.index', compact('categories', 'showArchived'));
    }

    public function create()
    {
        $sizeGroups = Size::where('is_active', true)
            ->select('size_group')
            ->distinct()
            ->orderBy('size_group')
            ->pluck('size_group');
        
        $sizes = Size::with('unit')->where('is_active', true)->orderBy('size_group')->orderBy('size_name')->get();
        return view('admin.categories.create', compact('sizes', 'sizeGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'category_description' => 'nullable|string',
            'category_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'size_group' => 'nullable|string|in:clothing,mug,paper,small_format,banner,vinyl,roll,specialty,poster,custom',
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
        $sizeGroups = Size::where('is_active', true)
            ->select('size_group')
            ->distinct()
            ->orderBy('size_group')
            ->pluck('size_group');
        
        $sizes = Size::with('unit')->where('is_active', true)->orderBy('size_group')->orderBy('size_name')->get();
        $category->load('sizes');
        return view('admin.categories.edit', compact('category', 'sizes', 'sizeGroups'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $category->category_id . ',category_id',
            'category_description' => 'nullable|string',
            'category_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'size_group' => 'nullable|string|in:clothing,mug,paper,small_format,banner,vinyl,roll,specialty,poster,custom',
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
            ->with('success', 'Category archived successfully.');
    }

    public function archive(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category archived successfully.');
    }

    public function restore($categoryId)
    {
        $category = Category::withTrashed()->findOrFail($categoryId);
        $category->restore();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category restored successfully.');
    }

    public function getSizesByGroup(Request $request)
    {
        $sizeGroup = $request->get('size_group');
        
        if (!$sizeGroup) {
            return response()->json([]);
        }

        $sizes = Size::with('unit')
            ->where('is_active', true)
            ->where('size_group', $sizeGroup)
            ->orderBy('size_name')
            ->get();

        return response()->json($sizes);
    }
}
