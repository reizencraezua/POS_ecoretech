<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'sizes', 'unit']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('category_name', 'like', "%{$search}%");
                  });
            });
        }

        $products = $query->latest()->paginate(15)->appends($request->query());

        return view('cashier.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();
        
        return view('cashier.products.create', compact('categories', 'sizes', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'unit_id' => 'required|exists:units,unit_id',
            'price' => 'required|numeric|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,size_id',
        ]);

        $product = Product::create($validated);

        if ($request->has('sizes')) {
            $product->sizes()->attach($request->sizes);
        }

        return redirect()->route('cashier.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'sizes', 'unit']);
        return view('cashier.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();
        $product->load(['sizes']);
        
        return view('cashier.products.edit', compact('product', 'categories', 'sizes', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        // Check for admin password
        if (!$this->verifyAdminPassword($request)) {
            return redirect()->back()
                ->withErrors(['admin_password' => 'Invalid admin password.'])
                ->withInput();
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'unit_id' => 'required|exists:units,unit_id',
            'price' => 'required|numeric|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,size_id',
        ]);

        $product->update($validated);

        if ($request->has('sizes')) {
            $product->sizes()->sync($request->sizes);
        } else {
            $product->sizes()->detach();
        }

        return redirect()->route('cashier.products.index')
            ->with('success', 'Product updated successfully.');
    }

    private function verifyAdminPassword(Request $request)
    {
        $adminPassword = $request->input('admin_password');
        if (!$adminPassword) {
            return false;
        }

        // Get admin user (assuming admin user ID is 1)
        $admin = \App\Models\User::find(1);
        if (!$admin) {
            return false;
        }

        return Hash::check($adminPassword, $admin->password);
    }
}
