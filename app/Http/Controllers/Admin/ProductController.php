<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Product::with(['category', 'size', 'unit'])->onlyTrashed()
            : Product::with(['category', 'size', 'unit']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('product_id', 'asc')->paginate(15);
        $products->appends($request->query());

        return view('admin.products.index', compact('products', 'showArchived'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'layout_price' => 'nullable|numeric|min:0',
            'requires_layout' => 'nullable|boolean',
            'layout_description' => 'nullable|string',
            'product_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,category_id',
            'size_id' => 'nullable|exists:sizes,size_id',
            'unit_id' => 'nullable|exists:units,unit_id',
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        // Get orders that contain this product
        $orders = \App\Models\Order::whereHas('details', function ($query) use ($product) {
            $query->where('product_id', $product->product_id);
        })->with(['customer', 'payments'])->latest()->get();

        // Get all payments related to orders containing this product
        $payments = \App\Models\Payment::whereHas('order.details', function ($query) use ($product) {
            $query->where('product_id', $product->product_id);
        })->with('order.customer')->latest()->get();

        // Calculate statistics
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_amount');
        $totalPaid = $payments->sum('amount_paid');
        $totalQuantity = $orders->sum(function ($order) use ($product) {
            return $order->details->where('product_id', $product->product_id)
                ->sum('quantity');
        });

        return view('admin.products.show', compact('product', 'orders', 'payments', 'totalOrders', 'totalRevenue', 'totalPaid', 'totalQuantity'));
    }

    public function edit(Product $product)
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('category_name')->get();
        $sizes = \App\Models\Size::where('is_active', true)->orderBy('size_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'sizes', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'layout_price' => 'nullable|numeric|min:0',
            'requires_layout' => 'nullable|boolean',
            'layout_description' => 'nullable|string',
            'product_description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,category_id',
            'size_id' => 'nullable|exists:sizes,size_id',
            'unit_id' => 'nullable|exists:units,unit_id',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product archived successfully.');
    }

    public function archive(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product archived successfully.');
    }

    public function restore($productId)
    {
        $product = Product::withTrashed()->findOrFail($productId);
        $product->restore();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product restored successfully.');
    }
}
