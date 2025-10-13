<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Customer Search API
Route::get('/customers/search', function (Request $request) {
    $query = $request->get('q');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $customers = Customer::where(function($q) use ($query) {
        $q->where('customer_firstname', 'like', "%{$query}%")
          ->orWhere('customer_lastname', 'like', "%{$query}%")
          ->orWhere('contact_number1', 'like', "%{$query}%");
    })
    ->where('is_active', true)
    ->limit(10)
    ->get();
    
    return response()->json($customers);
});

// Categories API
Route::get('/categories', function () {
    return Category::where('is_active', true)
        ->orderBy('category_name')
        ->get();
});

// Items Search API (Products and Services)
Route::get('/items/search', function (Request $request) {
    $query = $request->get('q');
    $category = $request->get('category');
    $type = $request->get('type', 'all');
    
    if (strlen($query) < 2) {
        return response()->json([]);
    }
    
    $items = collect();
    
    // Search Products
    if ($type === 'all' || $type === 'products') {
        $products = Product::where(function($q) use ($query) {
            $q->where('product_name', 'like', "%{$query}%")
              ->orWhere('product_description', 'like', "%{$query}%");
        })
        ->where('is_active', true);
        
        if ($category) {
            $products->where('category_id', $category);
        }
        
        $products = $products->with('category')->get()->map(function($product) {
            return [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'description' => $product->product_description,
                'price' => $product->base_price,
                'type' => 'product',
                'category' => $product->category ? $product->category->category_name : null
            ];
        });
        
        $items = $items->merge($products);
    }
    
    // Search Services
    if ($type === 'all' || $type === 'services') {
        $services = Service::where(function($q) use ($query) {
            $q->where('service_name', 'like', "%{$query}%")
              ->orWhere('service_description', 'like', "%{$query}%");
        })
        ->where('is_active', true);
        
        if ($category) {
            $services->where('category_id', $category);
        }
        
        $services = $services->with('category')->get()->map(function($service) {
            return [
                'id' => $service->service_id,
                'name' => $service->service_name,
                'description' => $service->service_description,
                'price' => $service->base_fee,
                'type' => 'service',
                'category' => $service->category ? $service->category->category_name : null
            ];
        });
        
        $items = $items->merge($services);
    }
    
    return response()->json($items->take(20));
});
