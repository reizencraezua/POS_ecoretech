<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Product::with(['category', 'size', 'unit'])->onlyTrashed()
            : Product::with(['category', 'size', 'unit']);


        $products = $query->orderBy('product_id', 'asc')->paginate(15);

        return view('admin.products.index', compact('products', 'showArchived'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_name' => 'required|string|max:255|unique:products,product_name',
                'base_price' => 'required|numeric|min:0|max:999999.99',
                'layout_price' => 'nullable|numeric|min:0|max:999999.99',
                'requires_layout' => 'nullable|boolean',
                'layout_description' => 'nullable|string|max:1000',
                'product_description' => 'nullable|string|max:2000',
                'category_id' => 'nullable|exists:categories,category_id',
                'size_id' => 'nullable|exists:sizes,size_id',
                'unit_id' => 'nullable|exists:units,unit_id',
            ], [
                'product_name.required' => 'Product name is required.',
                'product_name.unique' => 'A product with this name already exists.',
                'product_name.max' => 'Product name cannot exceed 255 characters.',
                'base_price.required' => 'Base price is required.',
                'base_price.numeric' => 'Base price must be a number.',
                'base_price.min' => 'Base price cannot be negative.',
                'base_price.max' => 'Base price cannot exceed ₱999,999.99.',
                'layout_price.numeric' => 'Layout price must be a number.',
                'layout_price.min' => 'Layout price cannot be negative.',
                'layout_price.max' => 'Layout price cannot exceed ₱999,999.99.',
                'category_id.exists' => 'Selected category does not exist.',
                'size_id.exists' => 'Selected size does not exist.',
                'unit_id.exists' => 'Selected unit does not exist.',
                'layout_description.max' => 'Layout description cannot exceed 1000 characters.',
                'product_description.max' => 'Product description cannot exceed 2000 characters.',
            ]);

            DB::beginTransaction();

            Product::create($validated);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Product validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
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
        try {
            $validated = $request->validate([
                'product_name' => 'required|string|max:255|unique:products,product_name,' . $product->product_id . ',product_id',
                'base_price' => 'required|numeric|min:0|max:999999.99',
                'layout_price' => 'nullable|numeric|min:0|max:999999.99',
                'requires_layout' => 'nullable|boolean',
                'layout_description' => 'nullable|string|max:1000',
                'product_description' => 'nullable|string|max:2000',
                'category_id' => 'nullable|exists:categories,category_id',
                'size_id' => 'nullable|exists:sizes,size_id',
                'unit_id' => 'nullable|exists:units,unit_id',
            ], [
                'product_name.required' => 'Product name is required.',
                'product_name.unique' => 'A product with this name already exists.',
                'product_name.max' => 'Product name cannot exceed 255 characters.',
                'base_price.required' => 'Base price is required.',
                'base_price.numeric' => 'Base price must be a number.',
                'base_price.min' => 'Base price cannot be negative.',
                'base_price.max' => 'Base price cannot exceed ₱999,999.99.',
                'layout_price.numeric' => 'Layout price must be a number.',
                'layout_price.min' => 'Layout price cannot be negative.',
                'layout_price.max' => 'Layout price cannot exceed ₱999,999.99.',
                'category_id.exists' => 'Selected category does not exist.',
                'size_id.exists' => 'Selected size does not exist.',
                'unit_id.exists' => 'Selected unit does not exist.',
                'layout_description.max' => 'Layout description cannot exceed 1000 characters.',
                'product_description.max' => 'Product description cannot exceed 2000 characters.',
            ]);

            DB::beginTransaction();

            $product->update($validated);

            DB::commit();

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Product validation error: ' . json_encode($e->errors()));
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to update product: ' . $e->getMessage())
                ->withInput();
        }
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
