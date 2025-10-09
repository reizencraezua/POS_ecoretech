@props(['selectedItems' => [], 'type' => 'both']) {{-- 'products', 'services', or 'both' --}}

<div x-data="productServiceSelector()" class="space-y-4">
    <!-- Search and Filter -->
    <div class="flex space-x-4">
        <div class="flex-1">
            <input type="text" 
                   x-model="searchQuery"
                   @input="searchItems()"
                   placeholder="Search products and services..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
        </div>
        <div>
            <select x-model="selectedCategory" @change="searchItems()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                <option value="">All Categories</option>
                <template x-for="category in categories" :key="category.category_id">
                    <option :value="category.category_id" x-text="category.category_name"></option>
                </template>
            </select>
        </div>
        <div>
            <select x-model="itemType" @change="searchItems()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon">
                <option value="all">All Items</option>
                <option value="products">Products Only</option>
                <option value="services">Services Only</option>
            </select>
        </div>
    </div>

    <!-- Search Results -->
    <div x-show="items.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto">
        <template x-for="item in items" :key="item.id">
            <div @click="addItem(item)" 
                 class="p-4 border border-gray-200 rounded-lg hover:border-maroon hover:shadow-md cursor-pointer transition-all">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900" x-text="item.name"></h4>
                        <p class="text-sm text-gray-600" x-text="item.description"></p>
                        <div class="mt-2 flex items-center space-x-2">
                            <span class="text-lg font-bold text-maroon" x-text="'₱' + parseFloat(item.price).toFixed(2)"></span>
                            <span class="text-xs px-2 py-1 rounded-full" 
                                  :class="item.type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'"
                                  x-text="item.type === 'product' ? 'Product' : 'Service'"></span>
                        </div>
                    </div>
                    <button class="text-maroon hover:text-maroon-dark">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </template>
    </div>

    <!-- No Results -->
    <div x-show="searchQuery.length > 2 && items.length === 0" class="text-center py-8 text-gray-500">
        <i class="fas fa-search text-gray-400 text-2xl mb-2"></i>
        <p>No items found</p>
    </div>

    <!-- Selected Items -->
    <div x-show="selectedItems.length > 0" class="space-y-2">
        <h3 class="font-medium text-gray-900">Selected Items</h3>
        <div class="space-y-2">
            <template x-for="(item, index) in selectedItems" :key="item.id + '_' + index">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                 :class="item.type === 'product' ? 'bg-blue-500' : 'bg-green-500'">
                                <i :class="item.type === 'product' ? 'fas fa-box' : 'fas fa-cogs'"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900" x-text="item.name"></p>
                                <p class="text-sm text-gray-600" x-text="'₱' + parseFloat(item.price).toFixed(2)"></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center space-x-1">
                            <button @click="decreaseQuantity(index)" class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input type="number" 
                                   x-model="item.quantity"
                                   @input="updateTotal()"
                                   min="1" 
                                   class="w-12 text-center border border-gray-300 rounded">
                            <button @click="increaseQuantity(index)" class="w-6 h-6 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <button @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Total -->
        <div class="border-t pt-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-medium text-gray-900">Total:</span>
                <span class="text-xl font-bold text-maroon" x-text="'₱' + total.toFixed(2)"></span>
            </div>
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <template x-for="(item, index) in selectedItems" :key="'hidden_' + index">
        <input type="hidden" :name="'items[' + index + '][id]'" :value="item.id">
        <input type="hidden" :name="'items[' + index + '][type]'" :value="item.type">
        <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
        <input type="hidden" :name="'items[' + index + '][price]'" :value="item.price">
    </template>
</div>

<script>
function productServiceSelector() {
    return {
        searchQuery: '',
        selectedCategory: '',
        itemType: 'all',
        items: [],
        selectedItems: @json($selectedItems),
        categories: [],
        total: 0,

        init() {
            this.loadCategories();
            this.updateTotal();
        },

        loadCategories() {
            fetch('/api/categories')
                .then(response => response.json())
                .then(data => {
                    this.categories = data;
                })
                .catch(error => console.error('Error loading categories:', error));
        },

        searchItems() {
            if (this.searchQuery.length < 2) {
                this.items = [];
                return;
            }

            const params = new URLSearchParams({
                q: this.searchQuery,
                category: this.selectedCategory,
                type: this.itemType
            });

            fetch(`/api/items/search?${params}`)
                .then(response => response.json())
                .then(data => {
                    this.items = data;
                })
                .catch(error => console.error('Error searching items:', error));
        },

        addItem(item) {
            const existingIndex = this.selectedItems.findIndex(selected => 
                selected.id === item.id && selected.type === item.type
            );

            if (existingIndex !== -1) {
                this.selectedItems[existingIndex].quantity += 1;
            } else {
                this.selectedItems.push({
                    ...item,
                    quantity: 1
                });
            }

            this.updateTotal();
        },

        removeItem(index) {
            this.selectedItems.splice(index, 1);
            this.updateTotal();
        },

        increaseQuantity(index) {
            this.selectedItems[index].quantity += 1;
            this.updateTotal();
        },

        decreaseQuantity(index) {
            if (this.selectedItems[index].quantity > 1) {
                this.selectedItems[index].quantity -= 1;
                this.updateTotal();
            }
        },

        updateTotal() {
            this.total = this.selectedItems.reduce((sum, item) => {
                return sum + (parseFloat(item.price) * item.quantity);
            }, 0);
        }
    }
}
</script>
