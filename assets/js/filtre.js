document.addEventListener('DOMContentLoaded', function() {
    console.log("Le DOM est chargé, le script s'exécute.");

    const filterForm = document.getElementById('filterForm');
    const products = document.querySelectorAll('.products_list > div > div');
    const filterCategories = document.querySelectorAll('.filter-category');

    filterCategories.forEach(category => {
        const button = category.querySelector('button');
        const content = category.querySelector('div[x-show]');

        button.addEventListener('click', function(event) {
            console.log("Bouton cliqué :", this.textContent.trim());
            event.preventDefault();
            const arrow = this.querySelector('svg');

            if (content) {
                console.log("Contenu trouvé, basculement de la visibilité");
                content.classList.toggle('hidden');
                content.setAttribute('x-show', content.classList.contains('hidden') ? 'false' : 'true');
            } else {
                console.log("Aucun contenu trouvé pour ce bouton");
            }

            if (arrow) {
                console.log("Flèche trouvée, basculement de la rotation");
                arrow.classList.toggle('rotate-180');
            } else {
                console.log("Aucune flèche trouvée pour ce bouton");
            }
        });

        // Ajoutez des écouteurs d'événements pour les cases à cocher
        const checkboxes = category.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    });

    console.log("Nombre de catégories de filtre trouvées :", filterCategories.length);

    function getSelectedValues(name) {
        return Array.from(document.querySelectorAll(`input[name="${name}"]:checked`)).map(el => el.value);
    }

    function applyFilters() {
        const selectedFilters = {
            categories: getSelectedValues('category'),
            collections: getSelectedValues('collection'),
            brands: getSelectedValues('brand')
        };

        filterProducts(selectedFilters);
    }

    function filterProducts(filters) {
        products.forEach(product => {
            const categories = product.dataset.categories.split(',');
            const collection = product.dataset.collection.toLowerCase();
            const brand = product.dataset.brand.toLowerCase();

            const categoryMatch = filters.categories.length === 0 || filters.categories.some(cat => categories.includes(cat));
            const collectionMatch = filters.collections.length === 0 || filters.collections.some(col => collection.includes(col.toLowerCase()));
            const brandMatch = filters.brands.length === 0 || filters.brands.some(b => brand === b.toLowerCase());

            if (categoryMatch && collectionMatch && brandMatch) {
                product.style.display = '';
            } else {
                product.style.display = 'none';
            }
        });
    }
});
