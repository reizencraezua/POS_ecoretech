@extends('layouts.admin')

@section('title', 'Products')
@section('page-title', 'Product Catalog')
@section('page-description', 'Manage your product inventory and pricing')

@section('content')
<div class="space-y-6" x-data="{ 
	productModal: false, 
	editModal: false, 
	editingProduct: null,
	requiresLayout: false,
	toggleLayoutPrice() {
		this.requiresLayout = !this.requiresLayout;
		const layoutPriceInput = document.getElementById('layout_price');
		if (layoutPriceInput) {
			layoutPriceInput.disabled = !this.requiresLayout;
			if (!this.requiresLayout) {
				layoutPriceInput.value = '0';
			}
		}
	},
	toggleEditLayoutPrice() {
		if (this.editingProduct) {
			this.editingProduct.requires_layout = !this.editingProduct.requires_layout;
			const editLayoutPriceInput = document.getElementById('edit_layout_price');
			if (editLayoutPriceInput) {
				editLayoutPriceInput.disabled = !this.editingProduct.requires_layout;
				if (!this.editingProduct.requires_layout) {
					this.editingProduct.layout_price = 0;
				}
			}
		}
	}
}">
	<!-- Header Actions -->
	<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
		<div class="flex items-center space-x-4">
			@if(!$showArchived)
				<button @click="productModal = true" class="bg-maroon hover:bg-maroon-dark text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
					<i class="fas fa-plus mr-2"></i>
					Add Product
				</button>
			@endif
		</div>
		
		<!-- Search and Archive Toggle -->
		<div class="flex items-center space-x-4">
			<a href="{{ route('admin.products.index', array_merge(request()->query(), ['archived' => isset($showArchived) && $showArchived ? 0 : 1])) }}"
			   class="px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center border {{ (isset($showArchived) && $showArchived) ? 'border-green-600 text-green-700 hover:bg-green-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
				<i class="fas fa-box-archive mr-2"></i>
				{{ (isset($showArchived) && $showArchived) ? 'Show Active' : 'View Archives' }}
			</a>
			
			<form method="GET" class="flex items-center space-x-2" id="searchForm">
				<div class="relative">
					<input type="text" 
                           id="instantSearchInput" 
                           data-instant-search="true"
                           data-container="productsTableContainer"
                           data-loading="searchLoading"
                           value="{{ request('search') }}" 
                           placeholder="Search products..." 
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
					<i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <div id="searchLoading" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
                        <i class="fas fa-spinner fa-spin text-gray-400"></i>
                    </div>
				</div>
				@if(request('search'))
					<a href="{{ route('admin.products.index') }}" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
						<i class="fas fa-times"></i>
					</a>
				@endif
			</form>
		</div>
	</div>

	<!-- Products Grid -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
		@forelse($products as $product)
			<div class="bg-white rounded-lg shadow hover:shadow-lg transition-all duration-200 border border-gray-200 group cursor-pointer relative">
				<!-- Clickable overlay for the entire card -->
				<a href="{{ route('admin.products.show', $product) }}" class="absolute inset-0 z-10"></a>
				
				<!-- Product Content -->
				<div class="p-4 relative">
					<div class="mb-3">
						<div class="flex items-center justify-between mb-2">
							<span class="text-sm text-gray-500">#{{ str_pad($product->product_id, 3, '0', STR_PAD_LEFT) }}</span>
							@if($product->category)
								<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
									  style="background-color: {{ $product->category->category_color }}20; color: {{ $product->category->category_color }};">
									{{ $product->category->category_name }}
								</span>
							@endif
						</div>
						<h3 class="text-lg font-semibold text-gray-900 mb-1 group-hover:text-maroon transition-colors">{{ $product->product_name }}</h3>
						@if($product->product_description)
							<p class="text-sm text-gray-600 truncate w-full max-w-xs">
								{{ $product->product_description }}
							</p>
						@endif

					</div>

					<!-- Price -->
					<div class="flex items-center justify-between mb-4">
						<div>
							<span class="text-xs text-gray-500">Fixed Price</span>
							<div class="text-2xl font-bold text-maroon">₱{{ number_format($product->base_price, 2) }}</div>
						</div>
					</div>

					<!-- Actions -->
					<div class="flex items-center justify-between border-t border-gray-200 pt-4 relative z-20">
						<span class="text-xs text-gray-500">
							Updated {{ $product->updated_at->diffForHumans() }}
						</span>
						
						<div class="flex items-center space-x-2">
							@if($showArchived)
								<x-archive-actions 
									:item="$product" 
									:archiveRoute="'admin.products.archive'" 
									:restoreRoute="'admin.products.restore'" 
									:editRoute="'admin.products.edit'"
									:showRestore="true" />
							@else
								<x-archive-actions 
									:item="$product" 
									:archiveRoute="'admin.products.archive'" 
									:restoreRoute="'admin.products.restore'" 
									:editRoute="'admin.products.edit'"
									:showRestore="false" />
							@endif
						</div>
					</div>
				</div>
			</div>
		@empty
			<div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
				<div class="text-gray-400">
					<i class="fas fa-box-open text-6xl mb-4"></i>
					<p class="text-xl font-medium mb-2">No products found</p>
					<p class="text-gray-500 mb-4">Start building your product catalog</p>
				</div>
			</div>
		@endforelse
	</div>

	<!-- Pagination -->
	@if($products->hasPages())
		<div class="bg-white rounded-lg shadow p-4">
			{{ $products->links() }}
		</div>
	@endif

	<!-- Add Product Modal -->
