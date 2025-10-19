@extends('layouts.admin')

@section('title', 'Quotations')
@section('page-title', 'Quotation Management')
@section('page-description', 'Manage customer quotations and proposals')


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
                    <input type="text" id="searchInput" placeholder="Search quotations..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon w-80">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
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
                    <!-- Quotation Items Section -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-list text-maroon mr-2"></i>
                            Quotation Items
                        </h4>
                        
                        <div id="quotation_items_container">
                            <!-- Items will be loaded here via JavaScript -->
                            <div class="text-center py-4 text-gray-500">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Loading quotation items...
                            </div>
                        </div>
                    </div>

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
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
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
                                        <option value="Downpayment">Downpayment</option>
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
        loadQuotationItems(quotationId);
        calculateDownpaymentInfo();
    }

    function closeConvertModal() {
        document.getElementById('convertModal').classList.add('hidden');
        document.getElementById('convertForm').reset();
        clearErrorMessages();
    }

    function toggleDownpaymentField() {
        const paymentTerm = document.getElementById('payment_term').value;
        const amountPaidRequired = document.getElementById('amount_paid_required');
        const paymentMethodRequired = document.getElementById('payment_method_required');
        const amountPaidField = document.getElementById('amount_paid');
        const paymentMethodField = document.getElementById('payment_method');

        if (paymentTerm === 'Downpayment') {
            amountPaidRequired.classList.remove('hidden');
            paymentMethodRequired.classList.remove('hidden');
            amountPaidField.setAttribute('required', 'required');
            paymentMethodField.setAttribute('required', 'required');
        } else {
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

    function loadQuotationItems(quotationId) {
        const container = document.getElementById('quotation_items_container');
        
        if (quotationId) {
            // Show loading state
            container.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Loading quotation items...
                </div>
            `;

            // Fetch quotation items
            fetch(`/admin/quotations/${quotationId}/items`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch quotation items');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.items && data.items.length > 0) {
                        // Build items table
                        let itemsHtml = `
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                        `;

                        data.items.forEach(item => {
                            const layoutStatus = item.layout ? 
                                '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-green-800"><i class="fas fa-check mr-1"></i>Yes</span>' :
                                '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium text-gray-800"><i class="fas fa-times mr-1"></i>No</span>';
                            
                            const layoutPrice = item.layout && item.layout_price > 0 ? 
                                '₱' + parseFloat(item.layout_price).toFixed(2) : 
                                '<span class="text-gray-400">-</span>';

                            itemsHtml += `
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full ${item.item_type === 'Product' ? 'text-blue-800 bg-blue-100' : 'text-green-800 bg-green-100'}">
                                            ${item.item_type}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${item.item_name}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${item.quantity}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${item.unit_name || 'N/A'}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${item.size || '-'}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">₱${parseFloat(item.price).toFixed(2)}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${layoutStatus}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${layoutPrice}</td>
                                </tr>
                            `;
                        });

                        itemsHtml += `
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 flex justify-between items-center text-sm text-gray-600">
                                <span><strong>${data.items.length}</strong> items</span>
                                <span>total amount: <strong>₱${parseFloat(data.total_amount).toFixed(2)}</strong> </span>
                            </div>
                        `;

                        container.innerHTML = itemsHtml;
                    } else {
                        container.innerHTML = `
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-box text-4xl mb-2"></i>
                                <p>No items found for this quotation.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching quotation items:', error);
                    container.innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            <i class="fas fa-exclamation-triangle text-4xl mb-2"></i>
                            <p>Failed to load quotation items. Please try again.</p>
                        </div>
                    `;
                });
        }
    }

    function calculateDownpaymentInfo() {
        const quotationId = document.getElementById('quotation_id').value;
        const layoutEmployeeDiv = document.getElementById('layout_employee_div');

        if (quotationId) {
            // Fetch quotation details to check if layout is required
            fetch(`/admin/quotations/${quotationId}/data`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch quotation data');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if layout is required and show/hide layout employee field
                    if (data.has_layout) {
                        layoutEmployeeDiv.classList.remove('hidden');
                        // Make layout employee required
                        document.getElementById('layout_employee_id').setAttribute('required', 'required');
                    } else {
                        layoutEmployeeDiv.classList.add('hidden');
                        document.getElementById('layout_employee_id').removeAttribute('required');
                    }
                })
                .catch(error => {
                    console.error('Error fetching quotation details:', error);
                    // Show user-friendly error message
                    showErrorMessage('Failed to load quotation data. Please try again.');
                });
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
        document.getElementById('convertForm').addEventListener('submit', function(e) {
            if (!validateConvertForm()) {
                e.preventDefault();
            }
        });
    });

    // Search functionality (adapted for quotations grid)
    const searchInput = document.getElementById('searchInput');
    const quotationsContainer = document.getElementById('quotationsTableContainer');
    
    if (searchInput && quotationsContainer) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const cards = quotationsContainer.querySelectorAll('.grid > div');
            
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
</script>
@endsection