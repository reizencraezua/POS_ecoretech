<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($customers as $customer)
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                        </div>
                        <div class="text-sm text-gray-500">ID: {{ $customer->customer_id }}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $customer->contact_number1 }}</div>
                @if($customer->contact_number2)
                    <div class="text-sm text-gray-500">{{ $customer->contact_number2 }}</div>
                @endif
                @if($customer->email)
                    <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                @if($customer->business_name)
                    <div class="text-sm font-medium text-gray-900">{{ $customer->business_name }}</div>
                @else
                    <span class="text-sm text-gray-400">Individual</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $customer->city }}, {{ $customer->province }}</div>
                <div class="text-sm text-gray-500">{{ $customer->address }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center justify-center space-x-2">
                    <a href="{{ route('cashier.customers.show', $customer) }}" 
                       class="text-maroon hover:text-maroon-dark" title="View Customer">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('cashier.customers.edit', $customer) }}" 
                       class="text-blue-600 hover:text-blue-900" title="Edit Customer">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="px-6 py-12 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-users text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No customers found</p>
                    <p class="text-sm">Get started by adding a new customer.</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@if($customers->hasPages())
<div class="px-6 py-4 border-t border-gray-200">
    {{ $customers->links() }}
</div>
@endif
