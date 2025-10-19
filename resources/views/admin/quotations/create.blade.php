@extends('layouts.admin')

@section('title', 'Create Quotation')
@section('page-title', 'Create New Quotation')
@section('page-description', 'Create a quotation for a customer to preview order costs')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.quotations.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Create Quotation</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Create a detailed quotation for customer cost preview
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.quotations.store') }}" class="p-6" x-data="quotationForm()" x-init="init()" @submit="console.log('Form submitted', $data)">
            @csrf
            
            <!-- Quotation Information Section -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-invoice mr-2 text-maroon"></i>
                    Quotation Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label for="customer_search" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                        <div class="relative">
                            <input type="text" id="customer_search" placeholder="Search or type customer name..." 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_id') border-red-500 @enderror"
                                   autocomplete="off" oninput="filterCustomers(this)" onfocus="showCustomerDropdown()" onblur="hideCustomerDropdown()">
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}">
                            <div id="customer_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                              
                                <div class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-green-600 font-semibold border-t border-gray-200" 
                                     onclick="showAddCustomerModal()">
                                    + Add New Customer
                                </div>
                                @foreach($customers as $customer)
                                    <div class="px-3 py-2 hover:bg-gray-100 cursor-pointer customer-option" 
                                         data-id="{{ $customer->customer_id }}" 
                                         data-name="{{ $customer->display_name }}"
                                         onclick="selectCustomer(this)">
                                        {{ $customer->display_name }}
                                    </div>
                                @endforeach
                               
                            </div>
                        </div>
                        @error('customer_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="quotation_date" class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                        <input type="date" name="quotation_date" id="quotation_date" value="{{ old('quotation_date', now()->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('quotation_date') border-red-500 @enderror">
                        @error('quotation_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                        <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('valid_until') border-red-500 @enderror">
                        @error('valid_until')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Quotation Items Section -->
            <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-box mr-2 text-maroon"></i>
                            Quotation Items
                        </h3>
                        <button type="button" @click="addItem()" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg transition-colors inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                </div>
                <div class="p-6">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layout</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-4">
                                        <select x-model="item.type" @change="updateItemOptions(index)" :name="`items[${index}][type]`" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Type</option>
                                            <option value="product">Product</option>
                                            <option value="service">Service</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.id" @change="updatePrice(index)" :name="`items[${index}][id]`" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Item</option>
                                            <template x-if="item.type === 'product'">
                                                <template x-for="product in products">
                                                    <option :value="product.product_id" x-text="product.product_name + ' - ₱' + product.base_price"></option>
                                                </template>
                                            </template>
                                            <template x-if="item.type === 'service'">
                                                <template x-for="service in services">
                                                    <option :value="service.service_id" x-text="service.service_name + ' - ₱' + service.base_fee"></option>
                                                </template>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" x-model="item.quantity" @input="calculateSubtotal(index)" :name="`items[${index}][quantity]`" 
                                               min="1" required class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.unit_id" :name="`items[${index}][unit_id]`" 
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Unit</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->unit_id }}">{{ $unit->unit_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.size" :name="`items[${index}][size]`" 
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Size</option>
                                            <template x-if="(item.type === 'product' || item.type === 'service') && item.id">
                                                <template x-for="size in getAvailableSizes(item.id, item.type)">
                                                    <option :value="size.size_name" x-text="size.size_name"></option>
                                                </template>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" x-model="item.price" :name="`items[${index}][price]`" 
                                               step="0.01" min="0" required readonly class="w-32 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700 cursor-not-allowed"
                                               placeholder="0.00">
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center space-x-2">
                                            <input type="checkbox" x-model="item.layout" @change="calculateSubtotal(index)" :name="`items[${index}][layout]`" 
                                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded">
                                            <span class="text-xs text-gray-600" x-text="item.layoutPrice > 0 ? '₱' + item.layoutPrice.toFixed(2) : ''"></span>
                                            <!-- Hidden input for layout price -->
                                            <input type="hidden" x-model="item.layoutPrice" :name="`items[${index}][layoutPrice]`">
                                        </div>
                                    </td>
                                   
                                    <td class="px-4 py-4">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-800 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            
                            <tr x-show="items.length === 0">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box text-4xl mb-2"></i>
                                    <p>No items added yet. Click "Add Item" to start.</p>
                                </td>
                            </tr>
                        </tbody>
                       
                    </table>
                </div>
                </div>
            </div>

            <!-- Quotation Summary Section -->
            <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calculator mr-2 text-maroon"></i>
                        Quotation Summary
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Items Summary -->
                        <div class="space-y-4">

                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Total Quantity:</span>
                                <span class="text-sm font-medium text-gray-900" x-text="getTotalQuantity() + ' pcs'"></span>
                            </div>

                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Sub Total:</span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getSubTotal().toFixed(2)"></span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">VAT (12%):</span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getVATAmount().toFixed(2)"></span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Base Amount:</span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getBaseAmount().toFixed(2)"></span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600" x-text="'-₱' + getOrderDiscount().toFixed(2)"></div>
                                    <div class="text-xs text-gray-500" x-text="getDiscountInfo()"></div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Layout Fees:</span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getLayoutFees().toFixed(2)"></span>
                            </div>
                            
                            <div class="flex justify-between items-center py-3 border-t-2 border-maroon">
                                <span class="text-lg font-semibold text-gray-900">FINAL TOTAL:</span>
                                <span class="text-xl font-bold text-maroon" x-text="'₱' + getFinalTotalAmount().toFixed(2)"></span>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="space-y-4">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                          placeholder="Additional notes for this quotation..."></textarea>
                            </div>
                            
                            <div>
                                <label for="terms_and_conditions" class="block text-sm font-medium text-gray-700 mb-1">Terms & Conditions</label>
                                <textarea name="terms_and_conditions" id="terms_and_conditions" rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                          placeholder="Terms and conditions for this quotation..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-info-circle mr-1"></i>
                            Review all details before creating the quotation
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.quotations.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Create Quotation
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function quotationForm() {
    return {
        items: [],
        totalAmount: 0,
        products: @json($products),
        services: @json($services),
        customers: @json($customers),
        discountRules: @json($discountRules),
        
        addItem() {
            this.items.push({
                type: '',
                id: '',
                quantity: 1,
                unit_id: '',
                size: '',
                price: 0,
                layout: false,
                layoutPrice: 0,
                discountAmount: 0,
                discountRule: '',
                subtotal: 0
            });
            this.calculateTotal();
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotal();
        },
        
        updateItemOptions(index) {
            this.items[index].id = '';
            this.items[index].price = 0;
            this.items[index].layout = false;
            this.items[index].layoutPrice = 0;
            this.items[index].discountAmount = 0;
            this.items[index].discountRule = '';
            this.items[index].size = '';
            this.calculateSubtotal(index);
        },

        getAvailableSizes(itemId, itemType) {
            if (itemType === 'product') {
                const product = this.products.find(p => p.product_id == itemId);
                if (!product || !product.category) return [];
                return product.category.sizes || [];
            } else if (itemType === 'service') {
                const service = this.services.find(s => s.service_id == itemId);
                if (!service || !service.category) return [];
                return service.category.sizes || [];
            }
            return [];
        },
        
        updatePrice(index) {
            const item = this.items[index];
            if (item.type === 'product' && item.id) {
                const product = this.products.find(p => p.product_id == item.id);
                if (product) {
                    item.price = parseFloat(product.base_price) || 0;
                    item.layoutPrice = parseFloat(product.layout_price) || 0;
                }
            } else if (item.type === 'service' && item.id) {
                const service = this.services.find(s => s.service_id == item.id);
                if (service) {
                    item.price = parseFloat(service.base_fee) || 0;
                    item.layoutPrice = parseFloat(service.layout_price) || 0;
                }
            }
            this.calculateSubtotal(index);
        },
        
        calculateSubtotal(index) {
            const item = this.items[index];

            // Ensure all values are properly converted to numbers
            const quantity = parseFloat(item.quantity) || 0;
            const price = parseFloat(item.price) || 0;
            const layoutPrice = parseFloat(item.layoutPrice) || 0;

            // Step 1: Compute base price (Quantity × Unit Price)
            let baseAmount = quantity * price;

            // Step 2: Add layout fee if applicable
            let subtotal = baseAmount;
            if (item.layout && layoutPrice > 0) {
                subtotal += layoutPrice;
            }

            // Step 3: Ensure subtotal is never negative
            item.subtotal = Math.max(0, subtotal);

            // Step 4: Recalculate overall total
            this.calculateTotal();
        },
        
        calculateTotal() {
            // Update total amount using the new calculation method
            this.totalAmount = this.getFinalTotalAmount();
        },
        
        // New calculation methods for the updated quotation summary
        getTotalQuantity() {
            return this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
        },
        
        getLayoutFees() {
            return this.items.reduce((sum, item) => sum + (item.layout ? (parseFloat(item.layoutPrice) || 0) : 0), 0);
        },
        
        getSubTotal() {
            // Sub Total = (Quantity × Unit Price)
            return this.items.reduce((sum, item) => {
                const quantity = parseInt(item.quantity) || 0;
                const unitPrice = parseFloat(item.price) || 0;
                return sum + (quantity * unitPrice);
            }, 0);
        },
        
        getVATAmount() {
            // VAT Tax = Sub total × 0.12
            const subTotal = this.getSubTotal();
            return subTotal * 0.12;
        },
        
        getBaseAmount() {
            // Base Amount = Subtotal - VAT Tax
            const subTotal = this.getSubTotal();
            const vatAmount = this.getVATAmount();
            return subTotal - vatAmount;
        },
        
        getOrderDiscount() {
            // Order Discount based on quantity
            const totalQuantity = this.getTotalQuantity();
            
            // Find applicable discount rule based on quantity
            for (const rule of this.discountRules) {
                if (totalQuantity >= rule.min_quantity && (rule.max_quantity === null || totalQuantity <= rule.max_quantity)) {
                    if (rule.discount_type === 'percentage') {
                        // For percentage discount, apply to subtotal
                        const subTotal = this.getSubTotal();
                        return subTotal * (rule.discount_percentage / 100);
                    } else {
                        // For fixed amount discount, return the fixed amount
                        return rule.discount_amount;
                    }
                }
            }
            return 0;
        },
        
        getFinalTotalAmount() {
            // Final Total Amount = (Sub total - discount) + layout fee
            const subTotal = this.getSubTotal();
            const discountAmount = this.getOrderDiscount();
            const layoutFees = this.getLayoutFees();
            
            return (subTotal - discountAmount) + layoutFees;
        },

        getDiscountInfo() {
            // Get discount rule information for display
            const totalQuantity = this.getTotalQuantity();
            
            // Find applicable discount rule based on quantity
            for (const rule of this.discountRules) {
                if (totalQuantity >= rule.min_quantity && (rule.max_quantity === null || totalQuantity <= rule.max_quantity)) {
                    if (rule.discount_type === 'percentage') {
                        return `${rule.discount_percentage}% off${rule.rule_name ? ' (' + rule.rule_name + ')' : ''}`;
                    } else {
                        return `₱${rule.discount_amount.toFixed(2)} off${rule.rule_name ? ' (' + rule.rule_name + ')' : ''}`;
                    }
                }
            }
            return '';
        },
        
        init() {
            console.log('Initializing quotation form...');
            console.log('Products available:', this.products.length);
            console.log('Services available:', this.services.length);
            console.log('Customers available:', this.customers.length);
            
            // Note: No automatic item addition - admin must click "Add Item" button
        }
    }
}

