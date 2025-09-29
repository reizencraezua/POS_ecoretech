<?php

// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Root route - redirect based on admin auth state to avoid redirect loops
Route::get('/', function () {
	if (Auth::guard('admin')->check()) {
		return redirect()->route('admin.dashboard');
	}
	return redirect()->route('admin.login');
});

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\LayoutFeeController;
use App\Http\Controllers\Admin\DiscountRuleController;

// Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
	Route::middleware('guest:admin')->group(function () {
		Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
		Route::post('login', [AdminAuthController::class, 'login']);
	});

	Route::middleware('auth:admin')->group(function () {
		Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

		// Dashboard
		Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

		// Customers
		Route::resource('customers', CustomerController::class);
		Route::post('customers/{customer}/archive', [CustomerController::class, 'archive'])->name('customers.archive');
		Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])->name('customers.restore');

		// Suppliers
		Route::resource('suppliers', SupplierController::class);
		Route::post('suppliers/{supplier}/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
		Route::post('suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

		// Quotations
		Route::resource('quotations', QuotationController::class);
		Route::patch('quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');
		Route::post('quotations/{quotation}/archive', [QuotationController::class, 'archive'])->name('quotations.archive');
		Route::post('quotations/{quotation}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');

		// Orders (Job Orders)
		Route::resource('orders', OrderController::class);
		Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
		Route::delete('orders/{order}/archive', [OrderController::class, 'archive'])->name('orders.archive');
		Route::patch('orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');

		// Products & Services
		Route::resource('products', ProductController::class);
		Route::post('products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
		Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
		
		Route::resource('services', ServiceController::class);
		Route::post('services/{service}/archive', [ServiceController::class, 'archive'])->name('services.archive');
		Route::post('services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore');

		// Categories, Sizes, Units, Layout Fees
		Route::resource('categories', CategoryController::class);
		Route::post('categories/{category}/archive', [CategoryController::class, 'archive'])->name('categories.archive');
		Route::post('categories/{category}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
		Route::resource('sizes', SizeController::class);
		Route::post('sizes/{size}/archive', [SizeController::class, 'archive'])->name('sizes.archive');
		Route::post('sizes/{size}/restore', [SizeController::class, 'restore'])->name('sizes.restore');
		Route::resource('units', UnitController::class);
		Route::post('units/{unit}/archive', [UnitController::class, 'archive'])->name('units.archive');
		Route::post('units/{unit}/restore', [UnitController::class, 'restore'])->name('units.restore');
		Route::resource('layout-fees', LayoutFeeController::class);
		Route::patch('layout-fees/{layoutFee}/activate', [LayoutFeeController::class, 'activate'])->name('layout-fees.activate');

		// Discount Rules
		Route::resource('discount-rules', DiscountRuleController::class);
		Route::post('discount-rules/{discountRule}/toggle-status', [DiscountRuleController::class, 'toggleStatus'])->name('discount-rules.toggle-status');
		Route::post('discount-rules/{discountRule}/archive', [DiscountRuleController::class, 'archive'])->name('discount-rules.archive');
		Route::post('discount-rules/{discountRule}/restore', [DiscountRuleController::class, 'restore'])->name('discount-rules.restore');

		// Employees
		Route::resource('employees', EmployeeController::class);
		Route::post('employees/{employee}/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
		Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
		
		// Jobs
		Route::resource('jobs', AdminJobController::class);

		// Payments
		Route::resource('payments', PaymentController::class);
		Route::post('payments/{payment}/archive', [PaymentController::class, 'archive'])->name('payments.archive');
		Route::post('payments/{payment}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
		Route::get('orders/{order}/payments', [PaymentController::class, 'orderPayments'])->name('orders.payments');

		// Delivery
		Route::resource('deliveries', DeliveryController::class);

		// Reports
		Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
		Route::get('reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
		Route::get('reports/income', [ReportController::class, 'incomeStatement'])->name('reports.income');
		Route::get('reports/aging', [ReportController::class, 'agingReport'])->name('reports.aging');
	});
});