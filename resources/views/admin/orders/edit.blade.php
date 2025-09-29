@extends('layouts.admin')

@section('title', 'Edit Job Order')
@section('page-title', 'Edit Job Order #' . $order->order_id)
@section('page-description', 'Update job order details and items')

@section('content')
<div class="max-w-full mx-auto px-4">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.orders.show', $order) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Job Order #{{ $order->order_id }}</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update the details below to modify this job order
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="p-6" x-data="orderForm()" x-init="init()">
            @csrf
            @method('PUT')
            
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
                                <option value="{{ $customer->customer_id }}" {{ old('customer_id', $order->customer_id) == $customer->customer_id ? 'selected' : '' }}>
                                    {{ $customer->display_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">Assigned Employee *</label>
                        <select name="employee_id" id="employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_id') border-red-500 @enderror">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}" {{ old('employee_id', $order->employee_id) == $employee->employee_id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->job->job_title ?? 'No Position' }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="layout_employee_id" class="block text-sm font-medium text-gray-700 mb-2">Layout Designer</label>
                        <select name="layout_employee_id" id="layout_employee_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_employee_id') border-red-500 @enderror">
                            <option value="">Select Layout Designer</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->employee_id }}" {{ old('layout_employee_id', $order->layout_employee_id) == $employee->employee_id ? 'selected' : '' }}>
                                    {{ $employee->full_name }} - {{ $employee->job->job_title ?? 'No Position' }}
                                </option>
                            @endforeach
                        </select>
                        @error('layout_employee_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">Order Date *</label>
                        <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_date') border-red-500 @enderror">
                        @error('order_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="deadline_date" class="block text-sm font-medium text-gray-700 mb-2">Deadline Date *</label>
                        <input type="date" name="deadline_date" id="deadline_date" value="{{ old('deadline_date', $order->deadline_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('deadline_date') border-red-500 @enderror">
                        @error('deadline_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="order_status" class="block text-sm font-medium text-gray-700 mb-2">Order Status *</label>
                    <select name="order_status" id="order_status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('order_status') border-red-500 @enderror">
                        <option value="On-Process" {{ old('order_status', $order->order_status) == 'On-Process' ? 'selected' : '' }}>On-Process</option>
                        <option value="Designing" {{ old('order_status', $order->order_status) == 'Designing' ? 'selected' : '' }}>Designing</option>
                        <option value="Production" {{ old('order_status', $order->order_status) == 'Production' ? 'selected' : '' }}>Production</option>
                        <option value="For Releasing" {{ old('order_status', $order->order_status) == 'For Releasing' ? 'selected' : '' }}>For Releasing</option>
                        <option value="Completed" {{ old('order_status', $order->order_status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ old('order_status', $order->order_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('order_status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Order Items Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-shopping-cart mr-2 text-maroon"></i>
                        Order Items
                    </h3>
                    <div class="flex items-center space-x-4">
                        <button type="button" @click="addProductItem()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Product
                        </button>
                        <button type="button" @click="addServiceItem()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Service
                        </button>
                    </div>
                </div>

                <!-- Items Container -->
                <div class="space-y-4" x-ref="itemsContainer">
                    @foreach($order->details as $index => $detail)
                    <div class="bg-white border border-gray-200 rounded-lg p-4" x-data="{ itemType: '{{ $detail->product_id ? 'product' : 'service' }}' }">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4">
                                <span class="px-3 py-1 text-xs font-medium rounded-full" 
                                      :class="itemType === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                      x-text="itemType === 'product' ? 'Product' : 'Service'"></span>
                                <span class="text-sm text-gray-600">Item {{ $index + 1 }}</span>
                            </div>
                            <button type="button" @click="removeItem($el)" class="text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                            <!-- Item Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                <select name="items[{{ $index }}][type]" x-model="itemType" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="product">Product</option>
                                    <option value="service">Service</option>
                                </select>
                            </div>

                            <!-- Item Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                                <select name="items[{{ $index }}][id]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="">Select Item</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->product_id }}" 
                                                {{ $detail->product_id == $product->product_id ? 'selected' : '' }}
                                                x-show="itemType === 'product'">
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                    @foreach($services as $service)
                                        <option value="{{ $service->service_id }}" 
                                                {{ $detail->service_id == $service->service_id ? 'selected' : '' }}
                                                x-show="itemType === 'service'">
                                            {{ $service->service_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $detail->quantity }}" min="1" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>

                            <!-- Unit -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                                <input type="text" name="items[{{ $index }}][unit]" value="{{ $detail->unit }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>

                            <!-- Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                                <input type="text" name="items[{{ $index }}][size]" value="{{ $detail->size }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                                <input type="number" name="items[{{ $index }}][price]" value="{{ $detail->price }}" step="0.01" min="0" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                        </div>

                        <!-- Layout Options -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="items[{{ $index }}][layout]" value="1" 
                                           {{ $detail->layout ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                    <span class="ml-2 text-sm text-gray-700">Include Layout Design</span>
                                </label>
                                <div x-show="itemType === 'product'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Layout Price</label>
                                    <input type="number" name="items[{{ $index }}][layoutPrice]" value="{{ $detail->layout_price ?? 0 }}" step="0.01" min="0"
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                <div x-show="items.length === 0" class="text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No items added</h3>
                    <p class="text-gray-500 mb-6">Add products or services to this order</p>
                    <div class="flex justify-center space-x-4">
                        <button type="button" @click="addProductItem()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </button>
                        <button type="button" @click="addServiceItem()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Service
                        </button>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.orders.show', $order) }}" 
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Alpine.js for dynamic form functionality -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function orderForm() {
    return {
        items: [],
        itemCounter: {{ $order->details->count() }},
        
        init() {
            // Initialize with existing items
            this.items = @json($order->details->map(function($detail) {
                return [
                    'type' => $detail->product_id ? 'product' : 'service',
                    'id' => $detail->product_id ?: $detail->service_id,
                    'quantity' => $detail->quantity,
                    'unit' => $detail->unit,
                    'size' => $detail->size,
                    'price' => $detail->price,
                    'layout' => $detail->layout ?? false,
                    'layoutPrice' => $detail->layout_price ?? 0
                ];
            }));
        },
        
        addProductItem() {
            this.addItem('product');
        },
        
        addServiceItem() {
            this.addItem('service');
        },
        
        addItem(type) {
            const index = this.itemCounter++;
            const itemHtml = this.createItemHtml(type, index);
            this.$refs.itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        },
        
        createItemHtml(type, index) {
            const products = @json($products);
            const services = @json($services);
            
            const productOptions = products.map(product => 
                `<option value="${product.product_id}" x-show="itemType === 'product'">${product.product_name}</option>`
            ).join('');
            
            const serviceOptions = services.map(service => 
                `<option value="${service.service_id}" x-show="itemType === 'service'">${service.service_name}</option>`
            ).join('');
            
            return `
                <div class="bg-white border border-gray-200 rounded-lg p-4" x-data="{ itemType: '${type}' }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <span class="px-3 py-1 text-xs font-medium rounded-full" 
                                  :class="itemType === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                  x-text="itemType === 'product' ? 'Product' : 'Service'"></span>
                            <span class="text-sm text-gray-600">Item ${index + 1}</span>
                        </div>
                        <button type="button" @click="removeItem($el)" class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                            <select name="items[${index}][type]" x-model="itemType" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                <option value="product">Product</option>
                                <option value="service">Service</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Item</label>
                            <select name="items[${index}][id]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                <option value="">Select Item</option>
                                ${productOptions}
                                ${serviceOptions}
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                            <input type="number" name="items[${index}][quantity]" value="1" min="1" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <input type="text" name="items[${index}][unit]" value=""
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Size</label>
                            <input type="text" name="items[${index}][size]" value=""
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                            <input type="number" name="items[${index}][price]" value="0" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="items[${index}][layout]" value="1" 
                                       class="rounded border-gray-300 text-maroon focus:ring-maroon">
                                <span class="ml-2 text-sm text-gray-700">Include Layout Design</span>
                            </label>
                            <div x-show="itemType === 'product'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Layout Price</label>
                                <input type="number" name="items[${index}][layoutPrice]" value="0" step="0.01" min="0"
                                       class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },
        
        removeItem(element) {
            element.closest('.bg-white.border').remove();
        }
    }
}
</script>
@endsection
