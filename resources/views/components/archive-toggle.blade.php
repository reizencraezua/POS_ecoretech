@props(['showArchived' => false, 'route' => ''])

<div class="flex items-center space-x-4 mb-6">
    <div class="flex items-center space-x-2">
        <a href="{{ $route }}?archived=false" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !$showArchived ? 'bg-maroon text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-list mr-2"></i>
            Active
        </a>
        <a href="{{ $route }}?archived=true" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $showArchived ? 'bg-maroon text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            <i class="fas fa-archive mr-2"></i>
            Archived
        </a>
    </div>
    
    @if($showArchived)
        <div class="text-sm text-gray-600 bg-yellow-50 px-3 py-2 rounded-lg border border-yellow-200">
            <i class="fas fa-info-circle mr-1"></i>
            Viewing archived items
        </div>
    @endif
</div>
