@extends('layouts.cashier')

@section('title', 'Quotations')
@section('page-title', 'Quotation Management')
@section('page-description', 'Manage customer quotations and pricing')

@section('header-actions')
<div class="flex items-center space-x-4">
    <a href="{{ route('cashier.quotations.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
        <i class="fas fa-plus mr-2"></i>
        Create Quotation
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="flex items-center space-x-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer name..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('cashier.quotations.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quotations Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quotation #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valid Until</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($quotations as $quotation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $quotation->quotation_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $quotation->customer->customer_firstname }} {{ $quotation->customer->customer_lastname }}</div>
                            @if($quotation->customer->business_name)
                                <div class="text-sm text-gray-500">{{ $quotation->customer->business_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $quotation->quotation_date ? $quotation->quotation_date->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            ₱{{ number_format($quotation->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($quotation->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($quotation->status === 'approved') bg-green-100 text-green-800
                                @elseif($quotation->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($quotation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('cashier.quotations.show', $quotation) }}" 
                                   class="text-maroon hover:text-maroon-dark">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($quotation->status === 'pending')
                                    <form method="POST" action="{{ route('cashier.quotations.update-status', $quotation) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-green-600 hover:text-green-900" 
                                                onclick="return confirm('Approve this quotation?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('cashier.quotations.update-status', $quotation) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Reject this quotation?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($quotation->status === 'approved')
                                    <button type="button" onclick="openConvertModal({{ $quotation->quotation_id }})" 
                                            class="text-blue-600 hover:text-blue-900" title="Convert to Job Order">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-file-invoice text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No quotations found</p>
                                <p class="text-sm">Get started by creating a new quotation.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($quotations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $quotations->links() }}
        </div>
        @endif
    </div>
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
                
                <div class="space-y-4">
                    <!-- Employee Assignment -->
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Assign Production Staff <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">Select Production Staff</option>
                            @foreach(\App\Models\Employee::with('job')->get() as $employee)
                                @if($employee->job && in_array(strtolower($employee->job->job_title), ['production', 'production staff', 'production worker']))
                                    <option value="{{ $employee->employee_id }}">{{ $employee->full_name }} - {{ $employee->job->job_title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Layout Employee Assignment (if layout is checked) -->
                    <div id="layoutEmployeeDiv" class="hidden">
                        <label for="layout_employee_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Assign Graphics Designer <span class="text-red-500">*</span>
                        </label>
                        <select name="layout_employee_id" id="layout_employee_id" 
                                class="w-full border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">Select Graphics Designer</option>
                            @foreach(\App\Models\Employee::with('job')->get() as $employee)
                                @if($employee->job && in_array(strtolower($employee->job->job_title), ['graphics designer', 'designer', 'graphic designer']))
                                    <option value="{{ $employee->employee_id }}">{{ $employee->full_name }} - {{ $employee->job->job_title }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Order Date -->
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Order Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="order_date" id="order_date" required 
                               value="{{ date('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>

                    <!-- Deadline Date -->
                    <div>
                        <label for="deadline_date" class="block text-sm font-medium text-gray-700 mb-1">
                            Deadline Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="deadline_date" id="deadline_date" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>

                    <!-- Payment Section -->
                    <div class="border-t pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-3">Initial Payment (Optional)</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                <select name="payment_method" id="payment_method" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="Cash">Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Check">Check</option>
                                    <option value="Credit Card">Credit Card</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="payment_term" class="block text-sm font-medium text-gray-700 mb-1">Payment Term</label>
                                <select name="payment_term" id="payment_term" 
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="Downpayment">Downpayment</option>
                                    <option value="Initial">Initial</option>
                                    <option value="Full">Full</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label>
                                <input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            
                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" 
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label for="payment_remarks" class="block text-sm font-medium text-gray-700 mb-1">Payment Remarks</label>
                            <textarea name="payment_remarks" id="payment_remarks" rows="2" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-maroon focus:border-maroon"></textarea>
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
    
    // Check if quotation has layout items to show layout employee field
    fetch(`/cashier/quotations/${quotationId}/check-layout`)
        .then(response => response.json())
        .then(data => {
            const layoutDiv = document.getElementById('layoutEmployeeDiv');
            const layoutSelect = document.getElementById('layout_employee_id');
            
            if (data.hasLayout) {
                layoutDiv.classList.remove('hidden');
                layoutSelect.required = true;
            } else {
                layoutDiv.classList.add('hidden');
                layoutSelect.required = false;
            }
        })
        .catch(error => console.error('Error:', error));
}

function closeConvertModal() {
    document.getElementById('convertModal').classList.add('hidden');
    document.getElementById('convertForm').reset();
}
</script>
@endsection
