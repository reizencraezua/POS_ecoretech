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
        
        <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="p-6" x-data="orderForm()" x-init="init()" @submit="if (!validateDownpayment()) { $event.preventDefault(); } else { console.log('Form submitted', $data); }">
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
                                <option value="{{ $employee->employee_id }}" {{ old('employee_id', $order->employee_id) == $employee->employee_id ? 'selected' : '' }}>
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
                                <option value="{{ $employee->employee_id }}" {{ old('layout_employee_id', $order->layout_employee_id) == $employee->employee_id ? 'selected' : '' }}>
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
                        <option value="Completed" {{ old('order_status', $order->order_status) == 'Completed' ? 'selected' : '' }} {{ !$order->isFullyPaid() ? 'disabled' : '' }}>Completed{{ !$order->isFullyPaid() ? ' (Must be fully paid)' : '' }}</option>
                        <option value="Cancelled" {{ old('order_status', $order->order_status) == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('order_status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
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
                            <!-- Existing Order Items (Static) -->
                    @foreach($order->details as $index => $detail)
                            <tr>
                                <td class="px-4 py-4">
                                    <select name="items[{{ $index }}][type]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                        <option value="">Select Type</option>
                                        <option value="product" {{ $detail->product_id ? 'selected' : '' }}>Product</option>
                                        <option value="service" {{ $detail->service_id ? 'selected' : '' }}>Service</option>
                                </select>
                                </td>
                                <td class="px-4 py-4">
                                <select name="items[{{ $index }}][id]" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    <option value="">Select Item</option>
                                    @foreach($products as $product)
                                            <option value="{{ $product->product_id }}" {{ $detail->product_id == $product->product_id ? 'selected' : '' }}>
                                                {{ $product->product_name }} - ₱{{ $product->base_price }}
                                        </option>
                                    @endforeach
                                    @foreach($services as $service)
                                            <option value="{{ $service->service_id }}" {{ $detail->service_id == $service->service_id ? 'selected' : '' }}>
                                                {{ $service->service_name }} - ₱{{ $service->base_fee }}
                                        </option>
                                    @endforeach
                                </select>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $detail->quantity }}" min="1" required 
                                           class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                           oninput="recalculateOrderSummary()">
                                </td>
                                <td class="px-4 py-4">
                                    <select name="items[{{ $index }}][unit_id]" required
                                            class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                            onchange="recalculateOrderSummary()">
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->unit_id }}" {{ $detail->unit_id == $unit->unit_id ? 'selected' : '' }}>
                                                {{ $unit->unit_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <select name="items[{{ $index }}][size]" 
                                            class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
                                            onchange="recalculateOrderSummary()">
                                        <option value="">Select Size</option>
                                        @if($detail->product_id)
                                            @php
                                                $product = $products->firstWhere('product_id', $detail->product_id);
                                                $availableSizes = $product ? $product->category->sizes : collect();
                                            @endphp
                                            @foreach($availableSizes as $size)
                                                <option value="{{ $size->size_name }}" {{ $detail->size == $size->size_name ? 'selected' : '' }}>
                                                    {{ $size->size_name }}
                                                </option>
                                            @endforeach
                                        @elseif($detail->service_id)
                                            @php
                                                $service = $services->firstWhere('service_id', $detail->service_id);
                                                $availableSizes = $service ? $service->category->sizes : collect();
                                            @endphp
                                            @foreach($availableSizes as $size)
                                                <option value="{{ $size->size_name }}" {{ $detail->size == $size->size_name ? 'selected' : '' }}>
                                                    {{ $size->size_name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" name="items[{{ $index }}][price]" value="{{ $detail->price }}" step="0.01" min="0" required readonly
                                           class="w-32 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-700 cursor-not-allowed"
                                           placeholder="0.00">
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" name="items[{{ $index }}][layout]" value="1" {{ $detail->layout ? 'checked' : '' }}
                                               class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded"
                                               onchange="recalculateOrderSummary()">
                                        <span class="text-xs text-gray-600">{{ $detail->layout_price > 0 ? '₱' . number_format($detail->layout_price, 2) : '' }}</span>
                                        <input type="hidden" name="items[{{ $index }}][layoutPrice]" value="{{ $detail->layout_price ?? 0 }}">
                                    </div>
                                </td>
                                
                                <td class="px-4 py-4">
                                    <span class="text-gray-400 text-sm">Cannot delete</span>
                                </td>
                            </tr>
                            @endforeach
                            
                            <!-- Dynamic Items (Alpine.js) -->
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-4">
                                        <select x-model="item.type" @change="updateItemOptions(index)" :name="`items[${index + {{ $order->details->count() }} }][type]`" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Type</option>
                                            <option value="product">Product</option>
                                            <option value="service">Service</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.id" @change="updatePrice(index)" :name="`items[${index + {{ $order->details->count() }} }][id]`" required
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
                                        <input type="number" x-model="item.quantity" @input="calculateSubtotal(index)" :name="`items[${index + {{ $order->details->count() }} }][quantity]`" 
                                               min="1" required class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.unit_id" :name="`items[${index + {{ $order->details->count() }} }][unit_id]`" required
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Unit</option>
                                            <template x-for="unit in units">
                                                <option :value="unit.unit_id" x-text="unit.unit_name"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <select x-model="item.size" :name="`items[${index + {{ $order->details->count() }} }][size]`" 
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                                            <option value="">Select Size</option>
                                            <template x-if="item.type === 'product' && item.id">
                                                <template x-for="size in getAvailableSizes(item.id)">
                                                    <option :value="size.size_name" x-text="size.size_name"></option>
                                                </template>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="px-4 py-4">
                                        <input type="number" x-model="item.price" @input="calculateSubtotal(index)" :name="`items[${index + {{ $order->details->count() }} }][price]`" 
                                               step="0.01" min="0" required class="w-32 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
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
                            
                            <tr x-show="items.length === 0 && {{ $order->details->count() }} === 0">
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-box text-4xl mb-2"></i>
                                    <p>No items added yet. Click "Add Item" to start.</p>
                                </td>
                            </tr>
                        </tbody>
                       
                    </table>
                </div>
                </div>
            </div>

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
                                <span class="text-sm font-medium text-gray-900" x-text="getTotalQuantity()" data-summary="items-count"></span>
                            </div>


                            <!-- Base Amount -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Base Amount: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getBaseAmount().toFixed(2)" data-summary="base-amount"></span>
                            </div>

                            <!-- VAT Tax -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">VAT Tax (12%): </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getVATAmount().toFixed(2)" data-summary="vat-amount"></span>
                            </div>

                            <!-- Sub Total -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Sub Total: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getSubTotal().toFixed(2)" data-summary="sub-total"></span>
                            </div>
                            
                            <!-- Order Discount -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Order Discount:</span>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-green-600" x-text="'-₱' + getOrderDiscount().toFixed(2)" data-summary="discount-amount"></div>
                                    <div class="text-xs text-gray-500" x-text="getDiscountInfo()"></div>
                                </div>
                            </div>
                            
                            
                            <!-- Layout Fees -->
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Layout Fees: </span>
                                <span class="text-sm font-medium text-gray-900" x-text="'₱' + getLayoutFees().toFixed(2)" data-summary="layout-fees"></span>
                            </div>

                            <!-- Final Total Amount -->
                            <div class="flex justify-between items-center py-3 border-t-2 border-maroon">
                                <span class="text-lg font-semibold text-gray-900">TOTAL AMOUNT: </span>
                                <span class="text-xl font-bold text-maroon" x-text="'₱' + getFinalTotalAmount().toFixed(2)" data-summary="total-amount"></span>
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
                        <p class="text-sm text-gray-600 mt-1">Add additional payment to this order</p>
                    </div>
                    <div class="p-6">
                        <!-- Existing Payments Section -->
                        @if($order->payments->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-history mr-2 text-maroon"></i>
                                Existing Payments
                            </h4>
                            <div class="space-y-3">
                                @foreach($order->payments as $payment)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-green-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-lg font-bold text-maroon">₱{{ number_format($payment->amount_paid, 2) }}</p>
                                                <p class="text-xs text-gray-500">{{ $payment->payment_date->format('M d, Y g:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $payment->payment_term }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">{{ $payment->payment_method }}</p>
                                        </div>
                                    </div>
                                    
                                    @if($payment->reference_number)
                                    <div class="flex items-center space-x-2 text-xs text-gray-600 mb-2">
                                        <i class="fas fa-hashtag"></i>
                                        <span>Reference: {{ $payment->reference_number }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($payment->remarks)
                                    <div class="text-xs text-gray-600 bg-white rounded p-2 border">
                                        <i class="fas fa-comment mr-1"></i>
                                        {{ $payment->remarks }}
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                                
                                <!-- Payment Summary -->
                                <div class="bg-maroon text-white rounded-lg p-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold">Total Paid:</span>
                                        <span class="text-xl font-bold" data-payment="total-paid">₱{{ number_format($order->payments->sum('amount_paid'), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-sm opacity-90">Remaining Balance:</span>
                                        <span class="text-lg font-semibold" data-payment="remaining-balance">₱{{ number_format($order->remaining_balance, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
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
                            <div id="existing_payments_info" class="mt-2 pt-2 border-t border-blue-300">
                                <!-- Existing payments info will be populated by JavaScript -->
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
                            Review all details before updating the order
                </div>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.orders.show', $order) }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                                <i class="fas fa-save mr-2"></i>
                                Update Job Order
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
        units: @json($units),
        discountRules: @json($discountRules),
        
        addItem() {
            console.log('Adding new item...');
            this.items.push({
                type: '',
                id: '',
                quantity: 1,
                unit_id: '',
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

        getAvailableSizes(productId) {
            const product = this.products.find(p => p.product_id == productId);
            if (!product || !product.category) return [];
            
            return product.category.sizes || [];
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
            
            // Update downpayment information if payment term is set to downpayment
            this.updateDownpaymentInfo();
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
            // Sum of all quantities from existing items + new items
            const existingQuantity = {{ $order->details->sum('quantity') }};
            const newQuantity = this.items.reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
            return existingQuantity + newQuantity;
        },

        getLayoutFees() {
            // Calculate layout fees from existing items + new items
            // For existing items, we need to check if layout checkbox is checked
            let existingLayoutFees = 0;
            
            // Only target checkboxes that are NOT part of the Alpine.js items (existing items only)
            // These are the checkboxes that don't have x-model attribute
            const allLayoutCheckboxes = document.querySelectorAll('input[name*="items["][name*="][layout]"]');
            const existingLayoutCheckboxes = Array.from(allLayoutCheckboxes).filter(checkbox => !checkbox.hasAttribute('x-model'));
            
            existingLayoutCheckboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    // Look for the layout price input in the same parent container
                    const layoutPriceInput = checkbox.parentElement.querySelector('input[name*="layoutPrice"]');
                    if (layoutPriceInput) {
                        const price = parseFloat(layoutPriceInput.value) || 0;
                        existingLayoutFees += price;
                    }
                }
            });
            
            // For new items, only include if layout is checked
            const newLayoutFees = this.items.reduce((sum, item) => sum + (item.layout ? (parseFloat(item.layoutPrice) || 0) : 0), 0);
            return existingLayoutFees + newLayoutFees;
        },
        
        getSubTotal() {
            // Sub Total = (Quantity × Unit Price) - does NOT include layout fees
            // Include existing items + new items
            const existingTotal = {{ $order->details->sum(function($detail) { return $detail->quantity * $detail->price; }) }};
            const newTotal = this.items.reduce((sum, item) => {
                const quantity = parseInt(item.quantity) || 0;
                const unitPrice = parseFloat(item.price) || 0;
                return sum + (quantity * unitPrice);
            }, 0);
            return existingTotal + newTotal;
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
            // Final Total Amount = (Sub total - discount) + layout fees (only if checkboxes are checked)
            const subTotal = this.getSubTotal();
            const discountAmount = this.getOrderDiscount();
            const layoutFees = this.getLayoutFees();
            
            return (subTotal - discountAmount) + layoutFees;
        },
        
        updateDownpaymentInfo() {
            const paymentTermSelect = document.getElementById('payment_term');
            const downpaymentInfo = document.getElementById('downpayment_info');
            
            if (paymentTermSelect && downpaymentInfo) {
                const selectedTerm = paymentTermSelect.value;
                
                if (selectedTerm === 'Downpayment') {
                    const finalTotalAmount = this.getFinalTotalAmount();
                    const existingPayments = {{ $order->payments->sum('amount_paid') }};
                    const remainingBalance = finalTotalAmount - existingPayments;
                    const expectedDownpayment = finalTotalAmount * 0.5;
                    const remainingDownpayment = Math.max(0, expectedDownpayment - existingPayments);
                    
                    // Update display elements
                    document.getElementById('final_total_amount').textContent = `₱${finalTotalAmount.toFixed(2)}`;
                    document.getElementById('downpayment_amount_display').textContent = `₱${remainingDownpayment.toFixed(2)}`;
                    
                    // Add additional info about existing payments
                    const existingPaymentsInfo = document.getElementById('existing_payments_info');
                    if (existingPaymentsInfo) {
                        existingPaymentsInfo.innerHTML = `
                            <div class="flex justify-between items-center text-xs text-blue-600 mb-1">
                                <span>Already Paid:</span>
                                <span>₱${existingPayments.toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs text-blue-600">
                                <span>Remaining Balance:</span>
                                <span>₱${remainingBalance.toFixed(2)}</span>
                            </div>
                        `;
                    }
                    
                    downpaymentInfo.style.display = 'block';
                } else {
                    downpaymentInfo.style.display = 'none';
                }
            }
        },

        toggleDownpaymentInfo() {
            this.updateDownpaymentInfo();
        },

        validateDownpayment() {
            const paymentTermSelect = document.getElementById('payment_term');
            const amountPaidInput = document.querySelector('input[name="payment[amount_paid]"]');
            
            if (amountPaidInput) {
                const finalTotalAmount = this.getFinalTotalAmount();
                const existingPayments = {{ $order->payments->sum('amount_paid') }};
                const remainingBalance = finalTotalAmount - existingPayments;
                const amountPaid = parseFloat(amountPaidInput.value) || 0;
                
                // Check if payment amount exceeds remaining balance
                if (amountPaid > remainingBalance) {
                    alert(`Payment amount cannot exceed the remaining balance of ₱${remainingBalance.toFixed(2)}. Current amount: ₱${amountPaid.toFixed(2)}`);
                    return false;
                }
                
                // Check downpayment validation
                if (paymentTermSelect && paymentTermSelect.value === 'Downpayment') {
                    const expectedDownpayment = finalTotalAmount * 0.5;
                    const remainingDownpayment = Math.max(0, expectedDownpayment - existingPayments);
                    const totalAfterPayment = existingPayments + amountPaid;
                    
                    if (totalAfterPayment < expectedDownpayment) {
                        alert(`Downpayment must be at least 50% of the total amount (₱${expectedDownpayment.toFixed(2)}). You need to pay at least ₱${remainingDownpayment.toFixed(2)} more. Current amount: ₱${amountPaid.toFixed(2)}`);
                        return false;
                    }
                }
            }
            return true;
        },
        
        init() {
            console.log('Initializing order form...');
            console.log('Products available:', this.products.length);
            console.log('Services available:', this.services.length);
            console.log('Customers available:', this.customers.length);
            console.log('Employees available:', this.employees.length);
            
            // Initialize empty items array for new items only
            this.items = [];
            
            // Set up event listeners for existing item inputs first
            this.setupExistingItemListeners();
            
            // Calculate initial totals after listeners are set up
            this.calculateTotal();
            
            // Update graphics designer availability based on initial state
            this.updateGraphicsDesignerAvailability();
        },
        
        setupExistingItemListeners() {
            // Add change listeners to existing item inputs for real-time calculation
            const existingItemInputs = document.querySelectorAll('input[name*="items["][name*="][quantity]"], input[name*="items["][name*="][price]"]');
            existingItemInputs.forEach(input => {
                input.addEventListener('input', () => {
                    this.calculateTotal();
                    this.updateDownpaymentInfo();
                });
            });
            
            // Add change listeners to existing item checkboxes
            const existingItemCheckboxes = document.querySelectorAll('input[name*="items["][name*="][layout]"]');
            existingItemCheckboxes.forEach((checkbox, index) => {
                checkbox.addEventListener('change', () => {
                    this.calculateTotal();
                    this.updateDownpaymentInfo();
                    this.updateGraphicsDesignerAvailability();
                });
            });
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
            // Check existing item layout checkboxes
            const existingLayoutCheckboxes = document.querySelectorAll('input[name*="items["][name*="][layout]"]');
            for (let checkbox of existingLayoutCheckboxes) {
                if (checkbox.checked) return true;
            }
            
            // Check new item layout checkboxes (Alpine.js items)
            return this.items.some(item => item.layout);
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
    
});

// Function to recalculate order summary when existing items are modified
function recalculateOrderSummary() {
    // Get all existing order items
    const existingItems = [];
    const itemRows = document.querySelectorAll('tbody tr');
    
    itemRows.forEach((row, index) => {
        // Skip Alpine.js dynamic items (they have x-model attributes)
        const hasAlpineModel = row.querySelector('[x-model]');
        if (hasAlpineModel) return;
        
        const typeSelect = row.querySelector('select[name*="[type]"]');
        const idSelect = row.querySelector('select[name*="[id]"]');
        const quantityInput = row.querySelector('input[name*="[quantity]"]');
        const priceInput = row.querySelector('input[name*="[price]"]');
        const layoutCheckbox = row.querySelector('input[name*="[layout]"]');
        const layoutPriceInput = row.querySelector('input[name*="[layoutPrice]"]');
        
        if (typeSelect && idSelect && quantityInput && priceInput) {
            const item = {
                type: typeSelect.value,
                id: idSelect.value,
                quantity: parseFloat(quantityInput.value) || 0,
                price: parseFloat(priceInput.value) || 0,
                layout: layoutCheckbox ? layoutCheckbox.checked : false,
                layoutPrice: layoutPriceInput ? parseFloat(layoutPriceInput.value) || 0 : 0
            };
            existingItems.push(item);
        }
    });
    
    // Calculate totals
    let totalQuantity = 0;
    let baseAmount = 0;
    let layoutFees = 0;
    
    existingItems.forEach(item => {
        totalQuantity += item.quantity;
        baseAmount += item.quantity * item.price;
        if (item.layout) {
            layoutFees += item.layoutPrice;
        }
    });
    
    // Add new items from Alpine.js if available
    if (window.Alpine && window.Alpine.store && window.Alpine.store('orderForm')) {
        const alpineData = window.Alpine.store('orderForm');
        if (alpineData && alpineData.items) {
            alpineData.items.forEach(item => {
                totalQuantity += parseFloat(item.quantity) || 0;
                baseAmount += (parseFloat(item.quantity) || 0) * (parseFloat(item.price) || 0);
                if (item.layout) {
                    layoutFees += parseFloat(item.layoutPrice) || 0;
                }
            });
        }
    }
    
    // Calculate using the correct formula
    // Formula 1: Sub Total = (Quantity × Unit Price)
    const subTotal = baseAmount;
    
    // Formula 2: VAT Tax = Sub Total × 0.12
    const vatAmount = subTotal * 0.12;
    
    // Formula 3: Base Amount = Sub Total - VAT
    const baseAmountAfterVAT = subTotal - vatAmount;
    
    // Formula 4: Calculate discount based on total quantity
    let discountAmount = 0;
    const discountRules = @json($discountRules);
    for (const rule of discountRules) {
        if (totalQuantity >= rule.min_quantity && (rule.max_quantity === null || totalQuantity <= rule.max_quantity)) {
            if (rule.discount_type === 'percentage') {
                discountAmount = subTotal * (rule.discount_percentage / 100);
            } else {
                discountAmount = rule.discount_amount;
            }
            break;
        }
    }
    
    // Formula 5: Final Total Amount = (Sub Total - Discount Amount) + layout fees
    const finalTotal = (subTotal - discountAmount) + layoutFees;
    
    // Update the order summary display
    updateOrderSummaryDisplay({
        totalQuantity: totalQuantity,
        baseAmount: baseAmountAfterVAT,
        vatAmount: vatAmount,
        subTotal: subTotal,
        discountAmount: discountAmount,
        layoutFees: layoutFees,
        finalTotal: finalTotal
    });
    
    // Update payment information
    updatePaymentInformation(finalTotal);
}

// Function to update the order summary display
function updateOrderSummaryDisplay(totals) {
    // Update No. of items
    const itemsCountElement = document.querySelector('[data-summary="items-count"]');
    if (itemsCountElement) {
        itemsCountElement.textContent = totals.totalQuantity;
    }
    
    // Update Base Amount
    const baseAmountElement = document.querySelector('[data-summary="base-amount"]');
    if (baseAmountElement) {
        baseAmountElement.textContent = '₱' + totals.baseAmount.toFixed(2);
    }
    
    // Update VAT
    const vatElement = document.querySelector('[data-summary="vat-amount"]');
    if (vatElement) {
        vatElement.textContent = '₱' + totals.vatAmount.toFixed(2);
    }
    
    // Update Sub Total
    const subTotalElement = document.querySelector('[data-summary="sub-total"]');
    if (subTotalElement) {
        subTotalElement.textContent = '₱' + totals.subTotal.toFixed(2);
    }
    
    // Update Order Discount
    const discountElement = document.querySelector('[data-summary="discount-amount"]');
    if (discountElement) {
        discountElement.textContent = '₱' + totals.discountAmount.toFixed(2);
    }
    
    // Update Layout Fees
    const layoutFeesElement = document.querySelector('[data-summary="layout-fees"]');
    if (layoutFeesElement) {
        layoutFeesElement.textContent = '₱' + totals.layoutFees.toFixed(2);
    }
    
    // Update Total Amount
    const totalAmountElement = document.querySelector('[data-summary="total-amount"]');
    if (totalAmountElement) {
        totalAmountElement.textContent = '₱' + totals.finalTotal.toFixed(2);
    }
}

// Function to update payment information
function updatePaymentInformation(newTotalAmount) {
    // Get existing total paid amount (this doesn't change when items are modified)
    const totalPaidElement = document.querySelector('[data-payment="total-paid"]');
    const remainingBalanceElement = document.querySelector('[data-payment="remaining-balance"]');
    
    if (totalPaidElement && remainingBalanceElement) {
        // Extract the current total paid amount from the existing text
        const currentTotalPaidText = totalPaidElement.textContent;
        const totalPaidAmount = parseFloat(currentTotalPaidText.replace('₱', '').replace(/,/g, '')) || 0;
        
        // Calculate new remaining balance
        const newRemainingBalance = newTotalAmount - totalPaidAmount;
        
        // Update remaining balance display
        remainingBalanceElement.textContent = '₱' + newRemainingBalance.toFixed(2);
        
        // Update the color based on balance
        if (newRemainingBalance <= 0) {
            remainingBalanceElement.className = 'text-lg font-semibold text-green-300';
        } else {
            remainingBalanceElement.className = 'text-lg font-semibold';
        }
    }
}
</script>
@endsection
