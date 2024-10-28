document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    // Définir toutes les variables globalement dans la portée de DOMContentLoaded
    const filterInputs = document.querySelectorAll('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]');
    const products = document.querySelectorAll('.product-card'); // Changé pour .product-card
    const activeFiltersContainer = document.getElementById('activeFilters');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const filterMenu = document.getElementById('filterMenu');

    function getSelectedValues(name) {
        return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(el => el.value);
    }

    function applyFilters() {
        const selectedFilters = {
            categories: getSelectedValues('categories'),
            marques: getSelectedValues('marques'),
            collections: getSelectedValues('collections')
        };

        products.forEach(product => {
            const categories = product.dataset.categories?.split(',') || [];
            const marque = product.dataset.brand || '';
            const collection = product.dataset.collection || '';

            const shouldDisplay = 
                (selectedFilters.categories.length === 0 || categories.some(cat => selectedFilters.categories.includes(cat))) &&
                (selectedFilters.marques.length === 0 || selectedFilters.marques.includes(marque)) &&
                (selectedFilters.collections.length === 0 || selectedFilters.collections.includes(collection));

            product.style.display = shouldDisplay ? '' : 'none';
        });

        updateActiveFilters(selectedFilters);
        updateUrlWithFilters(selectedFilters);
    }

    function updateUrlWithFilters(filters) {
        const url = new URL(window.location.href);
        
        // Ne pas effacer les paramètres existants s'ils ne sont pas modifiés
        if (filters.collections.length > 0) {
            url.searchParams.set('collection', filters.collections[0]);
        } else if (!document.querySelector('input[name="collections[]"]:checked')) {
            // Ne supprimer que si aucune case n'est cochée
            url.searchParams.delete('collection');
        }
        
        if (filters.categories.length > 0) {
            url.searchParams.set('category', filters.categories[0]);
        } else if (!document.querySelector('input[name="categories[]"]:checked')) {
            // Ne supprimer que si aucune case n'est cochée
            url.searchParams.delete('category');
        }
        
        // Mettre à jour l'URL sans recharger la page
        window.history.pushState({}, '', url);
    }

    function updateActiveFilters() {
        if (!activeFiltersContainer) return;
        
        activeFiltersContainer.innerHTML = '';
        filterInputs.forEach(checkbox => {
            if (checkbox.checked) {
                const label = checkbox.nextElementSibling.textContent.trim();
                const tag = document.createElement('span');
                tag.className = 'filter-tag bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full';
                tag.innerHTML = `
                    ${label}
                    <svg class="inline-block ml-1 w-4 h-4 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
                tag.querySelector('svg').addEventListener('click', () => {
                    checkbox.checked = false;
                    applyFilters();
                    updateActiveFilters();
                });
                activeFiltersContainer.appendChild(tag);
            }
        });
    }

    // Initialisation des filtres depuis l'URL
    function initializeFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const collection = urlParams.get('collection');
        const category = urlParams.get('category');

        // Vérifier si les checkboxes existent avant de les cocher
        if (collection) {
            const collectionCheckbox = document.querySelector(`input[name="collections[]"][value="${collection.toLowerCase()}"]`);
            if (collectionCheckbox) {
                collectionCheckbox.checked = true;
                // Appliquer les filtres sans mettre à jour l'URL
                applyFiltersWithoutUrlUpdate();
            }
        }

        if (category) {
            const categoryCheckbox = document.querySelector(`input[name="categories[]"][value="${category}"]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
                // Appliquer les filtres sans mettre à jour l'URL
                applyFiltersWithoutUrlUpdate();
            }
        }
    }

    // Nouvelle fonction pour appliquer les filtres sans mettre à jour l'URL
    function applyFiltersWithoutUrlUpdate() {
        const selectedFilters = {
            categories: getSelectedValues('categories'),
            marques: getSelectedValues('marques'),
            collections: getSelectedValues('collections')
        };

        products.forEach(product => {
            const categories = product.dataset.categories ? product.dataset.categories.split(',') : [];
            const marque = product.dataset.brand || '';
            const collection = product.dataset.collection || '';

            const categoryMatch = selectedFilters.categories.length === 0 || 
                selectedFilters.categories.some(cat => categories.includes(cat));
            const marqueMatch = selectedFilters.marques.length === 0 || 
                selectedFilters.marques.includes(marque);
            const collectionMatch = selectedFilters.collections.length === 0 || 
                selectedFilters.collections.includes(collection);

            if (categoryMatch && marqueMatch && collectionMatch) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }

    // Ajouter les écouteurs d'événements
    filterInputs.forEach(input => {
        input.addEventListener('change', () => {
            applyFilters();
            updateActiveFilters();
        });
    });

    if (toggleFiltersButton && filterMenu) {
        toggleFiltersButton.addEventListener('click', () => {
            filterMenu.classList.toggle('hidden');
        });
    }

    // Recherche de produits
    const searchInput = document.getElementById('products-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            products.forEach(product => {
                const productName = product.querySelector('h3').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    product.style.display = '';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    }

    // Initialiser les filtres au chargement
    initializeFromUrl();
});
