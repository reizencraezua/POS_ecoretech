@extends('layouts.admin')

@section('title', 'Edit Payment')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Edit Payment</h1>
            <p class="text-gray-600 mt-2">Update payment information</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('admin.payments.update', $payment) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Order Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Order Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                            <select name="order_id" id="order_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_id') border-red-500 @enderror">
                                <option value="">Select order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->order_id }}" {{ old('order_id', $payment->order_id) == $order->order_id ? 'selected' : '' }}>
                                        Order #{{ $order->order_id }} - {{ $order->customer->customer_firstname }} {{ $order->customer->customer_lastname }}
                                        (₱{{ number_format($order->final_total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('order_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date *</label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_date') border-red-500 @enderror">
                            @error('payment_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Payment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                            <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid', $payment->amount_paid) }}" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('amount_paid') border-red-500 @enderror">
                            
                            <!-- Maximum Payment Information -->
                            <div id="max_payment_info" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-md text-sm">
                                <div class="flex items-center text-green-700 mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span class="font-medium">Payment Information</span>
                                </div>
                                <div class="text-green-800">
                                    <div class="flex justify-between items-center">
                                        <span>Order Total Amount:</span>
                                        <span id="order_total_display" class="font-semibold">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Already Paid:</span>
                                        <span id="already_paid_display" class="font-semibold">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center border-t border-green-300 pt-2 mt-2">
                                        <span class="font-medium">Maximum Payment:</span>
                                        <span id="max_payment_display" class="font-bold text-lg text-green-700">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="downpayment_info" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm" style="display: none;">
                                <div class="flex items-center text-blue-700 mb-2">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span class="font-medium">Downpayment Information</span>
                                </div>
                                <div class="text-blue-800">
                                    <div class="flex justify-between items-center">
                                        <span>Total Amount:</span>
                                        <span id="total_amount_display" class="font-semibold">₱0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Required Downpayment (50%):</span>
                                        <span id="downpayment_amount_display" class="font-bold text-lg">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                            @error('amount_paid')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                            <select name="payment_method" id="payment_method" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_method') border-red-500 @enderror">
                                <option value="">Select method</option>
                                <option value="Cash" {{ old('payment_method', $payment->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Check" {{ old('payment_method', $payment->payment_method) == 'Check' ? 'selected' : '' }}>Check</option>
                                <option value="Bank Transfer" {{ old('payment_method', $payment->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="Credit Card" {{ old('payment_method', $payment->payment_method) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="GCash" {{ old('payment_method', $payment->payment_method) == 'GCash' ? 'selected' : '' }}>GCash</option>
                            </select>
                            @error('payment_method')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="payment_term" class="block text-sm font-medium text-gray-700 mb-1">Payment Term</label>
                        <select name="payment_term" id="payment_term"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('payment_term') border-red-500 @enderror">
                            <option value="">Select payment term</option>
                            <option value="Downpayment" {{ old('payment_term', $payment->payment_term) == 'Downpayment' ? 'selected' : '' }}>Downpayment</option>
                            <option value="Initial" {{ old('payment_term', $payment->payment_term) == 'Initial' ? 'selected' : '' }}>Initial</option>
                            <option value="Partial" {{ old('payment_term', $payment->payment_term) == 'Partial' ? 'selected' : '' }}>Partial</option>
                            <option value="Full" {{ old('payment_term', $payment->payment_term) == 'Full' ? 'selected' : '' }}>Full</option>
                        </select>
                        @error('payment_term')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mt-6" id="reference_number_field">
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $payment->reference_number) }}"
                               placeholder="Transaction ID, reference number, etc."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('reference_number') border-red-500 @enderror">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Additional Information</h3>
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea name="remarks" id="remarks" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('remarks') border-red-500 @enderror">{{ old('remarks', $payment->remarks) }}</textarea>
                        @error('remarks')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.payments.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-maroon text-white rounded-md hover:bg-maroon-700 focus:outline-none focus:ring-2 focus:ring-maroon">
                        Update Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const referenceNumberField = document.getElementById('reference_number_field');
    const paymentTermSelect = document.getElementById('payment_term');
    const amountInput = document.getElementById('amount_paid');
    const orderSelect = document.getElementById('order_id');
    const downpaymentInfo = document.getElementById('downpayment_info');
    const paymentDateInput = document.getElementById('payment_date');
    const maxPaymentInfo = document.getElementById('max_payment_info');
    
    // Store order amounts and payment data for validation
    const orderAmounts = {};
    const orderPayments = @json($orderPayments);
    
    // Populate order amounts from options
    orderSelect.querySelectorAll('option').forEach(option => {
        if (option.value) {
            const match = option.textContent.match(/₱([\d,]+\.?\d*)/);
            if (match) {
                orderAmounts[option.value] = parseFloat(match[1].replace(/,/g, ''));
            }
        }
    });
    
    function toggleReferenceField() {
        const selectedMethod = paymentMethodSelect.value;
        if (selectedMethod === 'GCash' || selectedMethod === 'Bank Transfer') {
            referenceNumberField.style.display = 'block';
        } else {
            referenceNumberField.style.display = 'none';
        }
    }
    
    function updateMaxPaymentInfo() {
        const selectedOrder = orderSelect.value;
        
        if (selectedOrder && orderAmounts[selectedOrder]) {
            const totalAmount = orderAmounts[selectedOrder];
            
            // Calculate already paid amount (total payments made by customer)
            const totalPaidByCustomer = orderPayments[selectedOrder] || 0;
            
            // Calculate remaining balance (excluding current payment being edited)
            const currentPaymentAmount = parseFloat(amountInput.value) || 0;
            const alreadyPaidExcludingCurrent = totalPaidByCustomer - currentPaymentAmount;
            const remainingBalance = totalAmount - alreadyPaidExcludingCurrent;
            const maxPayment = Math.max(0, remainingBalance); // Cannot exceed remaining balance
            
            // Update display elements
            document.getElementById('order_total_display').textContent = `₱${totalAmount.toFixed(2)}`;
            document.getElementById('already_paid_display').textContent = `₱${totalPaidByCustomer.toFixed(2)}`;
            document.getElementById('max_payment_display').textContent = `₱${maxPayment.toFixed(2)}`;
            
            // Add visual indicator if payment is at or near maximum
            const maxPaymentInfo = document.getElementById('max_payment_info');
            
            if (currentPaymentAmount > maxPayment) {
                maxPaymentInfo.classList.remove('bg-green-50', 'border-green-200');
                maxPaymentInfo.classList.add('bg-red-50', 'border-red-200');
                document.getElementById('max_payment_display').classList.add('text-red-700');
                document.getElementById('max_payment_display').classList.remove('text-green-700');
            } else {
                maxPaymentInfo.classList.remove('bg-red-50', 'border-red-200');
                maxPaymentInfo.classList.add('bg-green-50', 'border-green-200');
                document.getElementById('max_payment_display').classList.remove('text-red-700');
                document.getElementById('max_payment_display').classList.add('text-green-700');
            }
            
            maxPaymentInfo.style.display = 'block';
        } else {
            maxPaymentInfo.style.display = 'none';
        }
    }
    
    function toggleDownpaymentInfo() {
        const selectedTerm = paymentTermSelect.value;
        const selectedOrder = orderSelect.value;
        
        if (selectedTerm === 'Downpayment' && selectedOrder && orderAmounts[selectedOrder]) {
            const totalAmount = orderAmounts[selectedOrder];
            const expectedDownpayment = totalAmount * 0.5;
            
            // Update display elements
            document.getElementById('total_amount_display').textContent = `₱${totalAmount.toFixed(2)}`;
            document.getElementById('downpayment_amount_display').textContent = `₱${expectedDownpayment.toFixed(2)}`;
            
            downpaymentInfo.style.display = 'block';
        } else {
            downpaymentInfo.style.display = 'none';
        }
    }
    
    function validatePaymentAmount() {
        const selectedOrder = orderSelect.value;
        const enteredAmount = parseFloat(amountInput.value);
        
        if (selectedOrder && orderAmounts[selectedOrder] && enteredAmount > 0) {
            const totalAmount = orderAmounts[selectedOrder];
            const totalPaidByCustomer = orderPayments[selectedOrder] || 0;
            
            // Calculate remaining balance (excluding current payment being edited)
            const currentPaymentAmount = parseFloat(amountInput.value) || 0;
            const alreadyPaidExcludingCurrent = totalPaidByCustomer - currentPaymentAmount;
            const remainingBalance = totalAmount - alreadyPaidExcludingCurrent;
            const maxPayment = Math.max(0, remainingBalance);
            
            if (enteredAmount > maxPayment) {
                amountInput.classList.add('border-red-500');
                amountInput.classList.remove('border-gray-300');
                
                // Show error message
                let errorMsg = document.getElementById('amount_error_msg');
                if (!errorMsg) {
                    errorMsg = document.createElement('p');
                    errorMsg.id = 'amount_error_msg';
                    errorMsg.className = 'text-red-500 text-xs mt-1';
                    amountInput.parentNode.appendChild(errorMsg);
                }
                errorMsg.textContent = `Payment cannot exceed remaining balance of ₱${maxPayment.toFixed(2)}`;
            } else {
                amountInput.classList.remove('border-red-500');
                amountInput.classList.add('border-gray-300');
                
                // Remove error message
                const errorMsg = document.getElementById('amount_error_msg');
                if (errorMsg) {
                    errorMsg.remove();
                }
            }
        } else {
            amountInput.classList.remove('border-red-500');
            amountInput.classList.add('border-gray-300');
            
            // Remove error message
            const errorMsg = document.getElementById('amount_error_msg');
            if (errorMsg) {
                errorMsg.remove();
            }
        }
    }
    
    function validateDownpayment() {
        const selectedTerm = paymentTermSelect.value;
        const selectedOrder = orderSelect.value;
        const enteredAmount = parseFloat(amountInput.value);
        
        if (selectedTerm === 'Downpayment' && selectedOrder && orderAmounts[selectedOrder]) {
            const totalAmount = orderAmounts[selectedOrder];
            const expectedDownpayment = totalAmount * 0.5;
            const tolerance = 0.01;
            
            if (enteredAmount && Math.abs(enteredAmount - expectedDownpayment) > tolerance) {
                amountInput.classList.add('border-red-500');
                amountInput.classList.remove('border-gray-300');
                downpaymentInfo.classList.remove('bg-blue-50', 'border-blue-200', 'text-blue-700');
                downpaymentInfo.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
            } else {
                amountInput.classList.remove('border-red-500');
                amountInput.classList.add('border-gray-300');
                downpaymentInfo.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
                downpaymentInfo.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
            }
        } else {
            amountInput.classList.remove('border-red-500');
            amountInput.classList.add('border-gray-300');
            downpaymentInfo.classList.remove('bg-red-50', 'border-red-200', 'text-red-700');
            downpaymentInfo.classList.add('bg-blue-50', 'border-blue-200', 'text-blue-700');
        }
    }
    
    paymentMethodSelect.addEventListener('change', toggleReferenceField);
    paymentTermSelect.addEventListener('change', toggleDownpaymentInfo);
    orderSelect.addEventListener('change', function() {
        toggleDownpaymentInfo();
        updateMaxPaymentInfo();
        validatePaymentAmount();
    });
    amountInput.addEventListener('input', function() {
        validatePaymentAmount();
        validateDownpayment();
        updateMaxPaymentInfo();
    });
    
    // Check on page load
    toggleReferenceField();
    toggleDownpaymentInfo();
    updateMaxPaymentInfo();
});
</script>
@endsection