<div x-show="productModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="productModal = false">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Add New Product</h3>
            <button @click="productModal = false" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-6">
            @csrf

            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">Product Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Name -->
                    <div class="md:col-span-2">
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                        <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('product_name') border-red-500 @enderror"
                               placeholder="Enter product name">
                        @error('product_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category Selection -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category_id" id="category_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('category_id') border-red-500 @enderror">
                            <option value="">Select a category</option>
                            @foreach(\App\Models\Category::where('is_active', true)->orderBy('category_name')->get() as $category)
                                <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Choose the product category</p>
                    </div>

					<!-- Fixed Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-1">Fixed Price (₱) *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₱</span>
                            </div>
                            <input type="number" name="base_price" id="base_price" value="{{ old('base_price') }}" step="0.01" min="0" required
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('base_price') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        @error('base_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Standard unit price for this product</p>
                    </div>


					<div>
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                            <input type="checkbox" name="requires_layout" id="requires_layout" value="1" {{ old('requires_layout') ? 'checked' : '' }}
                                   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded" @change="toggleLayoutPrice()">
                            <div>
                                <label for="requires_layout" class="text-sm font-medium text-gray-900 cursor-pointer">
                                    Requires Layout Design
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Check if this product needs layout design services</p>
                            </div>
                        </div>
                        @error('requires_layout')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

					
                    <!-- Layout Price -->
					<div>
						<label for="layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<span class="text-gray-500 sm:text-sm">₱</span>
							</div>
							<input type="number" name="layout_price" id="layout_price" value="{{ old('layout_price') }}" step="0.01" min="0"
								   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('layout_price') border-red-500 @enderror"
								   placeholder="0.00" :disabled="!requiresLayout">
						</div>
						@error('layout_price')
							<p class="text-red-500 text-xs mt-1">{{ $message }}</p>
						@enderror
						<p class="text-xs text-gray-500 mt-1">Additional cost for layout design services</p>
					</div>

                    <!-- Requires Layout Checkbox -->
                 


                    <!-- Product Description -->
                    <div class="md:col-span-2">
                        <label for="product_description" class="block text-sm font-medium text-gray-700 mb-1">Product Description</label>
                        <textarea name="product_description" id="product_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon @error('product_description') border-red-500 @enderror"
                                  placeholder="Detailed description of the product, materials, specifications, etc...">{{ old('product_description') }}</textarea>
                        @error('product_description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 border-t border-gray-200 pt-6">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>


	<!-- Edit Product Modal -->
	<div x-show="editModal" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="editModal = false">
		<div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
			<div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
				<h3 class="text-xl font-semibold text-gray-900">Edit Product</h3>
				<button @click="editModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
					<i class="fas fa-times text-xl"></i>
				</button>
			</div>
			
			<form x-bind:action="editingProduct ? `/admin/products/${editingProduct.product_id}` : '#'" method="POST" class="space-y-6">
				@csrf
				@method('PATCH')
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div class="md:col-span-2">
						<label for="edit_product_name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
						<input type="text" name="product_name" id="edit_product_name" x-model="editingProduct ? editingProduct.product_name : ''" required
							   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
							   placeholder="Enter product name">
					</div>

					<!-- Category Selection -->
					<div>
						<label for="edit_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
						<select name="category_id" id="edit_category_id" 
								class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon">
							<option value="">Select a category</option>
							@foreach(\App\Models\Category::where('is_active', true)->orderBy('category_name')->get() as $category)
								<option value="{{ $category->category_id }}" 
										x-bind:selected="editingProduct && editingProduct.category_id == {{ $category->category_id }}">
									{{ $category->category_name }}
								</option>
							@endforeach
						</select>
						<p class="text-xs text-gray-500 mt-1">Choose the product category</p>
					</div>
					
					<div>
						<label for="edit_base_price" class="block text-sm font-medium text-gray-700 mb-1">Fixed Price (₱) *</label>
						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<span class="text-gray-500 sm:text-sm">₱</span>
							</div>
							<input type="number" name="base_price" id="edit_base_price" x-model="editingProduct ? editingProduct.base_price : ''" step="0.01" min="0" required
								   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
								   placeholder="0.00">
						</div>
					</div>

					<div>
						<div class="flex items-center space-x-3">
							<input type="checkbox" name="requires_layout" id="edit_requires_layout" value="1" 
								   x-model="editingProduct ? editingProduct.requires_layout : false"
								   class="h-4 w-4 text-maroon focus:ring-maroon border-gray-300 rounded" @change="toggleEditLayoutPrice()">
							<label for="edit_requires_layout" class="text-sm font-medium text-gray-700 cursor-pointer">
								Requires Layout Design
							</label>
						</div>
						<p class="text-xs text-gray-500 mt-1">Check if this product needs layout design services</p>
					</div>

					<div>
						<label for="edit_layout_price" class="block text-sm font-medium text-gray-700 mb-1">Layout Price (₱)</label>
						<div class="relative">
							<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
								<span class="text-gray-500 sm:text-sm">₱</span>
							</div>
							<input type="number" name="layout_price" id="edit_layout_price" x-model="editingProduct ? editingProduct.layout_price : 0" step="0.01" min="0"
								   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
								   placeholder="0.00" :disabled="!editingProduct || !editingProduct.requires_layout">
						</div>
					</div>

					

					
					<div class="md:col-span-2">
						<label for="edit_product_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
						<textarea name="product_description" id="edit_product_description" x-model="editingProduct ? editingProduct.product_description : ''" rows="3" 
								  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-maroon focus:border-maroon"
								  placeholder="Detailed description of the product..."></textarea>
					</div>
				</div>


				<div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
					<button type="button" @click="editModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
						Cancel
					</button>
					<button type="submit" class="bg-maroon hover:bg-maroon-dark text-white px-6 py-2 rounded-md transition-colors">
						<i class="fas fa-save mr-2"></i>
						Update Product
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Archive Confirmation Modal -->
<div id="archiveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
	<div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
		<div class="mt-3 text-center">
			<div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100">
				<i class="fas fa-archive text-gray-600"></i>
			</div>
			<h3 class="text-lg font-medium text-gray-900 mt-2">Archive Product</h3>
			<div class="mt-2 px-7 py-3">
				<p class="text-sm text-gray-500">Are you sure you want to archive this product? It will be moved to archives.</p>
			</div>
			<div class="items-center px-4 py-3">
				<button id="confirmArchiveBtn" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 mr-2">
					Archive
				</button>
				<button onclick="closeArchiveModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
					Cancel
				</button>
			</div>
		</div>
	</div>
</div>

<script>
function editProduct(product) {
	this.editingProduct = product;
	this.editModal = true;
}

// Archive confirmation functionality
let productToArchive = null;

function confirmArchive(productId) {
	productToArchive = productId;
	document.getElementById('archiveModal').classList.remove('hidden');
}

function closeArchiveModal() {
	document.getElementById('archiveModal').classList.add('hidden');
	productToArchive = null;
}

document.getElementById('confirmArchiveBtn').addEventListener('click', function() {
	if (productToArchive) {
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/admin/products/${productToArchive}/archive`;
		
		const csrfToken = document.createElement('input');
		csrfToken.type = 'hidden';
		csrfToken.name = '_token';
		csrfToken.value = '{{ csrf_token() }}';
		form.appendChild(csrfToken);
		
		document.body.appendChild(form);
		form.submit();
	}
});

// Close modal when clicking outside
document.getElementById('archiveModal').addEventListener('click', function(e) {
	if (e.target === this) {
		closeArchiveModal();
	}
});
</script>

<style>
[x-cloak] { display: none !important; }

.line-clamp-2 {
	display: -webkit-box;
	-webkit-line-clamp: 2;
	-webkit-box-orient: vertical;
	overflow: hidden;
}
</style>

<script src="{{ asset('js/instant-search.js') }}"></script>
@endsection