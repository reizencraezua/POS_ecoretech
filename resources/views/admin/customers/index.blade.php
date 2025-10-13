@extends('layouts.admin')

@section('title', 'Customers')
@section('page-title', 'Customer Management')
@section('page-description', 'Manage your customer database')


@section('content')
<div class="space-y-6" x-data="{ customerModal: false, editModal: false, editingCustomer: null }">
    
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <button @click="customerModal = true" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Customer
                </button>
            @endif
        </div>
        
        <!-- Search and Filters -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.customers.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <form method="GET" class="flex items-center space-x-2" id="searchForm">
                <div class="relative">
                    <input type="text" 
                           id="instantSearchInput" 
                           data-instant-search="true"
                           data-container="customersTableContainer"
                           data-loading="searchLoading"
                           value="{{ request('search') }}" 
                           placeholder="Search customers..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business/TIN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.customers.show', $customer) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center font-bold">
                                        {{ substr($customer->customer_firstname, 0, 1) }}{{ substr($customer->customer_lastname, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $customer->full_name }}</div>
                                        <div class="text-sm text-gray-500">#{{ str_pad($customer->customer_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $customer->contact_person1 }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->contact_number1 }}</div>
                                @if($customer->customer_email)
                                    <div class="text-sm text-gray-500">{{ $customer->customer_email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($customer->business_name)
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->business_name }}</div>
                                    @if($customer->tin)
                                        <div class="text-sm text-gray-500">TIN: {{ $customer->tin }}</div>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">Individual Customer</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $customer->orders_count ?? 0 }} orders</div>
                                <div class="text-sm text-gray-500">{{ $customer->quotations_count ?? 0 }} quotes</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-center space-x-3">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$customer" 
                                            :archiveRoute="'admin.customers.archive'" 
                                            :restoreRoute="'admin.customers.restore'" 
                                            :editRoute="'admin.customers.edit'"
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$customer" 
                                            :archiveRoute="'admin.customers.archive'" 
                                            :restoreRoute="'admin.customers.restore'" 
                                            :editRoute="'admin.customers.edit'"
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-users text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No customers found</p>
                                    <p class="text-gray-500 mb-4">Start by adding your first customer</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Add Customer Modal -->
    <div x-show="customerModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="customerModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Customer</h3>
                <button @click="customerModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.customers.store') }}" class="space-y-6">
                @csrf
                
                <!-- Personal Information -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="customer_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="customer_firstname" id="customer_firstname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label for="customer_middlename" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="customer_middlename" id="customer_middlename"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label for="customer_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="customer_lastname" id="customer_lastname" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Business Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                            <input type="text" name="business_name" id="business_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                   placeholder="Leave blank for individual customer">
                        </div>
                        <div>
                            <label for="tin" class="block text-sm font-medium text-gray-700 mb-1">TIN (Optional)</label>
                            <input type="text" name="tin" id="tin"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                   placeholder="000-000-000-000">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h4>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" name="customer_email" id="customer_email"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            <div>
                                <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                                <textarea name="customer_address" id="customer_address" rows="2" required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"></textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_person1" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact Person *</label>
                                <input type="text" name="contact_person1" id="contact_person1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            <div>
                                <label for="contact_number1" class="block text-sm font-medium text-gray-700 mb-1">Primary Contact Number *</label>
                                <input type="text" name="contact_number1" id="contact_number1" required
                                       pattern="[0-9]{11}" maxlength="11" minlength="11"
                                       title="Contact number must be exactly 11 digits"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                       placeholder="09XX-XXX-XXXX">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_person2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Contact Person</label>
                                <input type="text" name="contact_person2" id="contact_person2"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                            <div>
                                <label for="contact_number2" class="block text-sm font-medium text-gray-700 mb-1">Secondary Contact Number</label>
                                <input type="text" name="contact_number2" id="contact_number2"
                                       pattern="[0-9]{11}" maxlength="11" minlength="11"
                                       title="Contact number must be exactly 11 digits"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                       placeholder="09XX-XXX-XXXX">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" @click="customerModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="editModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Edit Customer</h3>
                <button @click="editModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form x-bind:action="editingCustomer ? `/admin/customers/${editingCustomer.customer_id}` : '#'" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Similar form structure as add modal but with x-model bindings for editing -->
                <div>
                    <h4 class="text-lg font-medium text-gray-900 mb-3">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="customer_firstname" x-model="editingCustomer ? editingCustomer.customer_firstname : ''" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="customer_middlename" x-model="editingCustomer ? editingCustomer.customer_middlename : ''"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="customer_lastname" x-model="editingCustomer ? editingCustomer.customer_lastname : ''" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" @click="editModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCustomer(customer) {
    this.editingCustomer = customer;
    this.editModal = true;
}

</script>

<style>
[x-cloak] { display: none !important; }
</style>
<script src="{{ asset('js/instant-search.js') }}"></script>
@endsection