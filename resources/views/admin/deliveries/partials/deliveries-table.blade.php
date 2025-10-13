<div id="deliveriesTableContainer">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($deliveries as $delivery)
                <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('admin.deliveries.show', $delivery) }}'">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">Order #{{ $delivery->order_id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-maroon text-white rounded-full flex items-center justify-center text-sm font-bold">
                                {{ substr($delivery->order->customer->customer_firstname, 0, 1) }}{{ substr($delivery->order->customer->customer_lastname, 0, 1) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $delivery->order->customer->customer_firstname }} {{ $delivery->order->customer->customer_lastname }}</div>
                                <div class="text-sm text-gray-500">{{ $delivery->order->customer->contact_number1 ?? 'No contact' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $delivery->delivery_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($delivery->status == 'scheduled') bg-blue-100 text-blue-800
                            @elseif($delivery->status == 'in_transit') bg-yellow-100 text-yellow-800
                            @elseif($delivery->status == 'delivered') bg-green-100 text-green-800
                            @elseif($delivery->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($delivery->employee)
                            <div class="text-sm font-medium text-gray-900">{{ $delivery->employee->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $delivery->employee->job->job_title ?? 'No Job Title' }}</div>
                        @elseif($delivery->driver_name)
                            <div class="text-sm font-medium text-gray-900">{{ $delivery->driver_name }}</div>
                            <div class="text-sm text-gray-500">External Driver</div>
                        @else
                            <span class="text-sm text-gray-500">Not Assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-center space-x-3">
                            @if($showArchived)
                                <x-archive-actions 
                                    :item="$delivery" 
                                    :archiveRoute="'admin.deliveries.archive'" 
                                    :restoreRoute="'admin.deliveries.restore'" 
                                    :editRoute="'admin.deliveries.edit'"
                                    :showRestore="true" />
                            @else
                                <x-archive-actions 
                                    :item="$delivery" 
                                    :archiveRoute="'admin.deliveries.archive'" 
                                    :restoreRoute="'admin.deliveries.restore'" 
                                    :editRoute="'admin.deliveries.edit'"
                                    :showRestore="false" />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                    @if(request('search'))
                        {{-- Don't show empty state when searching --}}
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i class="fas fa-truck text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No deliveries found</p>
                                    <p class="text-sm">Schedule your first delivery to get started</p>
                                    @if(!$showArchived)
                                        <a href="{{ route('admin.deliveries.create') }}" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center mt-4">
                                            <i class="fas fa-plus mr-2"></i>
                                            Schedule Delivery
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($deliveries->hasPages())
        <div class="bg-white px-6 py-3 border-t border-gray-200">
            {{ $deliveries->links() }}
        </div>
    @endif
</div>
