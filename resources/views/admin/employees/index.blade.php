@extends('layouts.admin')

@section('title', 'Employees')
@section('page-title', 'Employee Management')
@section('page-description', 'Manage your staff and their responsibilities')

@section('content')
<div class="space-y-6" x-data="{ employeeModal: false, positionModal: false }">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <button @click="employeeModal = true" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Employee
            </button>
            <a href="{{ route('admin.jobs.create') }}" class="bg-blue-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-briefcase mr-2"></i>
                Add Position
            </a>
        </div>
        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.employees.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <form method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search employees..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.employees.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders Assigned</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.employees.show', $employee) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center font-bold">
                                        {{ substr($employee->employee_firstname, 0, 1) }}{{ substr($employee->employee_lastname, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $employee->full_name }}</div>
                                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                        </div>
                                        <div class="text-sm text-gray-500">#{{ str_pad($employee->employee_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->employee_email }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->employee_contact }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="px-2 py-1 text-xs font-medium bg-maroon bg-opacity-10 text-maroon rounded-full">
                                        {{ $employee->job->job_title ?? 'No Position' }}
                                    </span>
                                </div>
                                @if($employee->job && $employee->job->job_description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($employee->job->job_description, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->hire_date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $employee->hire_date->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->orders_count ?? 0 }} total</div>
                                <div class="text-sm text-gray-500">{{ $employee->active_orders_count ?? 0 }} active</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <div class="flex items-center space-x-2">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$employee" 
                                            :archiveRoute="'admin.employees.archive'" 
                                            :restoreRoute="'admin.employees.restore'" 
                                            :editRoute="'admin.employees.edit'"
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$employee" 
                                            :archiveRoute="'admin.employees.archive'" 
                                            :restoreRoute="'admin.employees.restore'" 
                                            :editRoute="'admin.employees.edit'"
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-users text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No employees found</p>
                                    <p class="text-gray-500">Add your first team member</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    <!-- Add Employee Modal -->
    <div x-show="employeeModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="employeeModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Employee</h3>
                <button @click="employeeModal = false" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-6">
                @csrf

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="employee_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_firstname') border-red-500 @enderror"
                                   placeholder="Enter first name">
                            @error('employee_firstname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="employee_middlename" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_middlename') border-red-500 @enderror"
                                   placeholder="Enter middle name">
                            @error('employee_middlename')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="employee_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_lastname') border-red-500 @enderror"
                                   placeholder="Enter last name">
                            @error('employee_lastname')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="employee_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" name="employee_email" id="employee_email" value="{{ old('employee_email') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_email') border-red-500 @enderror"
                                   placeholder="employee@example.com">
                            @error('employee_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="employee_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                            <input type="text" name="employee_contact" id="employee_contact" value="{{ old('employee_contact') }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_contact') border-red-500 @enderror"
                                   placeholder="09XX-XXX-XXXX"
                                   maxlength="11"
                                   pattern="[0-9]{11}">
                            @error('employee_contact')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="employee_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                            <textarea name="employee_address" id="employee_address" rows="3" required
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_address') border-red-500 @enderror"
                                      placeholder="Enter complete address">{{ old('employee_address') }}</textarea>
                            @error('employee_address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Employment Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">Job Position *</label>
                            <select name="job_id" id="job_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_id') border-red-500 @enderror">
                                <option value="">Select job position</option>
                                @foreach($jobs as $job)
                                    <option value="{{ $job->job_id }}" {{ old('job_id') == $job->job_id ? 'selected' : '' }}>
                                        {{ $job->job_title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('job_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Hire Date *</label>
                            <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', now()->format('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                    <button type="button" @click="employeeModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Save Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection