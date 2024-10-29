document.addEventListener('DOMContentLoaded', function() {
    // Vérifier s'il y a des produits avant d'initialiser List.js
    const productContainer = document.querySelector('#products .list');
    if (!productContainer || !productContainer.children.length) {
        console.log('Aucun produit trouvé');
        return;
    }

    // Initialiser List.js
    const options = {
        valueNames: [
            'product-name',
            { data: ['category'] },
            { data: ['brand'] },
            { data: ['collection'] }
        ],
        searchClass: 'search',
        listClass: 'list'
    };

    try {
        const productList = new List('products', options);

        // Sélectionner toutes les checkboxes de filtre
        const filterCheckboxes = document.querySelectorAll('.checkbox-container input[type="checkbox"]');
        const activeFilters = {
            categories: new Set(),
            brands: new Set(),
            collections: new Set()
        };

        function toggleFilter(type, value) {
            if (activeFilters[type].has(value)) {
                activeFilters[type].delete(value);
            } else {
                activeFilters[type].add(value);
            }
        }

        function applyFilters() {
            productList.filter(function(item) {
                const element = item.elm;
                const categoryMatch = activeFilters.categories.size === 0 || 
                    [...activeFilters.categories].some(cat => element.dataset.category.includes(cat));
                const brandMatch = activeFilters.brands.size === 0 || 
                    activeFilters.brands.has(element.dataset.brand);
                const collectionMatch = activeFilters.collections.size === 0 || 
                    activeFilters.collections.has(element.dataset.collection);

                return categoryMatch && brandMatch && collectionMatch;
            });
        }

        // Ajouter des écouteurs d'événements pour chaque checkbox
        filterCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
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

        function createFilterTag(type, value, onRemove) {
            const tag = document.createElement('div');
            tag.className = 'filter-tag flex items-center bg-blue-50 text-blue-600 px-3 py-1 rounded-md text-sm';
            
            const text = document.createElement('span');
            text.textContent = value;
            
            const removeButton = document.createElement('button');
            removeButton.innerHTML = '&times;';
            removeButton.className = 'ml-2 font-bold hover:text-blue-800';
            removeButton.dataset.type = type;
            removeButton.dataset.value = value;
            removeButton.onclick = onRemove;
            
            tag.appendChild(text);
            tag.appendChild(removeButton);
            return tag;
        }

        function updateActiveFiltersDisplay() {
            const activeFiltersContainer = document.getElementById('activeFilters');
            activeFiltersContainer.innerHTML = '';

            filterCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const filterType = checkbox.dataset.category ? 'categories' :
                                     checkbox.dataset.brand ? 'brands' :
                                     'collections';
                    
                    const filterValue = checkbox.dataset.category ? checkbox.dataset.name :
                                       checkbox.dataset.brand ? checkbox.dataset.brand :
                                       checkbox.dataset.collection;

                    const tag = createFilterTag(filterType, filterValue, () => {
                        checkbox.checked = false;
                        checkbox.dispatchEvent(new Event('change'));
                    });
                    
                    activeFiltersContainer.appendChild(tag);
                }
            });

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
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de List.js:', error);
    }
}); 