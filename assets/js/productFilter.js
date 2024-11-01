document.addEventListener('DOMContentLoaded', function() {
    
    // Ajouter ces variables au début
    const noResults = document.getElementById('no-results');
    const resetFilters = document.getElementById('reset-filters');
    const productContainer = document.querySelector('#products .list');

    // Vérifier s'il y a des produits avant d'initialiser List.js
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

            // Ajouter cette vérification après le filtrage
            checkVisibleProducts();
        }

        function checkVisibleProducts() {
            const visibleProducts = productList.visibleItems.length;
            if (visibleProducts === 0) {
                productContainer.classList.add('hidden');
                noResults.classList.remove('hidden');
            } else {
                productContainer.classList.remove('hidden');
                noResults.classList.add('hidden');
            }
        }

        function updateURL(activeFilters) {
            const params = new URLSearchParams();
            
            // Ajouter les catégories actives
            if (activeFilters.categories.size > 0) {
                params.set('categories', Array.from(activeFilters.categories).join(','));
            }
            
            // Ajouter les marques actives
            if (activeFilters.brands.size > 0) {
                params.set('marques', Array.from(activeFilters.brands).join(','));
            }
            
            // Ajouter les collections actives
            if (activeFilters.collections.size > 0) {
                params.set('collections', Array.from(activeFilters.collections).join(','));
            }
            
            // Mettre à jour l'URL sans recharger la page
            const newURL = `${window.location.pathname}${params.toString() ? '?' + params.toString() : ''}`;
            window.history.pushState({}, '', newURL);
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
                updateURL(activeFilters);
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

        // Au chargement de la page, appliquer les filtres depuis l'URL
        const params = new URLSearchParams(window.location.search);
        
        // Appliquer les filtres depuis l'URL
        if (params.has('categories')) {
            params.get('categories').split(',').forEach(cat => {
                const checkbox = document.querySelector(`input[data-category="${cat}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
        
        if (params.has('marques')) {
            params.get('marques').split(',').forEach(brand => {
                const checkbox = document.querySelector(`input[data-brand="${brand}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
        
        if (params.has('collections')) {
            params.get('collections').split(',').forEach(collection => {
                const checkbox = document.querySelector(`input[data-collection="${collection}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }
        
        // Déclencher les filtres initiaux
        filterCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.dispatchEvent(new Event('change'));
            }
        });

        // Fonction de recherche pour filtrer les éléments
        function filterItems(searchInput, containerId) {
            const searchTerm = searchInput.value.toLowerCase();
            const container = document.querySelector(containerId);
            const items = container.querySelectorAll('.checkbox-container');
            
            items.forEach(item => {
                const text = item.querySelector('span').textContent.toLowerCase();
                const parentDiv = item.closest('div');
                
                if (text.includes(searchTerm)) {
                    item.style.display = 'flex';
                    // Si l'élément est dans une sous-catégorie, afficher aussi le parent
                    if (parentDiv.classList.contains('ml-6')) {
                        parentDiv.style.display = 'block';
                    }
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Pour les marques
        const marqueSearch = document.getElementById('marques-search');
        if (marqueSearch) {
            marqueSearch.addEventListener('input', () => {
                filterItems(marqueSearch, '#marques-filter');
            });
        }

        // Pour les catégories
        const categorySearch = document.getElementById('categories-search');
        if (categorySearch) {
            categorySearch.addEventListener('input', () => {
                filterItems(categorySearch, '#categories-filter');
            });
        }

        // Ajouter l'écouteur d'événements pour le bouton reset
        resetFilters?.addEventListener('click', () => {
            // Réinitialiser les filtres actifs
            activeFilters.categories.clear();
            activeFilters.brands.clear();
            activeFilters.collections.clear();

            // Décocher toutes les checkboxes
            filterCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            // Réinitialiser l'input de recherche
            const searchInput = document.getElementById('search-products');
            if (searchInput) {
                searchInput.value = '';
                // Déclencher la recherche pour mettre à jour la liste
                productList.search();
            }

            // Réinitialiser la recherche et les filtres
            productList.search();
            productList.filter();

            // Mettre à jour l'affichage
            updateActiveFiltersDisplay();
            updateURL(activeFilters);
            checkVisibleProducts();

            // Réinitialiser l'URL
            window.history.pushState({}, '', window.location.pathname);
        });

        // Ajouter la vérification après la recherche
        productList.on('searchComplete', checkVisibleProducts);
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de List.js:', error);
    }
}); 