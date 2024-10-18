document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    const filterInputs = document.querySelectorAll('#filterForm input[type="checkbox"]');
    const products = document.querySelectorAll('.products_list > div > div');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const filterForm = document.getElementById('filterForm');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const closeFiltersButton = document.getElementById('closeFilters');
    const applyFiltersButton = document.getElementById('applyFilters');

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

            const categoryMatch = selectedFilters.categories.length === 0 || selectedFilters.categories.some(cat => categories.includes(cat));
            const marqueMatch = selectedFilters.marques.length === 0 || selectedFilters.marques.includes(marque);
            const collectionMatch = selectedFilters.collections.length === 0 || selectedFilters.collections.includes(collection);

            if (categoryMatch && marqueMatch && collectionMatch) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
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

    // Écouteur d'événements pour le bouton "Valider les filtres" sur mobile
    applyFiltersButton.addEventListener('click', function() {
        applyFilters();
        updateActiveFilters();
        closeFilters();
    });

    // Fonctions pour ouvrir/fermer le panneau de filtres sur mobile
    function openFilters() {
        filterForm.classList.remove('translate-y-full');
    }

    function closeFilters() {
        filterForm.classList.add('translate-y-full');
    }

    toggleFiltersButton.addEventListener('click', openFilters);
    closeFiltersButton.addEventListener('click', closeFilters);

    // Gestion du redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            filterForm.classList.remove('translate-y-full');
        } else {
            filterForm.classList.add('translate-y-full');
        }
    });

    // Appliquer les filtres et mettre à jour les étiquettes dès le chargement de la page
    applyFilters();
    updateActiveFilters();
});

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

        const categoryMatch = selectedFilters.categories.length === 0 || selectedFilters.categories.some(cat => categories.includes(cat));
        const marqueMatch = selectedFilters.marques.length === 0 || selectedFilters.marques.includes(marque);
        const collectionMatch = selectedFilters.collections.length === 0 || selectedFilters.collections.includes(collection);

        if (categoryMatch && marqueMatch && collectionMatch) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}
