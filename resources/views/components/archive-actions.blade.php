@props(['item', 'archiveRoute' => '', 'restoreRoute' => '', 'showRestore' => false])

<div class="flex items-center space-x-2">
    @if($showRestore)
        <!-- Restore Button -->
        <form action="{{ $restoreRoute }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                    class="text-green-600 hover:text-green-800 transition-colors"
                    onclick="return confirm('Are you sure you want to restore this item?')"
                    title="Restore">
                <i class="fas fa-undo"></i>
            </button>
        </form>
    @else
        <!-- Archive Button -->
        <form action="{{ $archiveRoute }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                    class="text-orange-600 hover:text-orange-800 transition-colors"
                    onclick="return confirm('Are you sure you want to archive this item?')"
                    title="Archive">
                <i class="fas fa-archive"></i>
            </button>
        </form>
    @endif
    
    <!-- View Button -->
    <a href="{{ route(str_replace('.archive', '.show', $archiveRoute), $item) }}" 
       class="text-blue-600 hover:text-blue-800 transition-colors"
       title="View">
        <i class="fas fa-eye"></i>
    </a>
    
    @if(!$showRestore)
        <!-- Edit Button -->
        <a href="{{ route(str_replace('.archive', '.edit', $archiveRoute), $item) }}" 
           class="text-indigo-600 hover:text-indigo-800 transition-colors"
           title="Edit">
            <i class="fas fa-edit"></i>
        </a>
    @endif
</div>
