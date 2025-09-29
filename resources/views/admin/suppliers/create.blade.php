@extends('layouts.admin')

@section('title', 'Add New Supplier')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Supplier</h1>
                <p class="text-sm text-gray-600 mt-1">Create a new supplier record</p>
            </div>
            <a href="{{ route('admin.suppliers.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
            </a>
        </div>
    </div>

    <div class="p-6">
        <form method="POST" action="{{ route('admin.suppliers.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier Name -->
                <div class="md:col-span-2">
                    <label for="supplier_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="supplier_name" 
                           id="supplier_name" 
                           value="{{ old('supplier_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent @error('supplier_name') border-red-500 @enderror"
                           placeholder="Enter supplier name"
                           required>
                    @error('supplier_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="supplier_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <input type="email" 
                           name="supplier_email" 
                           id="supplier_email" 
                           value="{{ old('supplier_email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent @error('supplier_email') border-red-500 @enderror"
                           placeholder="supplier@example.com">
                    @error('supplier_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="supplier_contact" 
                           id="supplier_contact" 
                           value="{{ old('supplier_contact') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent @error('supplier_contact') border-red-500 @enderror"
                           placeholder="Enter contact number"
                           maxlength="20"
                           required>
                    @error('supplier_contact')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="supplier_address" class="block text-sm font-medium text-gray-700 mb-2">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="supplier_address" 
                              id="supplier_address" 
                              rows="4"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-transparent @error('supplier_address') border-red-500 @enderror"
                              placeholder="Enter complete address"
                              required>{{ old('supplier_address') }}</textarea>
                    @error('supplier_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.suppliers.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-maroon text-white px-6 py-2 rounded-lg hover:bg-maroon-dark transition-colors">
                    <i class="fas fa-save mr-2"></i>Create Supplier
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-format phone number (optional enhancement)
document.getElementById('supplier_contact').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 0) {
        if (value.length <= 3) {
            value = value;
        } else if (value.length <= 6) {
            value = value.slice(0, 3) + '-' + value.slice(3);
        } else {
            value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
        }
    }
    e.target.value = value;
});
</script>
@endsection