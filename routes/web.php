<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\LayoutFeeController;
use App\Http\Controllers\Admin\DiscountRuleController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\SupplierController;

// Cashier Controllers
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\QuotationController as CashierQuotationController;
use App\Http\Controllers\Cashier\JobOrderController as CashierJobOrderController;
use App\Http\Controllers\Cashier\DeliveryController as CashierDeliveryController;
use App\Http\Controllers\Cashier\PaymentController as CashierPaymentController;
use App\Http\Controllers\Cashier\CustomerController as CashierCustomerController;
use App\Http\Controllers\Cashier\ProductController as CashierProductController;
use App\Http\Controllers\Cashier\ServiceController as CashierServiceController;

// Authentication Routes
Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AdminAuthController::class, 'login']);

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
	Route::get('login', function () {
		return redirect()->route('login');
	})->name('login');

	Route::middleware(['auth:web', 'role:admin'])->group(function () {
		Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

		// Dashboard
		Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

		// Customers
		Route::resource('customers', CustomerController::class);
		Route::post('customers/{customer}/archive', [CustomerController::class, 'archive'])->name('customers.archive');
		Route::post('customers/{customer}/restore', [CustomerController::class, 'restore'])->name('customers.restore');

		// Employees
		Route::resource('employees', EmployeeController::class);
		Route::post('employees/{employee}/archive', [EmployeeController::class, 'archive'])->name('employees.archive');
		Route::post('employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');

		// Jobs
		Route::resource('jobs', JobController::class);
		Route::post('jobs/{job}/archive', [JobController::class, 'archive'])->name('jobs.archive');
		Route::post('jobs/{job}/restore', [JobController::class, 'restore'])->name('jobs.restore');

		// Quotations
		Route::resource('quotations', QuotationController::class);
		Route::post('quotations/{quotation}/archive', [QuotationController::class, 'archive'])->name('quotations.archive');
		Route::post('quotations/{quotation}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');
		Route::patch('quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.status');
		Route::get('quotations/{quotation}/data', [QuotationController::class, 'getData'])->name('quotations.data');
		Route::post('quotations/convert-to-job', [QuotationController::class, 'convertToJob'])->name('quotations.convert-to-job');

		// Orders
		Route::resource('orders', OrderController::class);
		Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
		Route::post('orders/{order}/archive', [OrderController::class, 'archive'])->name('orders.archive');
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
		Route::get('categories/sizes/by-group', [CategoryController::class, 'getSizesByGroup'])->name('categories.sizes.by-group');
		Route::resource('sizes', SizeController::class);
		Route::post('sizes/{size}/archive', [SizeController::class, 'archive'])->name('sizes.archive');
		Route::post('sizes/{size}/restore', [SizeController::class, 'restore'])->name('sizes.restore');
		Route::resource('units', UnitController::class);
		Route::post('units/{unit}/archive', [UnitController::class, 'archive'])->name('units.archive');
		Route::post('units/{unit}/restore', [UnitController::class, 'restore'])->name('units.restore');
		Route::resource('layout-fees', LayoutFeeController::class);
		Route::patch('layout-fees/{layoutFee}/activate', [LayoutFeeController::class, 'activate'])->name('layout-fees.activate');
		Route::post('layout-fees/{layoutFee}/archive', [LayoutFeeController::class, 'archive'])->name('layout-fees.archive');
		Route::post('layout-fees/{layoutFee}/restore', [LayoutFeeController::class, 'restore'])->name('layout-fees.restore');

		// Discount Rules
		Route::resource('discount-rules', DiscountRuleController::class);
		Route::post('discount-rules/{discountRule}/archive', [DiscountRuleController::class, 'archive'])->name('discount-rules.archive');
		Route::post('discount-rules/{discountRule}/restore', [DiscountRuleController::class, 'restore'])->name('discount-rules.restore');

		// Payments
		Route::get('payments/print-summary', [PaymentController::class, 'printSummary'])->name('payments.print-summary');
		Route::resource('payments', PaymentController::class);
		Route::post('payments/{payment}/archive', [PaymentController::class, 'archive'])->name('payments.archive');
		Route::post('payments/{payment}/restore', [PaymentController::class, 'restore'])->name('payments.restore');
		Route::get('payments/{payment}/print', [PaymentController::class, 'print'])->name('payments.print');

		// Deliveries
		Route::resource('deliveries', DeliveryController::class);
		Route::post('deliveries/{delivery}/archive', [DeliveryController::class, 'archive'])->name('deliveries.archive');
		Route::post('deliveries/{delivery}/restore', [DeliveryController::class, 'restore'])->name('deliveries.restore');

		// Inventory
		Route::resource('inventories', InventoryController::class);
		Route::get('inventories/critical', [InventoryController::class, 'critical'])->name('inventories.critical');
		Route::post('inventories/{inventory}/add-stock', [InventoryController::class, 'addStock'])->name('inventories.add-stock');
		Route::post('inventories/{inventory}/use-stock', [InventoryController::class, 'useStock'])->name('inventories.use-stock');
		Route::post('inventories/{inventory}/archive', [InventoryController::class, 'archive'])->name('inventories.archive');
		Route::post('inventories/{inventory}/restore', [InventoryController::class, 'restore'])->name('inventories.restore');

		// Suppliers
		Route::resource('suppliers', SupplierController::class);
		Route::post('suppliers/{supplier}/archive', [SupplierController::class, 'archive'])->name('suppliers.archive');
		Route::post('suppliers/{supplier}/restore', [SupplierController::class, 'restore'])->name('suppliers.restore');

	});
});

