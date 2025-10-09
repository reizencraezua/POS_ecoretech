@props(['selectedCustomer' => null, 'placeholder' => 'Search customers...', 'required' => false])

<div x-data="customerSearch()" class="relative">
    <!-- Customer Search Input -->
    <div class="relative">
        <input type="text" 
               x-model="searchQuery"
               @input="searchCustomers()"
               @focus="showDropdown = true"
               @blur="setTimeout(() => showDropdown = false, 200)"
               placeholder="{{ $placeholder }}"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon focus:border-maroon"
               :class="{ 'border-red-500': required && !selectedCustomer }"
               required="{{ $required ? 'required' : '' }}">
        
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <i class="fas fa-search text-gray-400"></i>
        </div>
    </div>

    <!-- Selected Customer Display -->
    <div x-show="selectedCustomer" class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900" x-text="selectedCustomer ? selectedCustomer.customer_firstname + ' ' + selectedCustomer.customer_lastname : ''"></p>
                    <p class="text-sm text-gray-600" x-text="selectedCustomer ? selectedCustomer.customer_contact : ''"></p>
                </div>
            </div>
            <button @click="clearSelection()" class="text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    <div x-show="showDropdown && customers.length > 0" 
         x-transition
         class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
        <div class="py-1">
            <template x-for="customer in customers" :key="customer.customer_id">
                <div @click="selectCustomer(customer)" 
                     class="px-4 py-3 hover:bg-gray-100 cursor-pointer flex items-center space-x-3">
                    <div class="w-8 h-8 bg-maroon rounded-full flex items-center justify-center text-white text-sm font-bold">
                        <span x-text="customer.customer_firstname.charAt(0)"></span>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900" x-text="customer.customer_firstname + ' ' + customer.customer_lastname"></p>
                        <p class="text-sm text-gray-600" x-text="customer.customer_contact"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- No Results -->
    <div x-show="showDropdown && searchQuery.length > 2 && customers.length === 0" 
         x-transition
         class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
        <div class="px-4 py-3 text-center text-gray-500">
            <i class="fas fa-search text-gray-400 mb-2"></i>
            <p>No customers found</p>
        </div>
    </div>

    <!-- Hidden Input for Form Submission -->
    <input type="hidden" name="customer_id" x-model="selectedCustomer ? selectedCustomer.customer_id : ''">
</div>

<script>
function customerSearch() {
    return {
        searchQuery: '',
        customers: [],
        selectedCustomer: @json($selectedCustomer),
        showDropdown: false,
        searchTimeout: null,

        init() {
            if (this.selectedCustomer) {
                this.searchQuery = this.selectedCustomer.customer_firstname + ' ' + this.selectedCustomer.customer_lastname;
            }
        },

        searchCustomers() {
            clearTimeout(this.searchTimeout);
            
            if (this.searchQuery.length < 2) {
                this.customers = [];
                return;
            }

            this.searchTimeout = setTimeout(() => {
                fetch(`/api/customers/search?q=${encodeURIComponent(this.searchQuery)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.customers = data;
                    })
                    .catch(error => {
                        console.error('Error searching customers:', error);
                        this.customers = [];
                    });
            }, 300);
        },

        selectCustomer(customer) {
            this.selectedCustomer = customer;
            this.searchQuery = customer.customer_firstname + ' ' + customer.customer_lastname;
            this.showDropdown = false;
            this.customers = [];
        },

        clearSelection() {
            this.selectedCustomer = null;
            this.searchQuery = '';
            this.customers = [];
        }
    }
}
</script>
