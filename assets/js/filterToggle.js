document.addEventListener('DOMContentLoaded', function() {
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const closeFiltersButton = document.getElementById('closeFilters');
    const applyFiltersButton = document.getElementById('applyFilters');
    const filterForm = document.getElementById('filterForm');

    function openFilters() {
        filterForm.classList.remove('translate-y-full');
        filterForm.classList.add('translate-y-0');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filterForm.classList.remove('translate-y-0');
        filterForm.classList.add('translate-y-full');
        document.body.style.overflow = '';
    }

    toggleFiltersButton.addEventListener('click', openFilters);
    closeFiltersButton.addEventListener('click', closeFilters);

    applyFiltersButton.addEventListener('click', function() {
        // Logique pour appliquer les filtres
        if (window.innerWidth < 768) { // Si on est en version mobile
            closeFilters();
        }
        // Ajoutez ici la logique pour appliquer les filtres et mettre à jour la liste des produits
        applyFilters(); // Nouvelle fonction à créer pour appliquer les filtres
        console.log('Filtres appliqués');
    });

    function applyFilters() {
        // Récupérer tous les filtres sélectionnés
        const selectedCategories = Array.from(document.querySelectorAll('input[name="categories[]"]:checked')).map(el => el.value);
        const selectedBrands = Array.from(document.querySelectorAll('input[name="marques[]"]:checked')).map(el => el.value);
        const selectedCollections = Array.from(document.querySelectorAll('input[name="collections[]"]:checked')).map(el => el.value);

        // Filtrer les produits
        const products = document.querySelectorAll('.products_list > div > div');
        products.forEach(product => {
            const categories = product.dataset.categories.split(',');
            const brand = product.dataset.brand;
            const collection = product.dataset.collection;

            const categoryMatch = selectedCategories.length === 0 || categories.some(cat => selectedCategories.includes(cat));
            const brandMatch = selectedBrands.length === 0 || selectedBrands.includes(brand);
            const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(collection);

            if (categoryMatch && brandMatch && collectionMatch) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });

        // Mettre à jour le titre des filtres
        updateFilterTitle();
    }

    function updateFilterTitle() {
        // Logique pour mettre à jour le titre des filtres
        // ... à implémenter ...
    }
});
