@extends('layouts.admin')

@section('title', 'Add Employee')
@section('page-title', 'Add New Employee')
@section('page-description', 'Create a new employee record')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.employees.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Employee Details</h2>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.employees.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="employee_firstname" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="employee_firstname" id="employee_firstname" value="{{ old('employee_firstname') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_firstname') border-red-500 @enderror">
                    @error('employee_firstname')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="employee_middlename" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="employee_middlename" id="employee_middlename" value="{{ old('employee_middlename') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
                </div>
                <div>
                    <label for="employee_lastname" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="employee_lastname" id="employee_lastname" value="{{ old('employee_lastname') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_lastname') border-red-500 @enderror">
                    @error('employee_lastname')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="employee_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="employee_email" id="employee_email" value="{{ old('employee_email') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_email') border-red-500 @enderror">
                    @error('employee_email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="employee_contact" class="block text-sm font-medium text-gray-700 mb-1">Contact *</label>
                    <input type="text" name="employee_contact" id="employee_contact" value="{{ old('employee_contact') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_contact') border-red-500 @enderror">
                    @error('employee_contact')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="employee_address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                <textarea name="employee_address" id="employee_address" rows="3" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('employee_address') border-red-500 @enderror">{{ old('employee_address') }}</textarea>
                @error('employee_address')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">Hire Date *</label>
                    <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', now()->format('Y-m-d')) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('hire_date') border-red-500 @enderror">
                    @error('hire_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="job_id" class="block text-sm font-medium text-gray-700 mb-1">Job Position *</label>
                        <button type="button" @click="document.getElementById('addJobModal').classList.remove('hidden')" class="text-blue-600 text-sm hover:underline">Add Position</button>
                    </div>
                    <select name="job_id" id="job_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_id') border-red-500 @enderror">
                        <option value="">Select Job Position</option>
                        @foreach($job as $position)
                            <option value="{{ $position->job_id }}" {{ old('job_id') == $position->job_id ? 'selected' : '' }}>
                                {{ $position->job_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('job_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- Add Job Position Modal -->
<div id="addJobModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" onclick="if(event.target===this){this.classList.add('hidden')}">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-lg shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Job Position</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('addJobModal').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.jobs.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="job_title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="job_title" id="job_title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
            </div>
            <div>
                <label for="job_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="job_description" id="job_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"></textarea>
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <button type="button" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50" onclick="document.getElementById('addJobModal').classList.add('hidden')">Cancel</button>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md">Save Position</button>
            </div>
        </form>
    </div>
</div>


