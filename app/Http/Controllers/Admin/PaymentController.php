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

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('order.customer', function ($q) use ($search) {
                $q->where('customer_firstname', 'like', "%{$search}%")
                    ->orWhere('customer_lastname', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
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

            // Get order for validation
            $order = Order::findOrFail($validated['order_id']);
            
            // Validate downpayment amount (must be exactly 50% of total amount)
            if ($validated['payment_term'] === 'Downpayment') {
                $expectedDownpayment = $order->total_amount * 0.5;
                $tolerance = 0.01; // Allow 1 cent tolerance for rounding
                
                if (abs($validated['amount_paid'] - $expectedDownpayment) > $tolerance) {
                    return redirect()->back()
                        ->withErrors(['amount_paid' => "Downpayment must be exactly 50% of the total amount (₱" . number_format($expectedDownpayment, 2) . ")"])
                        ->withInput();
                }
            }

            // Calculate balance and change
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
            'reference_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        // Get order for validation
        $order = Order::findOrFail($validated['order_id']);
        
        // Validate downpayment amount (must be exactly 50% of total amount)
        if ($validated['payment_term'] === 'Downpayment') {
            $expectedDownpayment = $order->total_amount * 0.5;
            $tolerance = 0.01; // Allow 1 cent tolerance for rounding
            
            if (abs($validated['amount_paid'] - $expectedDownpayment) > $tolerance) {
                return redirect()->back()
                    ->withErrors(['amount_paid' => "Downpayment must be exactly 50% of the total amount (₱" . number_format($expectedDownpayment, 2) . ")"])
                    ->withInput();
            }
        }

        // Calculate balance and change
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
}
