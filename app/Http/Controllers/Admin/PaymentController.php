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
        $query = Payment::with(['order.customer']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order.customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $payments = $query->latest('payment_date')->paginate(15);

        return view('admin.payments.index', compact('payments'));
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
                'remarks' => 'nullable|string',
            ]);

            // Calculate balance and change
            $order = Order::findOrFail($validated['order_id']);
            $totalPaid = $order->payments->sum('amount_paid') + $validated['amount_paid'];
            $remaining = max(0, $order->total_amount - $totalPaid);
            $change = $totalPaid > $order->total_amount ? ($totalPaid - $order->total_amount) : 0;

            Payment::create([
                'receipt_number' => 'RCPT-' . now()->format('YmdHis') . '-' . $order->order_id,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'payment_term' => $validated['payment_term'] ?? 'Initial',
                'amount_paid' => $validated['amount_paid'],
                'change' => $change,
                'balance' => $remaining,
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
        $orders = Order::with('customer')->where('order_status', '!=', 'Cancelled')->get();
        return view('admin.payments.edit', compact('payment', 'orders'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,GCash,Bank Transfer,Check,Credit Card',
            'payment_term' => 'nullable|in:Downpayment,Initial,Full',
            'remarks' => 'nullable|string',
        ]);

        // Calculate balance and change
        $order = Order::findOrFail($validated['order_id']);
        $totalPaid = $order->payments->where('id', '!=', $payment->id)->sum('amount_paid') + $validated['amount_paid'];
        $remaining = max(0, $order->total_amount - $totalPaid);
        $change = $totalPaid > $order->total_amount ? ($totalPaid - $order->total_amount) : 0;

        $payment->update([
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_term' => $validated['payment_term'] ?? 'Initial',
            'amount_paid' => $validated['amount_paid'],
            'change' => $change,
            'balance' => $remaining,
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    public function orderPayments(Order $order)
    {
        $payments = $order->payments()->latest('payment_date')->get();
        return view('admin.payments.order', compact('order', 'payments'));
    }
}
