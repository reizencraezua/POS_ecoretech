@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Business Reports')
@section('page-description', 'Comprehensive business analytics and reporting')

@section('content')
<div class="space-y-6">
    <!-- Report Categories -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Sales Report -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Sales Report</h3>
                        <p class="text-sm text-gray-600">Monthly & yearly sales</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Track revenue trends, top products, and customer insights.</p>
                <a href="{{ route('admin.reports.sales') }}" class="w-full bg-green-50 hover:bg-green-100 text-green-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>View Report
                </a>
            </div>
        </div>

        <!-- Income Statement -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calculator text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Income Statement</h3>
                        <p class="text-sm text-gray-600">Profit & loss analysis</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Comprehensive financial performance overview.</p>
                <a href="{{ route('admin.reports.income') }}" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>View Report
                </a>
            </div>
        </div>

        <!-- Aging Report -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Aging Report</h3>
                        <p class="text-sm text-gray-600">Outstanding balances</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Track overdue payments and collections.</p>
                <a href="{{ route('admin.reports.aging') }}" class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center">
                    <i class="fas fa-eye mr-2"></i>View Report
                </a>
            </div>
        </div>

        <!-- Custom Report -->
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="p-6">
                <div class="flex items-center space-x-4 mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cog text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Custom Report</h3>
                        <p class="text-sm text-gray-600">Build custom analytics</p>
                    </div>
                </div>
                <p class="text-sm text-gray-500 mb-4">Create tailored reports for specific needs.</p>
                <button class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 px-4 py-2 rounded-lg transition-colors inline-flex items-center justify-center" disabled>
                    <i class="fas fa-tools mr-2"></i>Coming Soon
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Overview</h3>
            <p class="text-sm text-gray-600">Key metrics at a glance</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">₱125,450</div>
                    <div class="text-sm text-gray-500">This Month's Revenue</div>
                    <div class="text-xs text-green-600">+12% from last month</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">48</div>
                    <div class="text-sm text-gray-500">Completed Orders</div>
                    <div class="text-xs text-blue-600">+8 from last month</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-orange-600">₱8,750</div>
                    <div class="text-sm text-gray-500">Outstanding Balance</div>
                    <div class="text-xs text-red-600">5 overdue accounts</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">156</div>
                    <div class="text-sm text-gray-500">Total Customers</div>
                    <div class="text-xs text-purple-600">+12 new this month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
            <p class="text-sm text-gray-600">Latest business activities</p>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-4 p-4 bg-green-50 rounded-lg">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Payment received from Garcia Enterprises</p>
                        <p class="text-xs text-gray-500">₱15,500.00 - 2 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 p-4 bg-blue-50 rounded-lg">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New order created for Torres Law Firm</p>
                        <p class="text-xs text-gray-500">Order #00156 - 4 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 p-4 bg-yellow-50 rounded-lg">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">Payment overdue reminder sent</p>
                        <p class="text-xs text-gray-500">Order #00142 - 6 hours ago</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">New customer registered</p>
                        <p class="text-xs text-gray-500">Martinez Construction - Yesterday</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Export Data</h3>
            <p class="text-sm text-gray-600">Download reports in various formats</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-excel text-green-600 mr-2"></i>
                    Export to Excel
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                    Export to PDF
                </button>
                <button class="flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                    Export to CSV
                </button>
            </div>
        </div>
    </div>
</div>
@endsection