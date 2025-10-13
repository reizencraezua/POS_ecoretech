@extends('layouts.admin')

@section('title', 'Job Positions')
@section('page-title', 'Job Positions Management')
@section('page-description', 'Manage employee job positions')

@section('content')
<div class="space-y-6" x-data="{ jobModal: false }">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            @if(!$showArchived)
                <button @click="jobModal = true" 
                        class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Job Position
                </button>
            @endif
        </div>

        
        <!-- Search and Archive Toggle -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.jobs.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
               class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-box-archive mr-2"></i>
                {{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
            </a>
            
            <form method="GET" class="flex items-center space-x-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search job positions..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.jobs.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Jobs Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-blue-50 hover:shadow-sm transition-all duration-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.jobs.show', $job) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-maroon text-white rounded-full flex items-center justify-center">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center gap-2">
                                            <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600">{{ $job->job_title }}</div>
                                            <i class="fas fa-external-link-alt text-xs text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                                        </div>
                                        <div class="text-sm text-gray-500">#{{ str_pad($job->job_id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $job->job_description ? \Illuminate\Support\Str::limit($job->job_description, 80) : 'No description' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $job->employees->count() }} {{ $job->employees->count() === 1 ? 'employee' : 'employees' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $job->created_at->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $job->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <div class="flex items-center space-x-2">
                                    @if($showArchived)
                                        <x-archive-actions 
                                            :item="$job" 
                                            :archiveRoute="'admin.jobs.archive'" 
                                            :restoreRoute="'admin.jobs.restore'" 
                                            :editRoute="'admin.jobs.edit'"
                                            :showRestore="true" />
                                    @else
                                        <x-archive-actions 
                                            :item="$job" 
                                            :archiveRoute="'admin.jobs.archive'" 
                                            :restoreRoute="'admin.jobs.restore'" 
                                            :editRoute="'admin.jobs.edit'"
                                            :showRestore="false" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-briefcase text-6xl mb-4"></i>
                                    <p class="text-xl font-medium mb-2">No job positions found</p>
                                    <p class="text-gray-500 mb-6">Get started by creating your first job position</p>
                                    <button @click="jobModal = true" 
                                            class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-maroon-dark transition-colors inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i>Add Job Position
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($jobs->hasPages())
            <div class="bg-white px-6 py-3 border-t border-gray-200">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>

    <!-- Add Job Position Modal -->
    <div x-show="jobModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="jobModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Job Position</h3>
                <button @click="jobModal = false" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.jobs.store') }}" class="space-y-6">
                @csrf

                <!-- Job Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Job Information</h3>
                    <div class="space-y-6">
                        <!-- Job Title -->
                        <div>
                            <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Job Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="job_title" 
                                   id="job_title" 
                                   value="{{ old('job_title') }}" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_title') border-red-500 @enderror"
                                   placeholder="e.g., Software Engineer, Manager, Sales Representative">
                            @error('job_title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Job Description -->
                        <div>
                            <label for="job_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Job Description
                            </label>
                            <textarea name="job_description" 
                                      id="job_description" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_description') border-red-500 @enderror"
                                      placeholder="Describe the job responsibilities, requirements, and qualifications...">{{ old('job_description') }}</textarea>
                            @error('job_description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Provide a brief description of the job responsibilities and requirements
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" 
                            @click="jobModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Job Position
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }
</style>
@endpush
@endsection