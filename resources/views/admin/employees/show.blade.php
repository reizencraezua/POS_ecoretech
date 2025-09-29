@extends('layouts.admin')

@section('title', 'Employee Details')
@section('page-title', 'Employee Details')
@section('page-description', 'View detailed information about this employee')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.employees.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900">{{ $employee->full_name }}</h2>
                        <div class="flex items-center space-x-6 text-sm text-gray-600 mt-1">
                            <span><i class="fas fa-briefcase mr-1"></i>{{ $employee->job->job_title ?? 'No Position' }}</span>
                            <span><i class="fas fa-calendar mr-1"></i>Hired {{ $employee->hire_date->format('M d, Y') }}</span>
                            <span><i class="fas fa-envelope mr-1"></i>{{ $employee->employee_email }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Employee
                    </a>
                    <form method="POST" action="{{ route('admin.employees.destroy', $employee) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Employee Information -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">First Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->employee_firstname }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Last Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->employee_lastname }}</p>
                        </div>
                        @if($employee->employee_middlename)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Middle Name</label>
                            <p class="text-gray-900 font-medium">{{ $employee->employee_middlename }}</p>
                        </div>
                        @endif
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900 font-medium">{{ $employee->employee_email }}</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Address</label>
                        <p class="text-gray-900 font-medium">{{ $employee->employee_address }}</p>
                    </div>
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Contact Number</label>
                        <p class="text-gray-900 font-medium">{{ $employee->employee_contact }}</p>
                    </div>
                </div>
            </div>

            <!-- Job Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Job Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Position</label>
                            <p class="text-gray-900 font-medium">{{ $employee->job->job_title ?? 'No Position Assigned' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Hire Date</label>
                            <p class="text-gray-900 font-medium">{{ $employee->hire_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Employee ID</label>
                            <p class="text-gray-900 font-medium">#{{ str_pad($employee->employee_id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tenure</label>
                            <p class="text-gray-900 font-medium">{{ $employee->hire_date->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Orders</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $employee->orders->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Active Orders</span>
                            <span class="text-lg font-semibold text-blue-600">{{ $employee->orders->whereIn('order_status', ['On-Process', 'Designing', 'Production', 'For Releasing'])->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Completed Orders</span>
                            <span class="text-lg font-semibold text-green-600">{{ $employee->orders->where('order_status', 'Completed')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            @if($employee->orders->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($employee->orders->take(5) as $order)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Order #{{ $order->order_id }}</p>
                                <p class="text-xs text-gray-500">{{ $order->customer->display_name ?? 'Unknown Customer' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($order->order_status === 'Completed') bg-green-100 text-green-800
                                    @elseif($order->order_status === 'On-Process') bg-blue-100 text-blue-800
                                    @elseif($order->order_status === 'Cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $order->order_status }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($employee->orders->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.orders.index', ['employee' => $employee->employee_id]) }}" class="text-sm text-blue-600 hover:text-blue-800">
                            View all orders
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
