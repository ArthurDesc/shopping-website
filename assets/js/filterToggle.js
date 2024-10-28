document.addEventListener('DOMContentLoaded', function() {
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const filterForm = document.getElementById('filterForm');

    if (toggleFiltersButton && filterForm) {
        toggleFiltersButton.addEventListener('click', function() {
            // Basculer la classe pour l'animation de translation
            filterForm.style.transform = filterForm.style.transform === 'translateY(0)' ? 'translateY(100%)' : 'translateY(0)';
            
            // Mettre à jour l'état du bouton
            const isExpanded = filterForm.style.transform === 'translateY(0)';
            toggleFiltersButton.setAttribute('aria-expanded', isExpanded);
        });
    }

    // Fermeture du menu au clic sur le bouton de fermeture
    const closeFiltersButton = document.getElementById('closeFilters');
    if (closeFiltersButton) {
        closeFiltersButton.addEventListener('click', function() {
            filterForm.style.transform = 'translateY(100%)';
            toggleFiltersButton.setAttribute('aria-expanded', 'false');
        });
    }

    // Fermeture du menu au clic en dehors
    document.addEventListener('click', function(event) {
        // Vérifier si on est en mode mobile (largeur d'écran < 768px)
        if (window.innerWidth < 768) {
            if (filterForm && !filterForm.contains(event.target) && !toggleFiltersButton.contains(event.target)) {
                filterForm.style.transform = 'translateY(100%)';
                toggleFiltersButton.setAttribute('aria-expanded', 'false');
            }
        }
    });
});