// Cashier Routes (using unified authentication)
Route::prefix('cashier')->name('cashier.')->group(function () {
	Route::middleware(['auth:web', 'role:cashier'])->group(function () {
		Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

		// Dashboard
		Route::get('dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');

		// Customers
		Route::resource('customers', CashierCustomerController::class);

		// Products
		Route::resource('products', CashierProductController::class);

		// Services
		Route::resource('services', CashierServiceController::class);

		// Quotations
		Route::resource('quotations', CashierQuotationController::class);
		Route::patch('quotations/{quotation}/status', [CashierQuotationController::class, 'updateStatus'])->name('quotations.status');
		Route::get('quotations/{quotation}/data', [CashierQuotationController::class, 'getData'])->name('quotations.data');
		Route::post('quotations/convert-to-job', [CashierQuotationController::class, 'convertToJob'])->name('quotations.convert-to-job');

		// Job Orders (Orders)
		Route::resource('orders', CashierJobOrderController::class);
		Route::patch('orders/{order}/status', [CashierJobOrderController::class, 'updateStatus'])->name('orders.status');
		Route::post('orders/{order}/void', [CashierJobOrderController::class, 'void'])->name('orders.void');

		// Deliveries
		Route::resource('deliveries', CashierDeliveryController::class);

		// Payments
		Route::get('payments/print-summary', [CashierPaymentController::class, 'printSummary'])->name('payments.print-summary');
		Route::resource('payments', CashierPaymentController::class);
		Route::get('payments/{payment}/print', [CashierPaymentController::class, 'print'])->name('payments.print');
		Route::post('payments/{payment}/archive', [CashierPaymentController::class, 'archive'])->name('payments.archive');
		Route::post('payments/{id}/restore', [CashierPaymentController::class, 'restore'])->name('payments.restore');

		// Password Change
		Route::get('change-password', [CashierDashboardController::class, 'showChangePasswordForm'])->name('change-password');
		Route::post('change-password', [CashierDashboardController::class, 'changePassword'])->name('change-password');
	});
});

// API Routes
Route::prefix('api')->group(function () {
	// Customer search API
	Route::get('customers/search', [CustomerController::class, 'search'])->name('api.customers.search');
	
	// Product search API
	Route::get('products/search', [ProductController::class, 'search'])->name('api.products.search');
	
	// Service search API
	Route::get('services/search', [ServiceController::class, 'search'])->name('api.services.search');
	
	// Size by group API
	Route::get('sizes/by-group', [SizeController::class, 'getByGroup'])->name('api.sizes.by-group');
	
	// Category sizes API
	Route::get('categories/{category}/sizes', [CategoryController::class, 'getSizes'])->name('api.categories.sizes');
	
	// Order details API
	Route::get('orders/{order}/details', [OrderController::class, 'getDetails'])->name('api.orders.details');
	
	// Quotation details API
	Route::get('quotations/{quotation}/details', [QuotationController::class, 'getDetails'])->name('api.quotations.details');
	
	// Payment details API
	Route::get('payments/{payment}/details', [PaymentController::class, 'getDetails'])->name('api.payments.details');
	
	// Delivery details API
	Route::get('deliveries/{delivery}/details', [DeliveryController::class, 'getDetails'])->name('api.deliveries.details');
	
	// Inventory search API
	Route::get('inventories/search', [InventoryController::class, 'search'])->name('api.inventories.search');
	
	
	// Employee search API
	Route::get('employees/search', [EmployeeController::class, 'search'])->name('api.employees.search');
	
	// Job position search API
	Route::get('job-positions/search', [JobController::class, 'search'])->name('api.job-positions.search');
	
	// Unit search API
	Route::get('units/search', [UnitController::class, 'search'])->name('api.units.search');
	
	// Supplier search API
	Route::get('suppliers/search', [InventoryController::class, 'searchSuppliers'])->name('api.suppliers.search');
	
	// Layout fee search API
	Route::get('layout-fees/search', [LayoutFeeController::class, 'search'])->name('api.layout-fees.search');
	
	// Discount rule search API
	Route::get('discount-rules/search', [DiscountRuleController::class, 'search'])->name('api.discount-rules.search');
});

// Test route to check if the application is working
Route::get('/test', function () {
    return response()->json(['status' => 'OK', 'message' => 'Application is working']);
});

// Debug route to check authentication
Route::get('/debug-auth', function () {
    $user = auth()->user();
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => $user ? [
            'email' => $user->email,
            'role' => $user->role,
            'is_admin' => $user->isAdmin(),
            'is_cashier' => $user->isCashier(),
        ] : null
    ]);
});

// Debug route to test admin dashboard access
Route::get('/debug-admin-dashboard', function () {
    return response()->json([
        'route_name' => 'admin.dashboard',
        'route_url' => route('admin.dashboard'),
        'authenticated' => auth()->check(),
        'user_role' => auth()->user()?->role,
    ]);
});

// Default route
Route::get('/', function () {
	return redirect()->route('login');
});