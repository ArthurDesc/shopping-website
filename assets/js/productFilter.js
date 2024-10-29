document.addEventListener('DOMContentLoaded', function() {
    // Initialiser List.js
    const options = {
        valueNames: [
            { data: ['category'] },
            { data: ['brand'] },
            { data: ['collection'] },
            'product-name',
            'product-price'
        ],
        searchClass: 'search',
        listClass: 'list'
    };

    const productList = new List('products', options);

    // Sélectionner toutes les checkboxes de filtre
    const filterCheckboxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
    const activeFilters = {
        categories: new Set(),
        brands: new Set(),
        collections: new Set()
    };

    // Ajouter des écouteurs d'événements pour chaque checkbox
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const container = this.closest('.checkbox-container');
            
            if (this.dataset.category) {
                toggleFilter('categories', this.dataset.category);
            } else if (this.dataset.brand) {
                toggleFilter('brands', this.dataset.brand);
            } else if (this.dataset.collection) {
                toggleFilter('collections', this.dataset.collection);
            }
            
            applyFilters();
            updateActiveFiltersDisplay();
        });
    });

    function toggleFilter(type, value) {
        if (activeFilters[type].has(value)) {
            activeFilters[type].delete(value);
        } else {
            activeFilters[type].add(value);
        }
    }

    function applyFilters() {
        productList.filter(function(item) {
            const itemCategories = (item.values().category || '').split(',');
            const itemBrand = item.values().brand || '';
            const itemCollection = item.values().collection || '';

            const categoryMatch = activeFilters.categories.size === 0 || 
                                [...activeFilters.categories].some(cat => itemCategories.includes(cat));
            const brandMatch = activeFilters.brands.size === 0 || 
                             activeFilters.brands.has(itemBrand);
            const collectionMatch = activeFilters.collections.size === 0 || 
                                  activeFilters.collections.has(itemCollection);

            return categoryMatch && brandMatch && collectionMatch;
        });
    }

    function updateActiveFiltersDisplay() {
        const activeFiltersContainer = document.getElementById('activeFilters');
        if (!activeFiltersContainer) return;
        
        activeFiltersContainer.innerHTML = '';

        function createFilterTag(value, type) {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.innerHTML = `
                ${value}
                <button type="button" class="ml-2" data-type="${type}" data-value="${value}">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"/>
                    </svg>
                </button>
            `;
            return tag;
        }

        for (const [type, values] of Object.entries(activeFilters)) {
            values.forEach(value => {
                const tag = createFilterTag(value, type);
                activeFiltersContainer.appendChild(tag);
            });
        }

        // Gérer la suppression des filtres
        activeFiltersContainer.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.type;
                const value = this.dataset.value;
                
                // Décocher la checkbox correspondante
                const checkbox = document.querySelector(`input[data-${type.slice(0, -1)}="${value}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                }
                
                activeFilters[type].delete(value);
                applyFilters();
                updateActiveFiltersDisplay();
            });
        });
    }
}); 