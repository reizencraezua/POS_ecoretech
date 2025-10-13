@extends('layouts.cashier')

@section('title', 'Quotations')
@section('page-title', 'Quotation Management')
@section('page-description', 'Manage customer quotations and proposals')

@section('header-actions')
<form method="GET" action="{{ route('cashier.quotations.index') }}" class="flex items-end gap-3">
    <div>
        <label for="start_date" class="block text-xs font-medium text-gray-600 mb-1">Start date</label>
        <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
    </div>
    <div>
        <label for="end_date" class="block text-xs font-medium text-gray-600 mb-1">End date</label>
        <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
            class="px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
    </div>
    <div class="flex items-center gap-2">
        <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
        <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-md">Filter</button>
        <a href="{{ route('cashier.quotations.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Reset</a>
    </div>
</form>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <a href="{{ route('cashier.quotations.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Quotation
            </a>
        </div>
            
        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
             <!-- Archive Button -->
             <a href="{{ route('cashier.quotations.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
                class="px-4 py-2 rounded-lg font-medium inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            <form method="GET" class="flex items-center space-x-2">
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
                </select>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search quotations..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
                <input type="hidden" name="archived" value="{{ (isset($showArchived) && $showArchived) ? 1 : 0 }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status') || request('start_date') || request('end_date') || request('archived'))
                <a href="{{ route('cashier.quotations.index', ['archived' => (isset($showArchived) && $showArchived) ? 1 : 0]) }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Quotations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($quotations as $quotation)
        <div class="bg-white rounded-lg shadow hover:shadow-lg border border-gray-200 cursor-pointer group" onclick="window.location.href='{{ route('cashier.quotations.show', $quotation) }}'">
            <!-- Card Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-maroon">Quote #{{ str_pad($quotation->quotation_id, 5, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-sm text-gray-600">{{ $quotation->quotation_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($quotation->status === 'Pending')
                                    bg-yellow-100 text-yellow-800
                                @else
                                    bg-green-100 text-green-800
                            @endif
                            ">
                            {{ $quotation->status }}
                            </span>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-maroon"></i>
                    </div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Customer Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-maroon text-white rounded-full flex items-center justify-center text-sm font-bold">
                            {{ substr($quotation->customer->customer_firstname, 0, 1) }}{{ substr($quotation->customer->customer_lastname, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $quotation->customer->display_name }}</p>
                            <p class="text-xs text-gray-500">{{ $quotation->customer->customer_contact }}</p>
                        </div>
                    </div>

                    <!-- Created By -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Created By:</span>
                        <span class="font-medium">
                            @if($quotation->creator)
                                @if($quotation->creator->employee)
                                    EMP{{ $quotation->creator->employee->employee_id }} : {{ $quotation->creator->employee->employee_firstname }}
                                @else
                                    {{ $quotation->creator->name }}
                                @endif
                            @else
                                System
                            @endif
                        </span>
                    </div>

                    <!-- Items Count -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Items:</span>
                        <span class="font-medium">{{ $quotation->details->count() }} item(s)</span>
                    </div>

                    <!-- Total Quantity -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Qty:</span>
                        <span class="font-medium">{{ $quotation->details->sum('quantity') }} pcs</span>
                    </div>

                    <!-- Total Amount -->
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="text-lg font-bold text-maroon">₱{{ number_format($quotation->final_total_amount, 2) }}</span>
                    </div>

                    <!-- Notes Preview -->
                    @if($quotation->notes)
                    <div class="text-xs text-gray-500">
                        <p class="truncate">{{ \Illuminate\Support\Str::limit($quotation->notes, 50) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card Actions -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between" onclick="event.stopPropagation()">
                <div class="flex items-center space-x-2">
                    @if($quotation->status === 'Pending')
                    <form method="POST" action="{{ route('cashier.quotations.status', $quotation) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                        <input type="hidden" name="status" value="Closed">
                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                            <i class="fas fa-check mr-1"></i>Close
                                        </button>
                                    </form>
                                @endif

                    @if($quotation->status === 'Closed')
                    <button onclick="openConvertModal({{ $quotation->quotation_id }})"
                        class="text-blue-600 hover:text-blue-800 text-sm">
                        <i class="fas fa-exchange-alt mr-1"></i>Convert
                                    </button>
                                @endif
                            </div>

                <div class="flex items-center">
                    <a href="{{ route('cashier.quotations.edit', $quotation) }}" class="text-maroon hover:text-maroon-dark text-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
                    @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <div class="text-gray-400">
                <i class="fas fa-file-alt text-6xl mb-4"></i>
                <p class="text-xl font-medium mb-2">No quotations found</p>
                <p class="text-gray-500 mb-4">Create your first quotation to get started</p>
            </div>
                            </div>
                    @endforelse
        </div>
        
    <!-- Pagination -->
        @if($quotations->hasPages())
    <div class="bg-white rounded-lg shadow p-4">
            {{ $quotations->links() }}
        </div>
        @endif
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
            
            <form id="convertForm" method="POST" action="{{ route('cashier.quotations.convert-to-job') }}">
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

                            <!-- Order Date and Deadline Date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date *</label>
                                    <input type="date" name="order_date" id="order_date" required
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"
                                        value="{{ now()->format('Y-m-d') }}">
                                    <p class="text-xs text-gray-500 mt-1">Date when the job order is created</p>
                                </div>

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
                                        placeholder="Enter amount paid">
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
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark">
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

            // Fetch quotation details to get total amount and layout info
            fetch(`/cashier/quotations/${quotationId}/data`)
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

                    // Show user-friendly error message
                    showErrorMessage('Failed to load quotation amount. Please try again.');
                });
        }
    }

    function updateDownpaymentInfo() {
        const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
        const downpaymentAmount = parseFloat(document.getElementById('downpayment_amount_display').textContent.replace('₱', '').replace(',', '')) || 0;
        const remainingBalance = downpaymentAmount - amountPaid;

        document.getElementById('already_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('remaining_balance_display').textContent = '₱' + remainingBalance.toFixed(2);
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

            // Get total amount and required downpayment
            const finalTotalAmount = parseFloat(document.getElementById('total_amount_display').textContent.replace('₱', '').replace(',', '')) || 0;
            const requiredDownpayment = finalTotalAmount * 0.5; // 50% downpayment requirement
            const amountPaidValue = parseFloat(amountPaid);
            
            // Check if amount paid is less than required downpayment
            if (amountPaidValue < requiredDownpayment) {
                showErrorMessage('Downpayment must be at least 50% of the total amount (₱' + requiredDownpayment.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ').');
                return false;
            }
            
            // Check if amount paid exceeds full payment amount
            if (amountPaidValue > finalTotalAmount) {
                showErrorMessage('Amount paid cannot exceed the total amount of ₱' + finalTotalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '.');
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
@endsection
