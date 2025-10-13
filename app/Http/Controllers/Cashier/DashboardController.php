<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Payment;
use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        // Allow all authenticated users to access dashboard

        $startDateParam = $request->query('start_date');
        $endDateParam = $request->query('end_date');
        $startDate = $startDateParam ? Carbon::parse($startDateParam)->startOfDay() : null;
        $endDate = $endDateParam ? Carbon::parse($endDateParam)->endOfDay() : null;

        $stats = [
            'total_quotations' => Quotation::count(),
            'pending_quotations' => Quotation::where('status', Quotation::STATUS_PENDING)->count(),
            'due_orders_today' => Order::where('deadline_date', now()->toDateString())->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])->count(),
            'due_orders_soon' => Order::whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])->count(),
            'total_orders' => Order::count(),
            'active_orders' => Order::whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])->count(),
            'total_deliveries' => Delivery::count(),
            'pending_deliveries' => Delivery::where('status', '!=', 'Delivered')->count(),
        ];

        // Recent quotations
        $recent_quotations = Quotation::with(['customer'])
            ->orderBy('quotation_date', 'desc')
            ->take(5)
            ->get();

        // Recent orders
        $recent_orders = Order::with(['customer', 'employee'])
            ->orderBy('order_date', 'desc')
            ->take(5)
            ->get();

        // Recent deliveries
        $recent_deliveries = Delivery::with(['order.customer'])
            ->orderBy('delivery_date', 'desc')
            ->take(5)
            ->get();

            // Orders with pending payments
            $pending_payments = Order::with(['customer', 'payments'])
                ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
                ->get()
                ->filter(function($order) {
                    return $order->remaining_balance > 0;
                })
                ->take(5);

            // Orders due in 1-3 days
            $due_orders = Order::with(['customer', 'employee'])
                ->whereNotNull('deadline_date')
                ->whereNotIn('order_status', [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED])
                ->whereBetween('deadline_date', [now()->addDay(), now()->addDays(3)])
                ->orderBy('deadline_date', 'asc')
                ->get();

            return view('cashier.dashboard', compact(
                'stats',
                'recent_quotations',
                'recent_orders',
                'recent_deliveries',
                'pending_payments',
                'due_orders'
            ));
    }

    /**
     * Show the password change form
     */
    public function showChangePasswordForm()
    {
        return view('cashier.change-password');
    }

    /**
     * Handle password change request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        // Check if new password is different from current password
        if (Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'New password must be different from your current password.'])
                ->withInput();
        }

        // Additional security checks
        $password = $request->password;
        $userInfo = strtolower($user->name . ' ' . $user->email);
        
        // Check for personal information in password
        $personalInfo = [
            strtolower($user->name),
            strtolower(explode('@', $user->email)[0]),
        ];
        
        foreach ($personalInfo as $info) {
            if (strlen($info) > 3 && strpos(strtolower($password), $info) !== false) {
                return redirect()->back()
                    ->withErrors(['password' => 'Password should not contain your name or email address.'])
                    ->withInput();
            }
        }

        // Check for common weak patterns
        $weakPatterns = [
            'password', '123456', 'qwerty', 'abc123', 'admin', 'user',
            'welcome', 'login', 'pass', 'secret', 'test'
        ];
        
        foreach ($weakPatterns as $pattern) {
            if (strpos(strtolower($password), $pattern) !== false) {
                return redirect()->back()
                    ->withErrors(['password' => 'Password contains common weak patterns. Please choose a stronger password.'])
                    ->withInput();
            }
        }

        // Check for sequential characters
        if (preg_match('/(.)\1{2,}/', $password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Password should not contain repeated characters (e.g., aaa, 111).'])
                ->withInput();
        }

        // Check for sequential numbers or letters
        if (preg_match('/(012|123|234|345|456|567|678|789|890|abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz)/i', $password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Password should not contain sequential characters (e.g., 123, abc).'])
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Password changed successfully! Your new password meets all security requirements.');
    }
}
