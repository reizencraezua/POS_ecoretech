@extends('layouts.admin')

@section('title', 'Job Position Details')
@section('page-title', $job->job_title)
@section('page-description', 'View job position details and assigned employees')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Job Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.jobs.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $job->job_title }}</h2>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.jobs.edit', $job) }}" 
                               class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="p-8">
                    <div class="space-y-6">
                        <!-- Job Description -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h3>
                            @if($job->job_description)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $job->job_description }}</p>
                                </div>
                            @else
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-500 italic">No description provided</p>
                                </div>
                            @endif
                        </div>

                        <!-- Job Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Job Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-calendar-plus text-maroon"></i>
                                        <span class="text-sm font-medium text-gray-700">Created</span>
                                    </div>
                                    <p class="text-gray-900">{{ $job->created_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <i class="fas fa-calendar-edit text-maroon"></i>
                                        <span class="text-sm font-medium text-gray-700">Last Updated</span>
                                    </div>
                                    <p class="text-gray-900">{{ $job->updated_at->format('F j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Employees -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Assigned Employees</h3>
                </div>
                
                <div class="p-6">
                    @if($job->employees->count() > 0)
                        <div class="space-y-4">
                            @foreach($job->employees as $employee)
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-maroon flex items-center justify-center">
                                            <i class="fas fa-user text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $employee->employee_firstname }} {{ $employee->employee_lastname }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            {{ $employee->employee_email }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('admin.employees.show', $employee) }}" 
                                           class="text-blue-600 hover:text-blue-800 transition-colors">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Total Employees</span>
                                <span class="font-medium">{{ $job->employees->count() }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-sm">No employees assigned to this position yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-lg shadow-md mt-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-users text-blue-500"></i>
                                <span class="text-sm text-gray-700">Total Employees</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $job->employees->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-clock text-green-500"></i>
                                <span class="text-sm text-gray-700">Days Active</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ $job->created_at->diffInDays(now()) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
