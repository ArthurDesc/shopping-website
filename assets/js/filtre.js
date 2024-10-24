document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM chargé, initialisation des filtres");

    const filterInputs = document.querySelectorAll('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]');
    const products = document.querySelectorAll('.products_list > div > div');

    console.log(`Nombre de filtres trouvés : ${filterInputs.length}`);
    console.log(`Nombre de produits trouvés : ${products.length}`);

    // Fonction pour appliquer les filtres
    function applyFilters() {
        console.log("Fonction applyFilters appelée");

        const selectedCollections = getSelectedValues('collections');
        const selectedCategories = getSelectedValues('categories');
        const selectedMarques = getSelectedValues('marques');
        const searchTerm = $('#products-search').val().toLowerCase();

        console.log("Filtres sélectionnés:", {
            collections: selectedCollections,
            categories: selectedCategories,
            marques: selectedMarques,
            searchTerm: searchTerm
        });

        let visibleProducts = 0;

        $('.product-card').each(function() {
            const $product = $(this);
            const productName = $product.find('h3').text().toLowerCase();
            const productCollection = $product.data('collection');
            const productCategories = $product.data('categories') ? $product.data('categories').toString().split(',') : [];
            const productMarque = $product.data('brand');

            console.log("Données du produit:", {
                name: productName,
                collection: productCollection,
                categories: productCategories,
                marque: productMarque
            });

            const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(productCollection);
            const categoryMatch = selectedCategories.length === 0 || productCategories.some(cat => selectedCategories.includes(cat));
            const marqueMatch = selectedMarques.length === 0 || selectedMarques.includes(productMarque);
            const searchMatch = productName.includes(searchTerm);

            const shouldShow = collectionMatch && categoryMatch && marqueMatch && searchMatch;
            console.log(`Le produit doit-il être affiché ? ${shouldShow}`);

            if (shouldShow) {
                $product.show();
                visibleProducts++;
            } else {
                $product.hide();
            }
        });

        console.log(`Nombre de produits visibles après filtrage : ${visibleProducts}`);
    }

    // Fonction pour obtenir les valeurs sélectionnées
    function getSelectedValues(name) {
        return $(`input[name="${name}[]"]:checked`).map(function() {
            return this.value;
        }).get();
    }

    // Ajouter un écouteur d'événements à chaque case à cocher
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            console.log(`Filtre modifié : ${this.name} - ${this.value} - Coché : ${this.checked}`);
            applyFilters();
            updateURL();
        });
    });

    // Fonction pour mettre à jour l'URL
    function updateURL() {
        const params = new URLSearchParams();
        ['collections', 'categories', 'marques'].forEach(filterType => {
            const values = getSelectedValues(filterType);
            if (values.length > 0) {
                params.set(filterType, values.join(','));
            }
        });

        const newURL = `${window.location.pathname}?${params.toString()}`;
        console.log(`Nouvelle URL : ${newURL}`);
        history.pushState(null, '', newURL);
    }

    // Appliquer les filtres initiaux
    console.log("Application des filtres initiaux");
    applyFilters();
});

// Garder la fonction globale si nécessaire
window.applyFilters = function() {
    console.log("Fonction applyFilters appelée");

    const selectedCollections = getSelectedValues('collections');
    const selectedCategories = getSelectedValues('categories');
    const selectedMarques = getSelectedValues('marques');
    const searchTerm = $('#products-search').val().toLowerCase();

    console.log("Filtres sélectionnés:", {
        collections: selectedCollections,
        categories: selectedCategories,
        marques: selectedMarques,
        searchTerm: searchTerm
    });

    products.forEach(product => {
        const categories = product.dataset.categories ? product.dataset.categories.split(',') : [];
        const marque = product.dataset.brand || '';
        const collection = product.dataset.collection || '';

        const categoryMatch = selectedCategories.length === 0 || 
            selectedCategories.some(cat => categories.includes(cat));
        const marqueMatch = selectedMarques.length === 0 || selectedMarques.includes(marque);
        const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(collection);

        if (categoryMatch && marqueMatch && collectionMatch) {
            product.style.display = '';
        } else {
            product.style.display = 'none';
        }
    });
}

$(document).ready(function() {
    console.log("jQuery est prêt");
    console.log("Nombre de produits trouvés :", $('.product-card').length);
    console.log("Nombre de filtres trouvés :", $('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]').length);

    function applyFilters() {
        console.log("Fonction applyFilters appelée");

        const selectedCollections = getSelectedValues('collections');
        const selectedCategories = getSelectedValues('categories');
        const selectedMarques = getSelectedValues('marques');
        const searchTerm = $('#products-search').val().toLowerCase();

        console.log("Filtres sélectionnés:", {
            collections: selectedCollections,
            categories: selectedCategories,
            marques: selectedMarques,
            searchTerm: searchTerm
        });

        let visibleProducts = 0;

        $('.product-card').each(function() {
            const $product = $(this);
            const productName = $product.find('h3').text().toLowerCase();
            const productCollection = $product.data('collection');
            const productCategories = $product.data('categories') ? $product.data('categories').toString().split(',') : [];
            const productMarque = $product.data('brand');

            console.log("Données du produit:", {
                name: productName,
                collection: productCollection,
                categories: productCategories,
                marque: productMarque
            });

            const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(productCollection);
            const categoryMatch = selectedCategories.length === 0 || productCategories.some(cat => selectedCategories.includes(cat));
            const marqueMatch = selectedMarques.length === 0 || selectedMarques.includes(productMarque);
            const searchMatch = productName.includes(searchTerm);

            const shouldShow = collectionMatch && categoryMatch && marqueMatch && searchMatch;
            console.log(`Le produit doit-il être affiché ? ${shouldShow}`);

            $product.toggle(shouldShow);

            if (shouldShow) {
                visibleProducts++;
            }
        });

        console.log(`Nombre de produits visibles après filtrage : ${visibleProducts}`);
    }

    function getSelectedValues(name) {
        return $(`input[name="${name}[]"]:checked`).map(function() {
            return this.value;
        }).get();
    }

    function updateURL() {
        const params = new URLSearchParams();
        ['collections', 'categories', 'marques'].forEach(filterType => {
            const values = getSelectedValues(filterType);
            if (values.length > 0) {
                params.set(filterType, values.join(','));
            }
        });

        const newURL = `${window.location.pathname}?${params.toString()}`;
        console.log(`Nouvelle URL : ${newURL}`);
        history.pushState(null, '', newURL);
    }

    // Appliquer les filtres initiaux
    applyFilters();

    // Écouter les changements sur tous les filtres
    $('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]').on('change', function() {
        console.log(`Filtre modifié : ${this.name} - ${this.value} - Coché : ${this.checked}`);
        applyFilters();
        updateURL();
    });

    // Écouter les changements dans la barre de recherche
    $('#products-search').on('input', applyFilters);
});
