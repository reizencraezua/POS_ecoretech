@extends('layouts.admin')

@section('title', 'Create Job Order')
@section('page-title', 'Create New Job Order')
@section('page-description', 'Create a new job order for a customer')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Create Job Order</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Fill in the details below to create a new job order
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.orders.store') }}" class="p-6" x-data="orderForm()" x-init="init()" @submit="if (!validateDownpayment()) { $event.preventDefault(); } else { console.log('Form submitted', $data); }">
            @csrf
            
            <!-- Order Information Section -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-clipboard-list mr-2 text-maroon"></i>
                    Order Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                        <select name="customer_id" id="customer_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('customer_id') border-red-500 @enderror">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                    {{ $customer->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Production Employee *</label>
                        <select name="employee_id" id="employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_id') border-red-500 @enderror">
                            <option value="">Select Production Employee</option>
                            @foreach($employees as $employee)
                                @if($employee->job && $employee->job->job_title && 
                                    (stripos($employee->job->job_title, 'production') !== false || 
                                     stripos($employee->job->job_title, 'operator') !== false ||
                                     stripos($employee->job->job_title, 'supervisor') !== false ||
                                     stripos($employee->job->job_title, 'manager') !== false))
                                    <option value="{{ $employee->employee_id }}" {{ old('employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->job->job_title }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="layout_employee_id" class="block text-sm font-medium text-gray-700 mb-2">Graphics Designer</label>
                        <select name="layout_employee_id" id="layout_employee_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_employee_id') border-red-500 @enderror">
                            <option value="">Select Graphics Designer (Optional)</option>
                            @foreach($employees as $employee)
                                @if($employee->job && $employee->job->job_title && 
                                    (stripos($employee->job->job_title, 'design') !== false || 
                                     stripos($employee->job->job_title, 'graphics') !== false ||
                                     stripos($employee->job->job_title, 'layout') !== false ||
                                     stripos($employee->job->job_title, 'artist') !== false))
                                    <option value="{{ $employee->employee_id }}" {{ old('layout_employee_id') == $employee->employee_id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->job->job_title }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('layout_employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date *</label>
                        <input type="date" name="order_date" id="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_date') border-red-500 @enderror">
                        @error('order_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="deadline_date" class="block text-sm font-medium text-gray-700 mb-2">Deadline Date *</label>
                        <input type="date" name="deadline_date" id="deadline_date" value="{{ old('deadline_date') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('deadline_date') border-red-500 @enderror">
                        @error('deadline_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-box mr-2 text-maroon"></i>
                            Order Items
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
                                        <input type="text" x-model="item.unit" :name="`items[${index}][unit]`" 
                                               class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                               placeholder="Pcs" value="Pcs">
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
                                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box text-4xl mb-2"></i>
                                    <p>No items added yet. Click "Add Item" to start.</p>
                                </td>
                            </tr>
                        </tbody>
                       
                    </table>
                </div>
                </div>
            </div>

            <!-- Layout Design Section -->
           
            <!-- Order Summary and Payment Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-calculator mr-2 text-maroon"></i>
                            Order Summary
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- No. of Items -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">No. of items: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="getTotalQuantity()"></span>
                            </div>


                            <!-- Base Amount -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Base Amount: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getBaseAmount().toFixed(2)"></span>
                            </div>

                            <!-- VAT Tax -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">VAT Tax (12%): </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getVATAmount().toFixed(2)"></span>
                            </div>

                            <!-- Sub Total -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Sub Total: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getSubTotal().toFixed(2)"></span>
                            </div>
                            
                            <!-- Order Discount -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Order Discount:</span>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600" x-text="'-₱' + getOrderDiscount().toFixed(2)"></div>
                                    <div class="text-xs text-gray-500" x-text="getDiscountInfo()"></div>
                                </div>
                            </div>
                            
                            
                            <!-- Layout Fees -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Layout Fees: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getLayoutFees().toFixed(2)"></span>
                            </div>

                            <!-- Final Total Amount -->
                            <div class="flex justify-between items-center py-3 border-t-2 border-maroon">
                                <span class="text-lg font-semibold text-gray-900">TOTAL AMOUNT: </span>
                                <span class="text-xl font-bold text-maroon" x-text="'₱' + getFinalTotalAmount().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-maroon"></i>
                            Payment Information
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Optional initial payment</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" name="payment[payment_date]" value="{{ now()->format('Y-m-d') }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment[payment_method]" id="payment_method"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">Select Method</option>
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Check">Check</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                    
                    <div id="reference_number_field" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reference Number</label>
                        <input type="text" name="payment[reference_number]" id="reference_number"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                               placeholder="Enter reference number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Term</label>
                        <select name="payment[payment_term]" id="payment_term" @change="toggleDownpaymentInfo()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            <option value="">Select Term</option>
                            <option value="Downpayment">Downpayment</option>
                            <option value="Initial">Initial</option>
                            <option value="Full">Full</option>
                        </select>
                    </div>
                    
                    <!-- Downpayment Information -->
                    <div id="downpayment_info" class="md:col-span-2 mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm" style="display: none;">
                        <div class="flex items-center text-blue-700 mb-2">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span class="font-medium">Downpayment Information</span>
                        </div>
                        <div class="text-blue-800">
                            <div class="flex justify-between items-center">
                                <span>Total Amount:</span>
                                <span id="final_total_amount" class="font-semibold">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span>Required Downpayment (50%):</span>
                                <span id="downpayment_amount_display" class="font-bold text-lg">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" step="0.01" min="0" name="payment[amount_paid]"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                               placeholder="0.00">
                    </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                <textarea name="payment[remarks]" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                          placeholder="Optional notes for this payment..."></textarea>
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
                            Review all details before creating the order
                        </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Create Job Order
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</div>

<script>
function orderForm() {
    return {
        items: [],
        totalAmount: 0,
        products: @json($products),
        services: @json($services),
        employees: @json($employees),
        customers: @json($customers),
        discountRules: @json($discountRules),
        
        
        addItem() {
            console.log('Adding new item...');
            this.items.push({
                type: '',
                id: '',
                quantity: 1,
                unit: 'Pcs',
                size: '',
                price: 0,
                layout: false,
                layoutPrice: 0,
                subtotal: 0
            });
            console.log('Items count:', this.items.length);
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
            
            // Step 5: Update graphics designer availability
            this.updateGraphicsDesignerAvailability();
        },
        
        calculateTotal() {
            // Update total amount using the new calculation method
            this.totalAmount = this.getFinalTotalAmount();
        },
        
        groupItemsByProduct() {
            const groups = {};
            this.items.forEach(item => {
                if (item.type === 'product' && item.id) {
                    if (!groups[item.id]) {
                        groups[item.id] = [];
                    }
                    groups[item.id].push(item);
                }
            });
            return groups;
        },
        
        calculateProductDiscount(subtotal, quantity) {
            for (const rule of this.discountRules) {
                if (quantity >= rule.min_quantity && (rule.max_quantity === null || quantity <= rule.max_quantity)) {
                    if (rule.discount_type === 'percentage') {
                        return subtotal * (rule.discount_percentage / 100);
                    } else {
                        return rule.discount_amount;
                    }
                }
            }
            return 0;
        },
        
        distributeDiscountToItems(items, totalDiscount, totalSubtotal) {
            if (totalDiscount <= 0 || totalSubtotal <= 0) return;
            
            items.forEach(item => {
                const proportion = item.subtotal / totalSubtotal;
                const itemDiscount = totalDiscount * proportion;
                item.subtotal = Math.max(0, item.subtotal - itemDiscount);
            });
        },
        
        getProductDiscounts() {
            const productGroups = this.groupItemsByProduct();
            let totalDiscount = 0;
            
            for (const [productId, items] of Object.entries(productGroups)) {
                const totalQuantity = items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                const totalSubtotal = items.reduce((sum, item) => {
                    const baseAmount = (parseInt(item.quantity) || 0) * (parseFloat(item.price) || 0);
                    const layoutPrice = item.layout ? (parseFloat(item.layoutPrice) || 0) : 0;
                    return sum + baseAmount;
                }, 0);
                
                const discount = this.calculateProductDiscount(totalSubtotal, totalQuantity);
                totalDiscount += discount;
            }
            
            return totalDiscount;
        },
        
        getItemsSubtotal() {
            // Calculate items subtotal (items + layout fees - product discounts)
            const itemsTotal = this.items.reduce((sum, item) => {
                const baseAmount = (parseInt(item.quantity) || 0) * (parseFloat(item.price) || 0);
                const layoutPrice = item.layout ? (parseFloat(item.layoutPrice) || 0) : 0;
                return sum + baseAmount + layoutPrice;
            }, 0);
            
            const productDiscounts = this.getProductDiscounts();
            return Math.max(0, itemsTotal - productDiscounts);
        },
        
        // New calculation methods for the updated order summary
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
        
        getFinalTotalAmount() {
            // Final Total Amount = (Sub total - discount) + layout fee
            const subTotal = this.getSubTotal();
            const discountAmount = this.getOrderDiscount();
            const layoutFees = this.getLayoutFees();
            
            return (subTotal - discountAmount) + layoutFees;
        },
        
        toggleDownpaymentInfo() {
            const paymentTermSelect = document.getElementById('payment_term');
            const downpaymentInfo = document.getElementById('downpayment_info');
            
            if (paymentTermSelect && downpaymentInfo) {
                const selectedTerm = paymentTermSelect.value;
                
                if (selectedTerm === 'Downpayment') {
                    const finalTotalAmount = this.getFinalTotalAmount();
                    const expectedDownpayment = finalTotalAmount * 0.5;
                    
                    // Update display elements
                    document.getElementById('final_total_amount').textContent = `₱${finalTotalAmount.toFixed(2)}`;
                    document.getElementById('downpayment_amount_display').textContent = `₱${expectedDownpayment.toFixed(2)}`;
                    
                    downpaymentInfo.style.display = 'block';
                } else {
                    downpaymentInfo.style.display = 'none';
                }
            }
        },

        validateDownpayment() {
            const paymentTermSelect = document.getElementById('payment_term');
            const amountPaidInput = document.querySelector('input[name="payment[amount_paid]"]');
            
            if (amountPaidInput) {
                const finalTotalAmount = this.getFinalTotalAmount();
                const amountPaid = parseFloat(amountPaidInput.value) || 0;
                
                // Check if payment amount exceeds total amount
                if (amountPaid > finalTotalAmount) {
                    alert(`Payment amount cannot exceed the total amount of ₱${finalTotalAmount.toFixed(2)}. Current amount: ₱${amountPaid.toFixed(2)}`);
                    return false;
                }
                
                // Check downpayment validation
                if (paymentTermSelect && paymentTermSelect.value === 'Downpayment') {
                    const requiredDownpayment = finalTotalAmount * 0.5;
                    
                    if (amountPaid < requiredDownpayment) {
                        alert(`Downpayment must be at least 50% of the total amount (₱${requiredDownpayment.toFixed(2)}). Current amount: ₱${amountPaid.toFixed(2)}`);
                        return false;
                    }
                }
            }
            return true;
        },
        
        
        updateGraphicsDesignerAvailability() {
            const graphicsDesignerSelect = document.getElementById('layout_employee_id');
            if (!graphicsDesignerSelect) return;
            
            // Check if any layout checkbox is checked
            const hasLayoutChecked = this.hasAnyLayoutChecked();
            
            if (hasLayoutChecked) {
                graphicsDesignerSelect.disabled = false;
                graphicsDesignerSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                graphicsDesignerSelect.classList.add('bg-white');
            } else {
                graphicsDesignerSelect.disabled = true;
                graphicsDesignerSelect.value = ''; // Clear selection
                graphicsDesignerSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
                graphicsDesignerSelect.classList.remove('bg-white');
            }
        },
        
        hasAnyLayoutChecked() {
            // Check new item layout checkboxes (Alpine.js items)
            return this.items.some(item => item.layout);
        },
        
        init() {
            console.log('Initializing order form...');
            console.log('Products available:', this.products.length);
            console.log('Services available:', this.services.length);
            console.log('Customers available:', this.customers.length);
            console.log('Employees available:', this.employees.length);
            
            // Removed automatic item addition - users must click "Add Item" button
            // Removed automatic production employee assignment - users must select manually
            
            // Update graphics designer availability based on initial state
            this.updateGraphicsDesignerAvailability();
        }
    }
}

// Handle reference number field visibility
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method');
    const referenceNumberField = document.getElementById('reference_number_field');
    const referenceNumberInput = document.getElementById('reference_number');
    
    paymentMethodSelect.addEventListener('change', function() {
        const selectedMethod = this.value;
        
        if (selectedMethod === 'GCash' || selectedMethod === 'Bank Transfer') {
            referenceNumberField.style.display = 'block';
            referenceNumberInput.required = true;
        } else {
            referenceNumberField.style.display = 'none';
            referenceNumberInput.required = false;
            referenceNumberInput.value = ''; // Clear the field when hidden
        }
    });
    
    // Downpayment functionality is now handled by Alpine.js @change="toggleDownpaymentInfo()"
});
</script>
@endsection
