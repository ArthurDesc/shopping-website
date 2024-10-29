document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    // Définir toutes les variables globalement dans la portée de DOMContentLoaded
    const filterForm = document.getElementById('filterForm');
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

        console.log('Filtres sélectionnés:', selectedFilters);
        console.log('Nombre de produits trouvés:', products.length);

        products.forEach(product => {
            const categories = product.dataset.categories?.split(',') || [];
            const marque = product.dataset.brand || '';
            const collection = product.dataset.collection || '';

            console.log('Données du produit:', {
                categories,
                marque,
                collection,
                element: product
            });

            const shouldDisplay = 
                (selectedFilters.categories.length === 0 || categories.some(cat => selectedFilters.categories.includes(cat))) &&
                (selectedFilters.marques.length === 0 || selectedFilters.marques.includes(marque)) &&
                (selectedFilters.collections.length === 0 || selectedFilters.collections.includes(collection));

            product.style.display = shouldDisplay ? '' : 'none';
        });

        updateURL(selectedFilters);
    }

    function updateURL() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();
        
        // Récupérer tous les filtres sélectionnés
        const selectedFilters = {
            categories: Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(el => el.value),
            marques: Array.from(document.querySelectorAll('input[name="marques[]"]:checked')).map(el => el.value),
            collections: Array.from(document.querySelectorAll('input[name="collections[]"]:checked')).map(el => el.value)
        };

        // Nettoyer les paramètres existants
        // Conserver uniquement les paramètres non liés aux filtres
        const preservedParams = ['page', 'sort']; // Ajoutez d'autres paramètres à préserver si nécessaire
        preservedParams.forEach(param => {
            if (url.searchParams.has(param)) {
                params.set(param, url.searchParams.get(param));
            }
        });

        // Ajouter les nouveaux paramètres de filtre
        Object.entries(selectedFilters).forEach(([key, values]) => {
            if (values.length > 0) {
                // Utiliser "category" au lieu de "categories" pour la cohérence
                const paramKey = key === 'categories' ? 'category' : key;
                params.set(paramKey, values.join(','));
            }
        });

        // Mettre à jour l'URL sans recharger la page
        const newUrl = `${url.pathname}${params.toString() ? '?' + params.toString() : ''}`;
        window.history.pushState({}, '', newUrl);
    }

    function updateActiveFilters() {
        if (!activeFiltersContainer) return;
        
        activeFiltersContainer.innerHTML = '';
        filterInputs.forEach(checkbox => {
            if (checkbox.checked) {
                // Rechercher spécifiquement le span contenant le texte
                const labelSpan = checkbox.closest('label').querySelector('span:not(.checkbox-path)');
                const label = labelSpan ? labelSpan.textContent.trim() : '';
                
                if (label) {
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
            }
        });
    }

    // Initialisation des filtres depuis l'URL
    function initializeFromURL() {
        const params = new URLSearchParams(window.location.search);
        
        // Gérer la conversion de "category" vers "categories[]"
        const categoryValue = params.get('category');
        if (categoryValue) {
            const values = categoryValue.split(',');
            values.forEach(value => {
                const checkbox = document.querySelector(`input[name="categories[]"][value="${value}"]`);
                if (checkbox) checkbox.checked = true;
            });
        }

        // Gérer les autres filtres
        ['marques', 'collections'].forEach(filterType => {
            const values = params.get(filterType)?.split(',') || [];
            values.forEach(value => {
                const checkbox = document.querySelector(`input[name="${filterType}[]"][value="${value}"]`);
                if (checkbox) checkbox.checked = true;
            });
        });

        applyFilters();
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
    initializeFromURL();
});
