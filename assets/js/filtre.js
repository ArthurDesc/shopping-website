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
