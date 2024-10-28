document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    const filterInputs = document.querySelectorAll('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]');
    const products = document.querySelectorAll('.products_list > div > div');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const filterMenu = document.getElementById('filterMenu');

    // Fonction pour obtenir les paramètres de l'URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Fonction pour cocher les filtres basés sur l'URL
    function checkFiltersFromUrl() {
        const collection = getUrlParameter('collection');
        const category = getUrlParameter('category');

        if (collection) {
            const collectionCheckbox = document.querySelector(`input[name="collections[]"][value="${collection}"i]`);
            if (collectionCheckbox) {
                collectionCheckbox.checked = true;
            }
        }

        if (category) {
            const categoryCheckbox = document.querySelector(`input[name="categories[]"][value="${category}"i]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
            }
        }
    }

    // Appeler la fonction pour cocher les filtres basés sur l'URL
    checkFiltersFromUrl();

    // Fonction pour afficher/masquer le menu des filtres
    function toggleFilterMenu() {
        filterMenu.classList.toggle('hidden');
    }

    // Écouteur d'événements pour le bouton de filtres
    toggleFiltersButton.addEventListener('click', toggleFilterMenu);

    // Ajouter un écouteur d'événements à chaque case à cocher
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            applyFilters();
            updateActiveFilters();
        });
    });

    function getSelectedValues(name) {
        return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(el => el.value);
    }

    function applyFilters() {
        const selectedFilters = {
            collection: getSelectedValues('collections')[0], // Prend la première valeur
            category: getSelectedValues('categories')[0]     // Prend la première valeur
        };
        
        // Mettre à jour l'URL
        updateUrlWithFilters(selectedFilters);
        
        // Filtrer les produits
        products.forEach(product => {
            const productCollection = product.dataset.collection;
            const productCategories = product.dataset.categories.split(',');
            
            const collectionMatch = !selectedFilters.collection || productCollection === selectedFilters.collection;
            const categoryMatch = !selectedFilters.category || productCategories.includes(selectedFilters.category);

            product.style.display = (collectionMatch && categoryMatch) ? '' : 'none';
        });
    }

    function updateActiveFilters() {
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

    // Appliquer les filtres initiaux
    applyFilters();
    updateActiveFilters();
});

// Garder la fonction globale si nécessaire
window.applyFilters = function() {
    console.log("Fonction applyFilters appelée");

    const selectedFilters = {
        categories: getSelectedValues('categories'),
        marques: getSelectedValues('marques'),
        collections: getSelectedValues('collections')
    };

    console.log("Filtres sélectionnés:", selectedFilters);

    products.forEach(product => {
        const categories = product.dataset.categories ? product.dataset.categories.split(',') : [];
        const marque = product.dataset.brand || '';
        const collection = product.dataset.collection || '';

        const categoryMatch = selectedFilters.categories.length === 0 || 
            selectedFilters.categories.some(cat => categories.includes(cat));
        const marqueMatch = selectedFilters.marques.length === 0 || selectedFilters.marques.includes(marque);
        const collectionMatch = selectedFilters.collections.length === 0 || selectedFilters.collections.includes(collection);

        if (categoryMatch && marqueMatch && collectionMatch) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}

$(document).ready(function() {
    // Fonction de recherche pour les produits
    $('#products-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.product-card').each(function() {
            const productName = $(this).find('h3').text().toLowerCase();
            if (productName.includes(searchTerm)) {
                $(this).show(); // Afficher le produit si le nom contient le terme de recherche
            } else {
                $(this).hide(); // Masquer le produit s'il ne contient pas le terme de recherche
            }
        });
    });
});

// Fonction pour mettre à jour l'URL avec les filtres sélectionnés
function updateUrlWithFilters(filters) {
    const url = new URL(window.location.href);
    
    // Nettoyer les paramètres existants
    url.searchParams.delete('collection');
    url.searchParams.delete('category');
    
    // Ajouter les nouveaux paramètres
    if (filters.collections.length > 0) {
        url.searchParams.set('collection', filters.collections[0]);
    }
    if (filters.categories.length > 0) {
        url.searchParams.set('category', filters.categories[0]);
    }
    
    // Mettre à jour l'URL sans recharger la page
    window.history.pushState({}, '', url);
}
