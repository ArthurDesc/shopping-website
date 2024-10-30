document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des éléments du DOM
    const filterForm = document.getElementById('filterForm');
    const filterInputs = document.querySelectorAll('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]');
    const products = document.querySelectorAll('.product-card');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const filterMenu = document.getElementById('filterMenu');

    // Vérifier si les éléments nécessaires existent
    if (!filterInputs.length || !products.length) return;

    // Fonctions utilitaires
    function getSelectedValues(name) {
        return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(el => el.value);
    }

    // Fonction principale de filtrage
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
        updateActiveFilters();
    }

    // Mise à jour de l'URL
    function updateURL(selectedFilters) {
        const url = new URL(window.location.href);
        const params = new URLSearchParams();
        
        // Ajouter les paramètres de filtre
        Object.entries(selectedFilters).forEach(([key, values]) => {
            if (values.length > 0) {
                if (key === 'collections') {
                    params.set('collection', values[0]);
                } else if (key === 'categories') {
                    params.set('categories', values.join(','));
                } else {
                    params.set(key, values.join(','));
                }
            }
        });

        window.history.pushState({}, '', `${url.pathname}${params.toString() ? '?' + params.toString() : ''}`);
    }

    // Mise à jour des filtres actifs
    function updateActiveFilters() {
        if (!activeFiltersContainer) return;
        
        activeFiltersContainer.innerHTML = '';
        filterInputs.forEach(checkbox => {
            if (checkbox.checked) {
                const labelSpan = checkbox.closest('label').querySelector('span:not(.checkbox-path)');
                const label = labelSpan ? labelSpan.textContent.trim() : '';
                
                if (label) {
                    const tag = createFilterTag(label, () => {
                        checkbox.checked = false;
                        applyFilters();
                    });
                    activeFiltersContainer.appendChild(tag);
                }
            }
        });
    }

    // Création d'un tag de filtre
    function createFilterTag(label, onRemove) {
        const tag = document.createElement('span');
        tag.className = 'filter-tag bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full';
        tag.innerHTML = `
            ${label}
            <svg class="inline-block ml-1 w-4 h-4 cursor-pointer" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        `;
        tag.querySelector('svg').addEventListener('click', onRemove);
        return tag;
    }

    // Initialisation depuis l'URL
    function initializeFilters() {
        const params = new URLSearchParams(window.location.search);
        
        // Initialiser les collections
        const collection = params.get('collection');
        if (collection) {
            const collectionCheckbox = document.querySelector(`input[name="collections[]"][value="${collection}"]`);
            if (collectionCheckbox) {
                collectionCheckbox.checked = true;
            }
        }

        // Initialiser les catégories
        const categories = params.get('categories')?.split(',') || [];
        categories.forEach(category => {
            const categoryCheckbox = document.querySelector(`input[name="categories[]"][value="${category}"]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
            }
        });

        // Mettre à jour les filtres actifs
        updateActiveFilters();
    }

    // Écouteurs d'événements
    filterInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
    });

    if (toggleFiltersButton && filterMenu) {
        toggleFiltersButton.addEventListener('click', () => {
            filterMenu.classList.toggle('hidden');
        });
    }

    // Écouteur d'événements pour les liens de collection
    document.querySelectorAll('[data-collection]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const collection = link.dataset.collection;
            updateURL({ collection: collection });
            applyFilters();
        });
    });

    // Initialisation
    initializeFilters();
});

