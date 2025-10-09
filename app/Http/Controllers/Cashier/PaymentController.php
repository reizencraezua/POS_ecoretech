<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user is cashier or admin
        $user = auth('web')->user();
        if (!$user || (!$user->isCashier() && !$user->isAdmin())) {
            abort(403, 'Access denied. Cashier or Admin role required.');
        }

        $query = Payment::with(['order.customer']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_id', 'like', "%{$search}%")
                  ->orWhereHas('order.customer', function($customerQuery) use ($search) {
                      $customerQuery->where('customer_firstname', 'like', "%{$search}%")
                                   ->orWhere('customer_lastname', 'like', "%{$search}%")
                                   ->orWhere('business_name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range filters
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }

        // Payment method filter
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest('payment_date')->paginate(15)->appends($request->query());

        return view('cashier.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::whereNotIn('order_status', ['Cancelled'])
            ->with('customer', 'payments')
            ->get()
            ->filter(function($order) {
                return $order->remaining_balance > 0;
            });

        return view('cashier.payments.create', compact('orders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'payment_method' => 'required|in:Cash,Check,GCash,PayMaya,Bank Transfer',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_date' => 'required|date',
                'payment_reference' => 'nullable|string|max:100',
                'payment_notes' => 'nullable|string|max:500',
            ]);

            // Check if payment amount exceeds remaining balance
            $order = Order::findOrFail($validated['order_id']);
            if ($validated['amount_paid'] > $order->remaining_balance) {
                return redirect()->back()
                    ->with('error', 'Payment amount cannot exceed remaining balance of ₱' . number_format($order->remaining_balance, 2))
                    ->withInput();
            }

            // Create payment
            $payment = Payment::create([
                'payment_id' => Payment::generatePaymentId(),
                'order_id' => $validated['order_id'],
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $validated['amount_paid'],
                'payment_date' => $validated['payment_date'],
                'payment_reference' => $validated['payment_reference'],
                'payment_notes' => $validated['payment_notes'],
            ]);

            // Check if order is fully paid
            if ($order->remaining_balance <= 0) {
                $order->update(['order_status' => 'For Releasing']);
            }

            return redirect()->route('cashier.payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating payment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to record payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['order.customer', 'order.details.product', 'order.details.service']);
        return view('cashier.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $orders = Order::whereNotIn('order_status', ['Cancelled'])
            ->with('customer', 'payments')
            ->get()
            ->filter(function($order) {
                return $order->remaining_balance > 0 || $order->payments->contains('id', $payment->id);
            });

        return view('cashier.payments.edit', compact('payment', 'orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        // Check for admin password
        if (!$this->verifyAdminPassword($request)) {
            return redirect()->back()
                ->withErrors(['admin_password' => 'Invalid admin password.'])
                ->withInput();
        }

        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'payment_method' => 'required|in:Cash,Check,GCash,PayMaya,Bank Transfer',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_date' => 'required|date',
                'payment_reference' => 'nullable|string|max:100',
                'payment_notes' => 'nullable|string|max:500',
            ]);

            // Check if payment amount exceeds remaining balance
            $order = Order::findOrFail($validated['order_id']);
            $totalPayments = $order->payments->sum('amount_paid');
            $currentPaymentAmount = $payment->amount_paid;
            $existingPaymentsExcludingCurrent = $totalPayments - $currentPaymentAmount;
            $remainingBalance = $order->final_total_amount - $existingPaymentsExcludingCurrent;
            
            if ($validated['amount_paid'] > $remainingBalance) {
                return redirect()->back()
                    ->with('error', 'Payment amount cannot exceed remaining balance of ₱' . number_format($remainingBalance, 2))
                    ->withInput();
            }

            // Update payment
            $payment->update([
                'order_id' => $validated['order_id'],
                'payment_method' => $validated['payment_method'],
                'amount_paid' => $validated['amount_paid'],
                'payment_date' => $validated['payment_date'],
                'payment_reference' => $validated['payment_reference'],
                'payment_notes' => $validated['payment_notes'],
            ]);

            // Check if order is fully paid
            if ($order->remaining_balance <= 0) {
                $order->update(['order_status' => 'For Releasing']);
            }

            return redirect()->route('cashier.payments.index')
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating payment: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update payment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Print receipt for a payment
     */
    public function print(Payment $payment)
    {
        $payment->load(['order.customer', 'order.details.product', 'order.details.service']);
        return view('cashier.payments.print', compact('payment'));
    }

    /**
     * Get payment summary data
     */
    public function summary(Request $request)
    {
        $query = Payment::with(['order.customer']);

        // Apply same filters as index method
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('payment_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('payment_date', '<=', $request->end_date);
        }
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->get();

        // Calculate summary data
        $totalAmount = $payments->sum('amount_paid');
        $paymentCount = $payments->count();
        $averagePayment = $paymentCount > 0 ? $totalAmount / $paymentCount : 0;

        // Payment methods breakdown
        $paymentMethods = $payments->groupBy('payment_method')->map(function ($group, $method) use ($totalAmount) {
            $amount = $group->sum('amount_paid');
            $count = $group->count();
            $percentage = $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0;
            
            // Assign colors for each payment method
            $colors = [
                'Cash' => '#10B981',
                'Check' => '#F59E0B',
                'GCash' => '#3B82F6',
                'PayMaya' => '#EC4899',
                'Bank Transfer' => '#8B5CF6'
            ];
            
            return [
                'method' => $method,
                'amount' => $amount,
                'count' => $count,
                'percentage' => $percentage,
                'color' => $colors[$method] ?? '#6B7280'
            ];
        })->values();

        return response()->json([
            'total_amount' => $totalAmount,
            'payment_count' => $paymentCount,
            'average_payment' => $averagePayment,
            'payment_methods' => $paymentMethods
        ]);
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

        return \Hash::check($adminPassword, $admin->password);
    }
}
