@extends('layouts.admin')

@section('title', 'Quotations')
@section('page-title', 'Quotation Management')
@section('page-description', 'Manage customer quotations and proposals')

@section('header-actions')
<form method="GET" action="{{ route('admin.quotations.index') }}" class="flex items-end gap-3" id="dateFilterForm">
    <div>
        <label for="start_date" class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
               class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
               onchange="document.getElementById('dateFilterForm').submit();">
    </div>
    <div>
        <label for="end_date" class="block text-xs font-medium text-gray-600 mb-1">End date</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
               class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
               onchange="document.getElementById('dateFilterForm').submit();">
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
        <a href="{{ route('admin.quotations.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
    </div>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.quotations.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Quotation
            </a>
        </div>

        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <form method="GET" class="flex items-center space-x-2" id="searchForm">
                 <!-- Archive Button -->
                <a href="{{ route('admin.quotations.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
                   class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                    <i class="fas fa-box-archive mr-2"></i>
                    {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
                </a>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon" onchange="document.getElementById('searchForm').submit();">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <div class="relative">
                    <input type="text" 
                           id="instantSearchInput" 
                           data-instant-search="true"
                           data-container="quotationsTableContainer"
                           data-loading="searchLoading"
                           value="{{ request('search') }}" 
                           placeholder="Search quotations..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                @if(request('search') || request('status') || request('start_date') || request('end_date') || request('archived'))
                    <a href="{{ route('admin.quotations.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
            
           
        </div>
    </div>

    @include('admin.quotations.partials.quotations-table')
</div>

<!-- Convert to Job Order Modal -->
<div id="convertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Convert Quotation to Job Order</h3>
                <button onclick="closeConvertModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="convertForm" method="POST" action="{{ route('admin.quotations.convert-to-job') }}">
                @csrf
                <input type="hidden" id="quotation_id" name="quotation_id">

                <div class="space-y-6">
                    <!-- Employee Assignment Section -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-users text-maroon mr-2"></i>
                            Employee Assignment
                        </h4>

                        <div class="space-y-4">
                            <!-- Production Staff and Graphics Designer -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Assign Production Staff *</label>
                                    <select name="employee_id" id="employee_id" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select Production Staff</option>
                                        @foreach(\App\Models\Employee::with('job')->get() as $employee)
                                        @if($employee->job && in_array(strtolower($employee->job->job_title), ['production staff', 'production worker', 'production', 'staff']))
                                        <option value="{{ $employee->employee_id }}">{{ $employee->full_name }} ({{ $employee->job->job_title }})</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div id="layout_employee_div" class="hidden">
                                    <label for="layout_employee_id" class="block text-sm font-medium text-gray-700 mb-2">Assign Graphics Designer *</label>
                                    <select name="layout_employee_id" id="layout_employee_id"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select Graphics Designer</option>
                                        @foreach(\App\Models\Employee::with('job')->get() as $employee)
                                        @if($employee->job && in_array(strtolower($employee->job->job_title), ['graphics designer', 'designer', 'graphic designer', 'layout designer']))
                                        <option value="{{ $employee->employee_id }}">{{ $employee->full_name }} ({{ $employee->job->job_title }})</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Deadline Date -->
                            <div>
                                <label for="deadline_date" class="block text-sm font-medium text-gray-700 mb-2">Deadline Date *</label>
                                <input type="date" name="deadline_date" id="deadline_date" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"
                                    min="{{ now()->addDay()->toDateString() }}"
                                    value="{{ now()->addDays(7)->toDateString() }}">
                                <p class="text-xs text-gray-500 mt-1">Set the deadline for job completion</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Section -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                            Payment Information
                        </h4>

                        <div class="space-y-4">
                            <!-- Payment Term and Payment Method -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="payment_term" class="block text-sm font-medium text-gray-700 mb-2">Payment Term</label>
                                    <select name="payment_term" id="payment_term"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select Payment Term</option>
                                        <option value="Full">Full Payment</option>
                                        <option value="Downpayment">Downpayment (50%)</option>
                                        <option value="Initial">Initial Payment</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Method
                                        <span id="payment_method_required" class="text-red-500 hidden">*</span>
                                    </label>
                                    <select name="payment_method" id="payment_method"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select Payment Method</option>
                                        <option value="Cash">Cash</option>
                                        <option value="GCash">GCash</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Check">Check</option>
                                        <option value="Credit Card">Credit Card</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Downpayment Information Box -->
                            <div id="downpayment_info_div" class="hidden">
                                <div class="bg-blue-100 border border-blue-300 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-2">
                                            <i class="fas fa-info text-xs"></i>
                                        </div>
                                        <h5 class="text-md font-semibold text-blue-800">Downpayment Information</h5>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">Total Amount:</span>
                                            <span class="text-gray-700 font-medium" id="total_amount_display">₱0.00</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">Required Downpayment (50%):</span>
                                            <span class="text-blue-800 font-bold text-lg" id="downpayment_amount_display">₱0.00</span>
                                        </div>
                                        
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>Note: You can pay more than the downpayment</span>
                                            <span>Max: <span id="max_payment_display">₱0.00</span></span>
                                        </div>

                                        <hr class="border-blue-200 my-2">

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">Already Paid:</span>
                                            <span class="text-blue-600 font-medium" id="already_paid_display">₱0.00</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">Remaining Balance:</span>
                                            <span class="text-blue-600 font-medium" id="remaining_balance_display">₱0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <!-- Amount Paid and Reference Number -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">
                                        Amount Paid
                                        <span id="amount_paid_required" class="text-red-500 hidden">*</span>
                                    </label>
                                    <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"
                                        placeholder="Enter amount paid (can exceed downpayment)">
                                </div>

                                <div id="reference_number_div" class="hidden">
                                    <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Reference Number *
                                    </label>
                                    <input type="text" name="reference_number" id="reference_number"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"
                                        placeholder="Enter reference number">
                                    <p class="text-xs text-gray-500 mt-1">Required for GCash and Bank Transfer payments</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-sticky-note text-green-600 mr-2"></i>
                            Additional Information
                        </h4>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"
                                placeholder="Additional notes for the job order"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeConvertModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                        Convert to Job Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openConvertModal(quotationId) {
        document.getElementById('quotation_id').value = quotationId;
        document.getElementById('convertModal').classList.remove('hidden');
        calculateDownpaymentInfo();
    }

    function closeConvertModal() {
        document.getElementById('convertModal').classList.add('hidden');
        document.getElementById('convertForm').reset();
        document.getElementById('downpayment_info_div').classList.add('hidden');
        clearErrorMessages();
    }

    function toggleDownpaymentField() {
        const paymentTerm = document.getElementById('payment_term').value;
        const downpaymentDiv = document.getElementById('downpayment_info_div');
        const amountPaidRequired = document.getElementById('amount_paid_required');
        const paymentMethodRequired = document.getElementById('payment_method_required');
        const amountPaidField = document.getElementById('amount_paid');
        const paymentMethodField = document.getElementById('payment_method');

        if (paymentTerm === 'Downpayment') {
            downpaymentDiv.classList.remove('hidden');
            amountPaidRequired.classList.remove('hidden');
            paymentMethodRequired.classList.remove('hidden');
            amountPaidField.setAttribute('required', 'required');
            paymentMethodField.setAttribute('required', 'required');
            calculateDownpaymentInfo();
        } else {
            downpaymentDiv.classList.add('hidden');
            amountPaidRequired.classList.add('hidden');
            paymentMethodRequired.classList.add('hidden');
            amountPaidField.removeAttribute('required');
            paymentMethodField.removeAttribute('required');
        }
    }

    function toggleReferenceNumberField() {
        const paymentMethod = document.getElementById('payment_method').value;
        const referenceNumberDiv = document.getElementById('reference_number_div');
        const referenceNumberField = document.getElementById('reference_number');

        if (paymentMethod === 'GCash' || paymentMethod === 'Bank Transfer') {
            referenceNumberDiv.classList.remove('hidden');
            referenceNumberField.setAttribute('required', 'required');
        } else {
            referenceNumberDiv.classList.add('hidden');
            referenceNumberField.removeAttribute('required');
            referenceNumberField.value = '';
        }
    }

    function calculateDownpaymentInfo() {
        const quotationId = document.getElementById('quotation_id').value;
        const totalAmountDisplay = document.getElementById('total_amount_display');
        const downpaymentAmountDisplay = document.getElementById('downpayment_amount_display');
        const alreadyPaidDisplay = document.getElementById('already_paid_display');
        const remainingBalanceDisplay = document.getElementById('remaining_balance_display');
        const layoutEmployeeDiv = document.getElementById('layout_employee_div');

        if (quotationId) {
            // Show loading state
            totalAmountDisplay.textContent = 'Loading...';
            downpaymentAmountDisplay.textContent = 'Loading...';
            alreadyPaidDisplay.textContent = '₱0.00';
            remainingBalanceDisplay.textContent = 'Loading...';
            document.getElementById('max_payment_display').textContent = 'Loading...';

            // Fetch quotation details to get total amount and layout info
            fetch(`/admin/quotations/${quotationId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch quotation data');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.final_total_amount) {
                        const totalAmount = parseFloat(data.final_total_amount);
                        const downpaymentAmount = totalAmount * 0.5;
                        const alreadyPaid = 0; // For new orders, no previous payments
                        const remainingBalance = downpaymentAmount - alreadyPaid;

                        // Update all displays
                        totalAmountDisplay.textContent = '₱' + totalAmount.toFixed(2);
                        downpaymentAmountDisplay.textContent = '₱' + downpaymentAmount.toFixed(2);
                        alreadyPaidDisplay.textContent = '₱' + alreadyPaid.toFixed(2);
                        remainingBalanceDisplay.textContent = '₱' + remainingBalance.toFixed(2);
                        
                        // Update maximum payment display
                        document.getElementById('max_payment_display').textContent = '₱' + totalAmount.toFixed(2);

                        // Check if layout is required and show/hide layout employee field
                        if (data.has_layout) {
                            layoutEmployeeDiv.classList.remove('hidden');
                            // Make layout employee required
                            document.getElementById('layout_employee_id').setAttribute('required', 'required');
                        } else {
                            layoutEmployeeDiv.classList.add('hidden');
                            document.getElementById('layout_employee_id').removeAttribute('required');
                        }
                    } else {
                        throw new Error('Invalid quotation data');
                    }
                })
                .catch(error => {
                    console.error('Error fetching quotation details:', error);
                    // Show error state
                    totalAmountDisplay.textContent = 'Error';
                    downpaymentAmountDisplay.textContent = 'Error';
                    remainingBalanceDisplay.textContent = 'Error';
                    document.getElementById('max_payment_display').textContent = 'Error';

                    // Show user-friendly error message
                    showErrorMessage('Failed to load quotation amount. Please try again.');
                });
        }
    }

    function updateDownpaymentInfo() {
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const downpaymentAmount = parseFloat(document.getElementById('downpayment_amount_display').textContent.replace('₱', '').replace(',', '')) || 0;
        const totalAmount = parseFloat(document.getElementById('total_amount_display').textContent.replace('₱', '').replace(',', '')) || 0;
        
        // Calculate remaining balance based on total amount, not just downpayment
        const remainingBalance = totalAmount - amountPaid;

        document.getElementById('already_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('remaining_balance_display').textContent = '₱' + remainingBalance.toFixed(2);
        
        // Show visual feedback based on payment amount
        const remainingElement = document.getElementById('remaining_balance_display');
        const amountPaidInput = document.getElementById('amount_paid');
        
        if (amountPaid > totalAmount) {
            // Amount exceeds total - show error
            remainingElement.style.color = '#dc2626'; // Red color for overpayment
            remainingElement.title = 'Amount paid exceeds total quotation amount';
            amountPaidInput.style.borderColor = '#dc2626';
        } else if (amountPaid > downpaymentAmount && amountPaid <= totalAmount) {
            // Amount exceeds downpayment but within total - show success
            remainingElement.style.color = '#059669'; // Green color for overpayment
            remainingElement.title = 'Amount paid exceeds downpayment but is within total amount';
            amountPaidInput.style.borderColor = '#059669';
        } else if (amountPaid > 0 && amountPaid <= downpaymentAmount) {
            // Normal downpayment range
            remainingElement.style.color = '#2563eb'; // Blue color for normal
            remainingElement.title = 'Amount paid is within downpayment range';
            amountPaidInput.style.borderColor = '#2563eb';
        } else {
            // Reset colors for zero amount
            remainingElement.style.color = '';
            remainingElement.title = '';
            amountPaidInput.style.borderColor = '';
        }
    }

    function validateConvertForm() {
        const paymentTerm = document.getElementById('payment_term').value;
        const amountPaid = document.getElementById('amount_paid').value;
        const paymentMethod = document.getElementById('payment_method').value;
        const referenceNumber = document.getElementById('reference_number').value;

        clearErrorMessages();

        if (!paymentTerm) {
            showErrorMessage('Please select a payment term.');
            return false;
        }

        if (paymentTerm === 'Downpayment') {
            if (!amountPaid || amountPaid <= 0) {
                showErrorMessage('Amount paid is required for downpayment.');
                return false;
            }

            if (!paymentMethod) {
                showErrorMessage('Payment method is required for downpayment.');
                return false;
            }

            // Check if amount paid exceeds total amount
            const totalAmount = parseFloat(document.getElementById('total_amount_display').textContent.replace('₱', '').replace(',', '')) || 0;
            if (parseFloat(amountPaid) > totalAmount) {
                showErrorMessage('Amount paid cannot exceed the total quotation amount.');
                return false;
            }

            // Check reference number for GCash and Bank Transfer
            if ((paymentMethod === 'GCash' || paymentMethod === 'Bank Transfer') && !referenceNumber) {
                showErrorMessage('Reference number is required for GCash and Bank Transfer payments.');
                return false;
            }
        }

        return true;
    }

    function showErrorMessage(message) {
        // Remove existing error messages
        const existingError = document.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Create new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.innerHTML = `
         <div class="flex items-center">
             <i class="fas fa-exclamation-circle mr-2"></i>
             ${message}
         </div>
     `;

        // Insert error message at the top of the form
        const form = document.getElementById('convertForm');
        form.insertBefore(errorDiv, form.firstChild);
    }

    function clearErrorMessages() {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => error.remove());
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('payment_term').addEventListener('change', toggleDownpaymentField);
        document.getElementById('payment_method').addEventListener('change', toggleReferenceNumberField);
        document.getElementById('amount_paid').addEventListener('input', updateDownpaymentInfo);
        document.getElementById('convertForm').addEventListener('submit', function(e) {
            if (!validateConvertForm()) {
                e.preventDefault();
            }
        });
    });
</script>

<script src="{{ asset('js/instant-search.js') }}"></script>
@endsection