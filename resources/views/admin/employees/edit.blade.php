@extends('layouts.admin')

@section('title', 'Edit Employee')
@section('page-title', 'Edit Employee')
@section('page-description', 'Update employee information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.employees.show', $employee) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Employee</h2>
                </div>
                <div class="text-sm text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Update employee information below
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Personal Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="employee_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname', $employee->employee_firstname) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_firstname') border-red-500 @enderror">
                        @error('employee_firstname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="employee_middlename" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                        <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename', $employee->employee_middlename) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_middlename') border-red-500 @enderror">
                        @error('employee_middlename')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="employee_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname', $employee->employee_lastname) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_lastname') border-red-500 @enderror">
                        @error('employee_lastname')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="employee_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                    <input type="email" name="employee_email" id="employee_email" value="{{ old('employee_email', $employee->employee_email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_email') border-red-500 @enderror">
                    @error('employee_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="employee_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                    <input type="text" name="employee_contact" id="employee_contact" value="{{ old('employee_contact', $employee->employee_contact) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_contact') border-red-500 @enderror"
                           placeholder="09XX-XXX-XXXX">
                    @error('employee_contact')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-6">
                    <label for="employee_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                    <textarea name="employee_address" id="employee_address" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_address') border-red-500 @enderror">{{ old('employee_address', $employee->employee_address) }}</textarea>
                    @error('employee_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Job Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Job Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">Job Position *</label>
                        <select name="job_id" id="job_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_id') border-red-500 @enderror">
                            <option value="">Select Job Position</option>
                            @foreach($job as $jobPosition)
                                <option value="{{ $jobPosition->job_id }}" {{ old('job_id', $employee->job_id) == $jobPosition->job_id ? 'selected' : '' }}>
                                    {{ $jobPosition->job_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('job_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Hire Date *</label>
                        <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('hire_date') border-red-500 @enderror">
                        @error('hire_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.employees.show', $employee) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
