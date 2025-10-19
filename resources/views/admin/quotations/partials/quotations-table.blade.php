<div id="quotationsTableContainer">
    <!-- Quotations Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($quotations as $quotation)
        <div class="bg-white rounded-lg shadow hover:shadow-lg border border-gray-200 cursor-pointer group" onclick="window.location.href='{{ route('admin.quotations.show', $quotation) }}'">
            <!-- Card Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-maroon transition-colors">Quote #{{ str_pad($quotation->quotation_id, 5, '0', STR_PAD_LEFT) }}</h3>
                        <p class="text-sm text-gray-600">{{ $quotation->quotation_date->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($quotation->status === 'Pending')
                                     text-yellow-800
                                @else
                                     text-green-800
                                @endif
                            ">
                            {{ $quotation->status }}
                        </span>
                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-maroon transition-colors"></i>
                    </div>
                </div>
            </div>

            <!-- Card Content -->
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Customer Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-maroon text-white rounded-full flex items-center justify-center text-sm font-bold">
                            {{ substr($quotation->customer->customer_firstname, 0, 1) }}{{ substr($quotation->customer->customer_lastname, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $quotation->customer->display_name }}</p>
                            <p class="text-xs text-gray-500">{{ $quotation->customer->customer_contact }}</p>
                        </div>
                    </div>

                    <!-- Items Count -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Items:</span>
                        <span class="font-medium">{{ $quotation->details->count() }} item(s)</span>
                    </div>

                    <!-- Total Quantity -->
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Total Qty:</span>
                        <span class="font-medium">{{ $quotation->details->sum('quantity') }} pcs</span>
                    </div>

                    <!-- Total Amount -->
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total Amount:</span>
                        <span class="text-lg font-bold text-maroon">₱{{ number_format($quotation->final_total_amount, 2) }}</span>
                    </div>

                    <!-- Notes Preview -->
                    @if($quotation->notes)
                    <div class="text-xs text-gray-500">
                        <p class="truncate">{{ \Illuminate\Support\Str::limit($quotation->notes, 50) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card Actions -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between" onclick="event.stopPropagation()">
                <div class="flex items-center space-x-2">
                    @if($quotation->status === 'Pending')
                    <form method="POST" action="{{ route('admin.quotations.status', $quotation) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="Closed">
                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm transition-colors">
                            <i class="fas fa-check mr-1"></i>Close
                        </button>
                    </form>
                    @endif

                    @if($quotation->status === 'Closed')
                    <button onclick="openConvertModal({{ $quotation->quotation_id }})"
                        class="text-blue-600 hover:text-blue-800 text-sm transition-colors">
                        <i class="fas fa-exchange-alt mr-1"></i>Convert
                    </button>
                    @endif
                </div>

                <div class="flex items-center">
                    <a href="{{ route('admin.quotations.edit', $quotation) }}" class="text-maroon hover:text-maroon-dark text-sm transition-colors">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                </div>
            </div>
        </div>
        @empty
            @if(request('search'))
                {{-- Don't show empty state when searching --}}
            @else
                <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-gray-400">
                        <i class="fas fa-file-alt text-6xl mb-4"></i>
                        <p class="text-xl font-medium mb-2">No quotations found</p>
                        <p class="text-gray-500 mb-4">Create your first quotation to get started</p>
                    </div>
                </div>
            @endif
        @endforelse
    </div>

    <!-- Pagination -->
    @if($quotations->hasPages())
    <div class="bg-white rounded-lg shadow p-4">
        {{ $quotations->links() }}
    </div>
    @endif
</div>