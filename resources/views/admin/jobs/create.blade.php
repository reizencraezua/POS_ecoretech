@extends('layouts.admin')

@section('title', 'Job Positions')
@section('page-title', 'Job Positions Management')
@section('page-description', 'Manage employee job positions')

@section('content')
<div class="space-y-6" x-data="{ jobModal: false }">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-4">
            <button @click="jobModal = true" 
                    class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Job Position
            </button>
        </div>
        
        <!-- Search -->
        <div class="flex items-center space-x-4">
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
                                    <!-- Edit Job -->
                                    <a href="{{ route('admin.jobs.edit', $job) }}" 
                                       class="text-maroon hover:text-maroon-dark transition-colors" title="Edit Job Position" onclick="event.stopPropagation();">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <!-- Archive Job -->
                                    <form method="POST" action="{{ route('admin.jobs.archive', $job) }}" 
                                          class="inline" onsubmit="return confirm('Are you sure you want to archive this job position?')">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-900 transition-colors" title="Archive Job Position" onclick="event.stopPropagation();">
                                            <i class="fas fa-archive"></i>
                                        </button>
                                    </form>
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
                                    <a href="{{ route('admin.jobs.create') }}" 
                                       class="bg-maroon text-white px-4 py-2 rounded-lg hover:bg-maroon-dark transition-colors inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i>Add Job Position
                                    </a>
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
</div>
@endsection