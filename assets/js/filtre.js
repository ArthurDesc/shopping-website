document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    const filterInputs = document.querySelectorAll('#filterForm input[type="checkbox"]');
    const products = document.querySelectorAll('.products_list > div > div');

    // Ajouter un écouteur d'événements à chaque case à cocher
    filterInputs.forEach(input => {
        input.addEventListener('change', applyFilters);
    });

    function getSelectedValues(name) {
        const selectedValues = Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(el => el.value);
        console.log(`Valeurs sélectionnées pour ${name}:`, selectedValues);
        return selectedValues;
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

    // Appliquer les filtres dès le chargement de la page
    applyFilters();

    const filterForm = document.getElementById('filterForm');
    const applyFiltersButton = document.getElementById('applyFilters');

    applyFiltersButton.addEventListener('click', function(e) {
        e.preventDefault();
        filterForm.submit();
    });
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

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const closeFiltersButton = document.getElementById('closeFilters');
    const activeFiltersContainer = document.getElementById('activeFilters');
    const filterTitle = document.getElementById('filterTitle');

    function openFilters() {
        filterForm.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filterForm.classList.add('translate-y-full');
        document.body.style.overflow = '';
    }

    toggleFiltersButton.addEventListener('click', openFilters);
    closeFiltersButton.addEventListener('click', closeFilters);

    // Fonction pour mettre à jour les étiquettes des filtres actifs
    function updateActiveFilters() {
        activeFiltersContainer.innerHTML = '';
        let activeFilters = [];

        filterForm.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            let filterType = checkbox.name.replace('[]', '');
            let filterValue = checkbox.nextElementSibling.textContent.trim();
            activeFilters.push({ type: filterType, value: filterValue });

            let tag = document.createElement('span');
            tag.className = 'bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full';
            tag.textContent = filterValue;
            activeFiltersContainer.appendChild(tag);
        });

        filterTitle.textContent = activeFilters.length > 0 ? "Articles filtrés" : "Tous les articles";

        // Appliquer les filtres aux produits
        applyFilters(activeFilters);
    }

    // Ajouter des écouteurs d'événements à toutes les cases à cocher
    filterForm.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', updateActiveFilters);
    });

    // Fonction pour appliquer les filtres aux produits
    function applyFilters(activeFilters) {
        const products = document.querySelectorAll('.products_list .bg-white');
        products.forEach(product => {
            let shouldShow = true;
            activeFilters.forEach(filter => {
                if (filter.type === 'categories') {
                    if (!product.dataset.categories.includes(filter.value)) {
                        shouldShow = false;
                    }
                } else if (filter.type === 'marques') {
                    if (product.dataset.brand !== filter.value) {
                        shouldShow = false;
                    }
                } else if (filter.type === 'collections') {
                    if (product.dataset.collection !== filter.value) {
                        shouldShow = false;
                    }
                }
            });
            product.style.display = shouldShow ? '' : 'none';
        });
    }

    // Appliquer les filtres au chargement de la page
    updateActiveFilters();
});
