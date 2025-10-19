@extends('layouts.admin')

@section('title', 'Edit Job Position')
@section('page-title', 'Edit Job Position')
@section('page-description', 'Update job position details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-8 py-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.jobs.show', $job) }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-semibold text-gray-900">Edit Job Position: {{ $job->job_title }}</h2>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.jobs.update', $job) }}" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Job Title -->
                <div>
                    <label for="job_title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                    <input type="text" name="job_title" id="job_title" value="{{ old('job_title', $job->job_title) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_title') border-red-500 @enderror"
                           placeholder="Enter job title">
                    @error('job_title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Job Description -->
                <div>
                    <label for="job_description" class="block text-sm font-medium text-gray-700 mb-2">Job Description</label>
                    <textarea name="job_description" id="job_description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('job_description') border-red-500 @enderror"
                              placeholder="Enter job description (optional)">{{ old('job_description', $job->job_description) }}</textarea>
                    @error('job_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Provide a brief description of the job responsibilities and requirements</p>
                </div>
            </div>

            <!-- Current Employee Count Info -->
            @if($job->employees->count() > 0)
                <div class="mt-6 p-4 text-blue-800 border border-blue-200 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-sm text-blue-700">
                            This job position currently has {{ $job->employees->count() }} employee(s) assigned.
                        </span>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.jobs.show', $job) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-maroon text-white rounded-md hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Job Position
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
