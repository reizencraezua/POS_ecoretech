<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use App\Models\Size;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['category', 'sizes', 'unit']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('service_name', 'like', "%{$search}%")
                  ->orWhere('service_description', 'like', "%{$search}%")
                  ->orWhereHas('category', function($categoryQuery) use ($search) {
                      $categoryQuery->where('category_name', 'like', "%{$search}%");
                  });
            });
        }

        $services = $query->latest()->paginate(15)->appends($request->query());

        return view('cashier.services.index', compact('services'));
    }

    public function create()
    {
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();
        
        return view('cashier.services.create', compact('categories', 'sizes', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'unit_id' => 'required|exists:units,unit_id',
            'price' => 'required|numeric|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,size_id',
        ]);

        $service = Service::create($validated);

        if ($request->has('sizes')) {
            $service->sizes()->attach($request->sizes);
        }

        return redirect()->route('cashier.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $service->load(['category', 'sizes', 'unit']);
        return view('cashier.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $categories = Category::all();
        $sizes = Size::all();
        $units = Unit::all();
        $service->load(['sizes']);
        
        return view('cashier.services.edit', compact('service', 'categories', 'sizes', 'units'));
    }

    public function update(Request $request, Service $service)
    {
        // Check for admin password
        if (!$this->verifyAdminPassword($request)) {
            return redirect()->back()
                ->withErrors(['admin_password' => 'Invalid admin password.'])
                ->withInput();
        }

        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'unit_id' => 'required|exists:units,unit_id',
            'price' => 'required|numeric|min:0',
            'sizes' => 'nullable|array',
            'sizes.*' => 'exists:sizes,size_id',
        ]);

        $service->update($validated);

        if ($request->has('sizes')) {
            $service->sizes()->sync($request->sizes);
        } else {
            $service->sizes()->detach();
        }

        return redirect()->route('cashier.services.index')
            ->with('success', 'Service updated successfully.');
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
