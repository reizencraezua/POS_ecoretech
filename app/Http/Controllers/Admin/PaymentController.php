<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Payment::onlyTrashed()->with(['order.customer'])
            : Payment::with(['order.customer']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('order.customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        // Date range filters
        if ($request->has('date_range') && $request->date_range) {
            $dateRange = $request->date_range;
            $today = now();
            
            switch ($dateRange) {
                case 'today':
                    $query->whereDate('payment_date', $today->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('payment_date', $today->subDay()->toDateString());
                    break;
                case 'last_7_days':
                    $query->whereDate('payment_date', '>=', $today->subDays(7)->toDateString());
                    break;
                case 'last_30_days':
                    $query->whereDate('payment_date', '>=', $today->subDays(30)->toDateString());
                    break;
                case 'last_3_months':
                    $query->whereDate('payment_date', '>=', $today->subMonths(3)->toDateString());
                    break;
                case 'this_year':
                    $query->whereYear('payment_date', $today->year);
                    break;
            }
        } else {
            // Fallback to custom date range
            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('payment_date', '>=', $request->start_date);
            }
            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('payment_date', '<=', $request->end_date);
            }
        }

        // Payment method filter
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Payment status filter
        if ($request->has('payment_status') && $request->payment_status) {
            if ($request->payment_status === 'complete') {
                $query->where('balance', 0);
            } elseif ($request->payment_status === 'partial') {
                $query->where('balance', '>', 0);
            }
        }

        $payments = $query->latest('payment_date')->paginate(15)->appends($request->query());

        return view('admin.payments.index', compact('payments', 'showArchived'));
    }

    public function create()
    {
        $orders = Order::with('customer')->where('order_status', '!=', 'Cancelled')->get();
        return view('admin.payments.create', compact('orders'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'payment_date' => 'required|date',
                'amount_paid' => 'required|numeric|min:0.01',
                'payment_method' => 'required|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment_term' => 'nullable|in:Downpayment,Initial,Partial,Full',
                'reference_number' => 'nullable|string|max:255',
                'remarks' => 'nullable|string',
            ]);

            // Get order for validation (including soft-deleted to check if it exists)
            $order = Order::withTrashed()->findOrFail($validated['order_id']);
            
            // Check if order is soft-deleted
            if ($order->trashed()) {
                return redirect()->back()
                    ->withErrors(['order_id' => 'Cannot create payment for a deleted order.'])
                    ->withInput();
            }
            
            // Validate downpayment amount (must be exactly 50% of total amount)
            if ($validated['payment_term'] === 'Downpayment') {
                $expectedDownpayment = $order->final_total_amount * 0.5;
                $tolerance = 0.01; // Allow 1 cent tolerance for rounding
                
                if (abs($validated['amount_paid'] - $expectedDownpayment) > $tolerance) {
                    return redirect()->back()
                        ->withErrors(['amount_paid' => "Downpayment must be exactly 50% of the total amount (₱" . number_format($expectedDownpayment, 2) . ")"])
                        ->withInput();
                }
            }

            // Calculate balance and change
            $totalPaid = $order->payments->sum('amount_paid') + $validated['amount_paid'];
            $remaining = max(0, $order->final_total_amount - $totalPaid);
            $change = $totalPaid > $order->final_total_amount ? ($totalPaid - $order->final_total_amount) : 0;

            Payment::create([
                'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'payment_term' => $validated['payment_term'] ?? 'Initial',
                'amount_paid' => $validated['amount_paid'],
                'change' => $change,
                'balance' => $remaining,
                'reference_number' => $validated['reference_number'],
                'remarks' => $validated['remarks'],
                'order_id' => $validated['order_id'],
            ]);

            // Check if this is an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment recorded successfully.',
                    'data' => [
                        'remaining_balance' => $remaining,
                        'total_paid' => $totalPaid,
                        'change' => $change
                    ]
                ]);
            }

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the payment.',
                    'error' => $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function show(Payment $payment)
    {
        $payment->load(['order.customer']);
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $orders = Order::with(['customer', 'payments'])->where('order_status', '!=', 'Cancelled')->get();
        
        // Calculate existing payments for each order (including current payment for display)
        $orderPayments = [];
        foreach ($orders as $order) {
            $totalPayments = $order->payments->sum('amount_paid');
            $orderPayments[$order->order_id] = $totalPayments;
        }
        
        return view('admin.payments.edit', compact('payment', 'orders', 'orderPayments'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,GCash,Bank Transfer,Check,Credit Card',
            'payment_term' => 'nullable|in:Downpayment,Initial,Full',
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Get order for validation (including soft-deleted to check if it exists)
        $order = Order::withTrashed()->findOrFail($validated['order_id']);
        
        // Check if order is soft-deleted
        if ($order->trashed()) {
            return redirect()->back()
                ->withErrors(['order_id' => 'Cannot update payment for a deleted order.'])
                ->withInput();
        }
        
        // Validate that payment amount doesn't exceed remaining balance
        $totalPayments = $order->payments->sum('amount_paid');
        $currentPaymentAmount = $payment->amount_paid;
        $existingPaymentsExcludingCurrent = $totalPayments - $currentPaymentAmount;
        $remainingBalance = $order->final_total_amount - $existingPaymentsExcludingCurrent;
        
        if ($validated['amount_paid'] > $remainingBalance) {
            return redirect()->back()
                ->withErrors(['amount_paid' => "Payment amount cannot exceed remaining balance of ₱" . number_format($remainingBalance, 2)])
                ->withInput();
        }
        
        // Validate downpayment amount (must be exactly 50% of total amount)
        if ($validated['payment_term'] === 'Downpayment') {
            $expectedDownpayment = $order->final_total_amount * 0.5;
            $tolerance = 0.01; // Allow 1 cent tolerance for rounding
            
            if (abs($validated['amount_paid'] - $expectedDownpayment) > $tolerance) {
                return redirect()->back()
                    ->withErrors(['amount_paid' => "Downpayment must be exactly 50% of the total amount (₱" . number_format($expectedDownpayment, 2) . ")"])
                    ->withInput();
            }
        }

        // Calculate balance and change
        $totalPaid = $order->payments->where('id', '!=', $payment->id)->sum('amount_paid') + $validated['amount_paid'];
        $remaining = max(0, $order->final_total_amount - $totalPaid);
        $change = $totalPaid > $order->final_total_amount ? ($totalPaid - $order->final_total_amount) : 0;

        $payment->update([
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_term' => $validated['payment_term'] ?? 'Initial',
            'amount_paid' => $validated['amount_paid'],
            'change' => $change,
            'balance' => $remaining,
            'reference_number' => $validated['reference_number'],
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment archived successfully.');
    }

    public function archive(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment archived successfully.');
    }

    public function restore($paymentId)
    {
        $payment = Payment::withTrashed()->findOrFail($paymentId);
        $payment->restore();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment restored successfully.');
    }

    public function orderPayments(Order $order)
    {
        $payments = $order->payments()->latest('payment_date')->get();
        return view('admin.payments.order', compact('order', 'payments'));
    }

    /**
     * Print receipt for a payment
     */
    public function print(Payment $payment)
    {
        $payment->load(['order.customer', 'order.details.product', 'order.details.service']);
        return view('admin.payments.print', compact('payment'));
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
        if ($request->has('payment_status') && $request->payment_status) {
            if ($request->payment_status === 'complete') {
                $query->where('balance', 0);
            } elseif ($request->payment_status === 'partial') {
                $query->where('balance', '>', 0);
            }
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
                'GCash' => '#3B82F6',
                'Bank Transfer' => '#8B5CF6',
                'Check' => '#F59E0B',
                'Credit Card' => '#6B7280'
            ];
            
            return [
                'method' => $method,
                'amount' => $amount,
                'count' => $count,
                'percentage' => $percentage,
                'color' => $colors[$method] ?? '#6B7280'
            ];
        })->values();

        // Payment status counts
        $completePayments = $payments->where('balance', 0)->count();
        $partialPayments = $payments->where('balance', '>', 0)->count();

        return response()->json([
            'total_amount' => $totalAmount,
            'payment_count' => $paymentCount,
            'average_payment' => $averagePayment,
            'payment_methods' => $paymentMethods,
            'complete_payments' => $completePayments,
            'partial_payments' => $partialPayments
        ]);
    }
}
