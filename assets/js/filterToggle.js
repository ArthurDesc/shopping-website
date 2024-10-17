document.addEventListener('DOMContentLoaded', function() {
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const closeFiltersButton = document.getElementById('closeFilters');
    const applyFiltersButton = document.getElementById('applyFilters');
    const filterForm = document.getElementById('filterForm');

    function openFilters() {
        filterForm.classList.remove('-translate-x-full');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filterForm.classList.add('-translate-x-full');
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
        console.log('Filtres appliqués');
        // Exemple : fetchFilteredProducts();
    });
});