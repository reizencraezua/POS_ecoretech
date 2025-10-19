<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Log;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LaravelLog;

class PaymentController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $showArchived = $request->boolean('archived');
        $query = $showArchived
            ? Payment::onlyTrashed()->with(['order.customer'])
            : Payment::with(['order.customer'])->whereHas('order', function($q) {
                $q->where('order_status', '!=', \App\Models\Order::STATUS_VOIDED);
            });


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

        $payments = $query->orderBy('payment_id', 'desc')->paginate(15);

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
                'amount_paid' => 'required|numeric|min:0.01|max:999999.99',
                'payment_method' => 'required|in:Cash,GCash,Bank Transfer,Check,Credit Card',
                'payment_term' => 'nullable|in:Downpayment,Initial,Partial,Full',
                'reference_number' => 'nullable|string|max:255',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'order_id.required' => 'Please select an order.',
                'order_id.exists' => 'Selected order does not exist.',
                'payment_date.required' => 'Payment date is required.',
                'payment_date.date' => 'Please enter a valid payment date.',
                'amount_paid.required' => 'Amount paid is required.',
                'amount_paid.numeric' => 'Amount paid must be a number.',
                'amount_paid.min' => 'Amount paid must be at least ₱0.01.',
                'amount_paid.max' => 'Amount paid cannot exceed ₱999,999.99.',
                'payment_method.required' => 'Payment method is required.',
                'payment_method.in' => 'Invalid payment method selected.',
                'payment_term.in' => 'Invalid payment term selected.',
                'reference_number.max' => 'Reference number cannot exceed 255 characters.',
                'remarks.max' => 'Remarks cannot exceed 1000 characters.',
            ]);

            DB::beginTransaction();

            // Get order for validation (including soft-deleted to check if it exists)
            $order = Order::withTrashed()->findOrFail($validated['order_id']);
            
            // Check if order is soft-deleted
            if ($order->trashed()) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['order_id' => 'Cannot create payment for a deleted order.'])
                    ->withInput();
            }
            
            // Validate payment amount against remaining balance
            $totalPaid = $order->payments->sum('amount_paid') + $validated['amount_paid'];
            if ($totalPaid > $order->final_total_amount) {
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['amount_paid' => 'Payment amount cannot exceed the total amount of ₱' . number_format($order->final_total_amount, 2) . '.'])
                    ->withInput();
            }

            // Calculate balance and change
            $remaining = max(0, $order->final_total_amount - $totalPaid);
            $change = $totalPaid > $order->final_total_amount ? ($totalPaid - $order->final_total_amount) : 0;

            Payment::create([
                'payment_id' => Payment::generatePaymentId(),
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
                'created_by' => auth('web')->id() ?? \App\Models\User::first()->id,
                'received_by' => auth('web')->id() ?? \App\Models\User::first()->id,
            ]);

            DB::commit();

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

            // Log the creation
            $this->logCreated(
                Log::TYPE_PAYMENT,
                $payment->payment_id,
                $this->generateTransactionName(Log::TYPE_PAYMENT, $payment->payment_id),
                $payment->toArray()
            );

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment recorded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            LaravelLog::error('Payment validation error: ' . json_encode($e->errors()));
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            LaravelLog::error('Error creating payment: ' . $e->getMessage());
            LaravelLog::error('Stack trace: ' . $e->getTraceAsString());
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while processing the payment.',
                    'error' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Failed to create payment: ' . $e->getMessage())
                ->withInput();
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
        // Store original data for logging
        $originalData = $payment->toArray();
        
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
        
        // Note: Downpayment validation removed to allow flexible payment updates

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

        // Log the update
        $this->logUpdated(
            Log::TYPE_PAYMENT,
            $payment->payment_id,
            $originalData,
            $payment->fresh()->toArray(),
            $this->generateTransactionName(Log::TYPE_PAYMENT, $payment->payment_id)
        );

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        // Log the deletion
        $this->logDeleted(
            Log::TYPE_PAYMENT,
            $payment->payment_id,
            $this->generateTransactionName(Log::TYPE_PAYMENT, $payment->payment_id),
            $payment->toArray()
        );

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

    public function restore(Payment $payment)
    {
        try {
            $payment->restore();

            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment restored successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to restore payment. Please try again.');
        }
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
     * Print payment summary
     */
    public function printSummary(Request $request)
    {
        $query = Payment::with(['order.customer']);

        // Apply filters (same logic as index method)
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

        $payments = $query->latest('payment_date')->get();

        // Calculate summary data
        $totalAmount = $payments->sum('amount_paid');
        $paymentCount = $payments->count();
        $averagePayment = $paymentCount > 0 ? $totalAmount / $paymentCount : 0;

        // Payment methods breakdown
        $paymentMethods = $payments->groupBy('payment_method')->map(function ($group, $method) use ($totalAmount) {
            $amount = $group->sum('amount_paid');
            $count = $group->count();
            $percentage = $totalAmount > 0 ? round(($amount / $totalAmount) * 100, 1) : 0;
            
            return [
                'method' => $method,
                'amount' => $amount,
                'count' => $count,
                'percentage' => $percentage
            ];
        })->values();

        // Filter information
        $filters = [];
        if ($request->date_range) {
            $filters['Date Range'] = ucfirst(str_replace('_', ' ', $request->date_range));
        }
        if ($request->start_date) $filters['Start Date'] = $request->start_date;
        if ($request->end_date) $filters['End Date'] = $request->end_date;
        if ($request->payment_method) $filters['Payment Method'] = $request->payment_method;
        if ($request->payment_status) $filters['Payment Status'] = ucfirst($request->payment_status);

        return view('components.print-summary', compact('payments', 'totalAmount', 'paymentCount', 'averagePayment', 'paymentMethods', 'filters'));
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
