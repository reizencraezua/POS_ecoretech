<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Service::with(['category', 'size', 'unit'])->onlyTrashed()
            : Service::with(['category', 'size', 'unit']);

        // Handle search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('service_name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        // Order by ID and paginate
        $services = $query->orderBy('service_id', 'asc')->paginate(9);

        // Preserve search parameters in pagination
        $services->appends($request->query());

        return view('admin.services.index', compact('services', 'showArchived'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('category_name')->get();
        $sizes = \App\Models\Size::where('is_active', true)->orderBy('size_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('admin.services.create', compact('categories', 'sizes', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:255|unique:services,service_name',
            'base_fee' => 'required|numeric|min:0|max:999999.99',
            'layout_price' => 'nullable|numeric|min:0|max:999999.99',
            'requires_layout' => 'nullable|boolean',
            'layout_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,category_id',
            'size_id' => 'nullable|exists:sizes,size_id',
            'unit_id' => 'nullable|exists:units,unit_id',
        ], [
            'service_name.required' => 'Service name is required.',
            'service_name.unique' => 'A service with this name already exists.',
            'base_fee.required' => 'Base fee is required.',
            'base_fee.numeric' => 'Base fee must be a valid number.',
            'base_fee.min' => 'Base fee cannot be negative.',
            'base_fee.max' => 'Base fee cannot exceed ₱999,999.99.',
            'layout_price.numeric' => 'Layout price must be a valid number.',
            'layout_price.min' => 'Layout price cannot be negative.',
            'layout_price.max' => 'Layout price cannot exceed ₱999,999.99.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'layout_description.max' => 'Layout description cannot exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Service::create([
                'service_name' => $request->service_name,
                'base_fee' => $request->base_fee,
                'layout_price' => $request->layout_price ?? 0,
                'requires_layout' => $request->has('requires_layout'),
                'layout_description' => $request->layout_description,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'size_id' => $request->size_id,
                'unit_id' => $request->unit_id,
            ]);

            return redirect()->route('admin.services.index')
                ->with('success', 'Service created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create service. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        $categories = \App\Models\Category::where('is_active', true)->orderBy('category_name')->get();
        $sizes = \App\Models\Size::where('is_active', true)->orderBy('size_name')->get();
        $units = \App\Models\Unit::where('is_active', true)->orderBy('unit_name')->get();

        return view('admin.services.edit', compact('service', 'categories', 'sizes', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'service_name' => 'required|string|max:255|unique:services,service_name,' . $service->service_id . ',service_id',
            'base_fee' => 'required|numeric|min:0|max:999999.99',
            'layout_price' => 'nullable|numeric|min:0|max:999999.99',
            'requires_layout' => 'nullable|boolean',
            'layout_description' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:1000',
            'category_id' => 'nullable|exists:categories,category_id',
            'size_id' => 'nullable|exists:sizes,size_id',
            'unit_id' => 'nullable|exists:units,unit_id',
        ], [
            'service_name.required' => 'Service name is required.',
            'service_name.unique' => 'A service with this name already exists.',
            'base_fee.required' => 'Base fee is required.',
            'base_fee.numeric' => 'Base fee must be a valid number.',
            'base_fee.min' => 'Base fee cannot be negative.',
            'base_fee.max' => 'Base fee cannot exceed ₱999,999.99.',
            'layout_price.numeric' => 'Layout price must be a valid number.',
            'layout_price.min' => 'Layout price cannot be negative.',
            'layout_price.max' => 'Layout price cannot exceed ₱999,999.99.',
            'description.max' => 'Description cannot exceed 1000 characters.',
            'layout_description.max' => 'Layout description cannot exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $service->update([
                'service_name' => $request->service_name,
                'base_fee' => $request->base_fee,
                'layout_price' => $request->layout_price ?? 0,
                'requires_layout' => $request->has('requires_layout'),
                'layout_description' => $request->layout_description,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'size_id' => $request->size_id,
                'unit_id' => $request->unit_id,
            ]);

            return redirect()->route('admin.services.index')
                ->with('success', 'Service updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update service. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // Get orders that contain this service
        $orders = \App\Models\Order::whereHas('details', function ($query) use ($service) {
            $query->where('service_id', $service->service_id);
        })->with(['customer', 'payments'])->latest()->get();

        // Get all payments related to orders containing this service
        $payments = \App\Models\Payment::whereHas('order.details', function ($query) use ($service) {
            $query->where('service_id', $service->service_id);
        })->with('order.customer')->latest()->get();

        // Calculate statistics
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total_amount');
        $totalPaid = $payments->sum('amount_paid');
        $totalQuantity = $orders->sum(function ($order) use ($service) {
            return $order->details->where('service_id', $service->service_id)
                ->sum('quantity');
        });

        return view('admin.services.show', compact('service', 'orders', 'payments', 'totalOrders', 'totalRevenue', 'totalPaid', 'totalQuantity'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            // You might want to check if this service is being used in orders
            // before allowing deletion
            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service archived successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to archive service. It may be in use by existing orders.');
        }
    }

    /**
     * Archive the specified resource.
     */
    public function archive(Service $service)
    {
        try {
            $service->delete();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service archived successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to archive service.');
        }
    }

    /**
     * Restore the specified resource.
     */
    public function restore($serviceId)
    {
        try {
            $service = Service::withTrashed()->findOrFail($serviceId);
            $service->restore();

            return redirect()->route('admin.services.index')
                ->with('success', 'Service restored successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore service.');
        }
    }

    /**
     * Get service data for AJAX requests (optional utility method)
     */
    public function getServiceData($id)
    {
        try {
            $service = Service::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }
    }
}
