<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Quotation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the default timezone to Philippines
        date_default_timezone_set('Asia/Manila');
        
        // Configure route model binding for models with custom primary keys
        Route::bind('payment', function ($value) {
            return Payment::withTrashed()->where('payment_id', $value)->firstOrFail();
        });
        
        Route::bind('delivery', function ($value) {
            return Delivery::withTrashed()->where('delivery_id', $value)->firstOrFail();
        });
        
        Route::bind('order', function ($value) {
            return Order::withTrashed()->where('order_id', $value)->firstOrFail();
        });
        
        Route::bind('quotation', function ($value) {
            return Quotation::withTrashed()->where('quotation_id', $value)->firstOrFail();
        });
    }
}
