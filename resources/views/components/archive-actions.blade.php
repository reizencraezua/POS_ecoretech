@props(['item', 'archiveRoute' => '', 'restoreRoute' => '', 'editRoute' => '', 'showRestore' => false])

<div class="flex items-center space-x-2 relative z-20">
    @if(!$showRestore && $editRoute)
        <!-- Edit Button -->
        <a href="{{ route($editRoute, $item) }}" 
           class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
           title="Edit"
           onclick="event.stopPropagation();">
            <i class="fas fa-edit"></i>
        </a>
    @endif
    
    @if($showRestore)
        <!-- Edit Button for Archived Items -->
        @if($editRoute)
            <a href="{{ route($editRoute, $item) }}" 
               class="text-red-600 hover:text-red-800 transition-colors p-1 rounded hover:bg-red-50"
               title="Edit"
               onclick="event.stopPropagation();">
                <i class="fas fa-edit"></i>
            </a>
        @endif
        
        <!-- Restore Button -->
        <form action="{{ route($restoreRoute, $item) }}" method="POST" class="inline" onclick="event.stopPropagation();">
            @csrf
            <button type="submit" 
                    class="text-green-600 hover:text-green-800 transition-colors p-1 rounded hover:bg-green-50"
                    onclick="return confirm('Are you sure you want to restore this item?')"
                    title="Restore">
                <i class="fas fa-undo"></i>
            </button>
        </form>
    @else
        <!-- Archive Button -->
        <form action="{{ route($archiveRoute, $item) }}" method="POST" class="inline" onclick="event.stopPropagation();">
            @csrf
            <button type="submit" 
                    class="text-gray-600 hover:text-gray-800 transition-colors p-1 rounded hover:bg-gray-50"
                    onclick="return confirm('Are you sure you want to archive this item?')"
                    title="Archive">
                <i class="fas fa-archive"></i>
            </button>
        </form>
    @endif
</div>