// Add Customer Modal Functions
function handleCustomerSelection(select) {
    if (select.value === 'add_new') {
        openAddCustomerModal();
        // Reset to empty selection
        select.value = '';
    }
}

function openAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.remove('hidden');
}

function closeAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.add('hidden');
    document.getElementById('addCustomerForm').reset();
}

function addCustomer() {
    const form = document.getElementById('addCustomerForm');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.customers.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Add new customer to dropdown
            const select = document.getElementById('customer_id');
            const option = document.createElement('option');
            option.value = data.customer.customer_id;
            option.textContent = data.customer.display_name;
            option.selected = true;
            select.appendChild(option);
            
            // Close modal
            closeAddCustomerModal();
            
            // Show success message
            alert('Customer added successfully!');
        } else {
            let errorMessage = 'Error adding customer: ' + (data.message || 'Unknown error');
            if (data.errors) {
                errorMessage += '\nValidation errors:\n';
                for (const field in data.errors) {
                    errorMessage += `${field}: ${data.errors[field].join(', ')}\n`;
                }
            }
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding customer: ' + error.message);
    });
}

// Searchable dropdown functions
function filterCustomers(input) {
    const dropdown = document.getElementById('customer_dropdown');
    const options = dropdown.querySelectorAll('.customer-option');
    const searchTerm = input.value.toLowerCase();
    
    options.forEach(option => {
        const name = option.dataset.name.toLowerCase();
        if (name.includes(searchTerm)) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    dropdown.classList.remove('hidden');
}

function showCustomerDropdown() {
    const dropdown = document.getElementById('customer_dropdown');
    dropdown.classList.remove('hidden');
}

function hideCustomerDropdown() {
    // Delay hiding to allow clicking on options
    setTimeout(() => {
        const dropdown = document.getElementById('customer_dropdown');
        dropdown.classList.add('hidden');
    }, 200);
}

function selectCustomer(option) {
    const customerId = option.dataset.id;
    const customerName = option.dataset.name;
    
    document.getElementById('customer_id').value = customerId;
    document.getElementById('customer_search').value = customerName;
    
    const dropdown = document.getElementById('customer_dropdown');
    dropdown.classList.add('hidden');
    
    // Trigger the original customer selection handler
    handleCustomerSelection({ value: customerId });
}

function showAddCustomerModal() {
    document.getElementById('addCustomerModal').classList.remove('hidden');
}
</script>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add New Customer</h3>
                <button onclick="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="addCustomerForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_firstname" class="block text-sm font-medium text-gray-700">First Name *</label>
                        <input type="text" name="customer_firstname" id="customer_firstname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="customer_middlename" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" name="customer_middlename" id="customer_middlename"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="customer_lastname" class="block text-sm font-medium text-gray-700">Last Name *</label>
                        <input type="text" name="customer_lastname" id="customer_lastname" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                  
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-gray-700">Business Name</label>
                        <input type="text" name="business_name" id="business_name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="customer_email" id="customer_email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="contact_person1" class="block text-sm font-medium text-gray-700">Contact Person *</label>
                        <input type="text" name="contact_person1" id="contact_person1" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="contact_number1" class="block text-sm font-medium text-gray-700">Contact Number *</label>
                        <input type="text" name="contact_number1" id="contact_number1" required
                               pattern="[0-9]{11}" maxlength="11" minlength="11"
                               title="Contact number must be exactly 11 digits"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="contact_person2" class="block text-sm font-medium text-gray-700">Secondary Contact Person</label>
                        <input type="text" name="contact_person2" id="contact_person2"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="contact_number2" class="block text-sm font-medium text-gray-700">Secondary Contact Number</label>
                        <input type="text" name="contact_number2" id="contact_number2"
                               pattern="[0-9]{11}" maxlength="11" minlength="11"
                               title="Contact number must be exactly 11 digits"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                    <div>
                        <label for="tin" class="block text-sm font-medium text-gray-700">TIN</label>
                        <input type="text" name="tin" id="tin"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                    </div>
                </div>
                <div>
                    <label for="customer_address" class="block text-sm font-medium text-gray-700">Address *</label>
                    <textarea name="customer_address" id="customer_address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddCustomerModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="addCustomer()"
                            class="px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-dark transition-colors">
                        Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection