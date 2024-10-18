document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    const filterForm = document.getElementById('filterForm');
    const toggleFiltersBtn = document.getElementById('toggleFilters');
    const closeFiltersBtn = document.getElementById('closeFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const filterInputs = document.querySelectorAll('#filterForm input[type="checkbox"]');
    const products = document.querySelectorAll('.products_list > div > div');

    // Fonction pour ouvrir le menu des filtres sur mobile
    function openFilters() {
        console.log("Ouverture des filtres");
        filterForm.classList.remove('translate-y-full');
        filterForm.classList.add('translate-y-0');
        document.body.style.overflow = 'hidden'; // Empêche le défilement du body sur mobile
    }

    // Fonction pour fermer le menu des filtres sur mobile
    function closeFilters() {
        console.log("Fermeture des filtres");
        if (window.innerWidth < 768) { // Vérifier si on est en version mobile
            filterForm.classList.remove('translate-y-0');
            filterForm.classList.add('translate-y-full');
            document.body.style.overflow = ''; // Réactive le défilement du body
        }
    }

    // Écouteurs d'événements pour les boutons mobile
    if (toggleFiltersBtn) toggleFiltersBtn.addEventListener('click', openFilters);
    if (closeFiltersBtn) closeFiltersBtn.addEventListener('click', closeFilters);

    // Écouteur d'événement pour le bouton Valider (mobile et desktop)
    applyFiltersBtn.addEventListener('click', function() {
        applyFilters();
        closeFilters();
    });

    // Fonction pour appliquer les filtres
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

    function getSelectedValues(name) {
        const selectedValues = Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(el => el.value);
        console.log(`Valeurs sélectionnées pour ${name}:`, selectedValues);
        return selectedValues;
    }
});
