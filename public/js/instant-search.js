/**
 * Instant Search Functionality
 * Provides AJAX-based instant search for admin pages
 */
class InstantSearch {
    constructor(options) {
        this.searchInput = document.getElementById(options.searchInputId);
        this.loadingElement = document.getElementById(options.loadingId);
        this.containerElement = document.getElementById(options.containerId);
        this.searchUrl = options.searchUrl;
        this.searchTimeout = null;
        this.debounceDelay = options.debounceDelay || 300;
        
        this.init();
    }
    
    init() {
        if (!this.searchInput || !this.containerElement) {
            console.warn('InstantSearch: Required elements not found');
            return;
        }
        
        this.searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value.trim());
        });
    }
    
    handleSearch(query) {
        // Clear previous timeout
        clearTimeout(this.searchTimeout);
        
        // Show loading indicator
        if (this.loadingElement) {
            this.loadingElement.classList.remove('hidden');
        }
        
        // Set new timeout for search
        this.searchTimeout = setTimeout(() => {
            this.performSearch(query);
        }, this.debounceDelay);
    }
    
    performSearch(query) {
        // Create URL with search parameter
        const url = new URL(window.location.href);
        if (query) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
        }
        
        // Preserve other filters
        const form = document.querySelector('form[method="GET"]');
        if (form) {
            const formData = new FormData(form);
            for (let [key, value] of formData.entries()) {
                if (key !== 'search' && value) {
                    url.searchParams.set(key, value);
                }
            }
        }
        
        // Perform AJAX request
        fetch(url.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            }
        })
        .then(response => response.text())
        .then(html => {
            // Parse the response to extract just the table content
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById(this.containerElement.id);
            
            if (newContainer) {
                // Only update if there are actual results or if search is empty
                const hasResults = newContainer.querySelector('tbody tr:not([colspan])') || 
                                 newContainer.querySelector('.no-results') ||
                                 query === '';
                
                if (hasResults || query === '') {
                    this.containerElement.innerHTML = newContainer.innerHTML;
                } else {
                    // Show only the table structure with no data rows
                    const table = this.containerElement.querySelector('table');
                    if (table) {
                        const tbody = table.querySelector('tbody');
                        if (tbody) {
                            tbody.innerHTML = '';
                        }
                    }
                }
            }
            
            // Hide loading indicator
            if (this.loadingElement) {
                this.loadingElement.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            if (this.loadingElement) {
                this.loadingElement.classList.add('hidden');
            }
        });
    }
}

// Auto-initialize instant search if elements exist
document.addEventListener('DOMContentLoaded', function() {
    // Check for common instant search patterns
    const searchInputs = document.querySelectorAll('[data-instant-search]');
    
    searchInputs.forEach(input => {
        const containerId = input.getAttribute('data-container');
        const loadingId = input.getAttribute('data-loading');
        
        if (containerId) {
            new InstantSearch({
                searchInputId: input.id,
                loadingId: loadingId,
                containerId: containerId,
                debounceDelay: 300
            });
        }
    });
});
