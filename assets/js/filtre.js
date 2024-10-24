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

    // Fonction pour lire les paramètres de l'URL
    function getURLParameters() {
        const params = new URLSearchParams(window.location.search);
        return {
            collection: params.get('collection') ? [params.get('collection')] : [],
            category: params.get('category') ? [params.get('category')] : [],
            marque: params.get('marque') ? [params.get('marque')] : []
        };
    }

    // Fonction pour cocher les cases en fonction des paramètres de l'URL
    function setCheckboxesFromURL() {
        const params = getURLParameters();
        Object.entries(params).forEach(([filterType, values]) => {
            values.forEach(value => {
                const checkbox = $(`input[name="${filterType}s[]"][value="${value}"]`);
                if (checkbox.length) {
                    checkbox.prop('checked', true);
                    addFilterTag(filterType + 's', value);
                }
            });
        });
    }

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

        $('.product-card').each(function() {
            const $product = $(this);
            const productName = $product.find('h3').text().toLowerCase();
            const productCollection = $product.data('collection');
            const productCategories = $product.data('categories') ? $product.data('categories').toString().split(',') : [];
            const productMarque = $product.data('brand');

            const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(productCollection);
            const categoryMatch = selectedCategories.length === 0 || productCategories.some(cat => selectedCategories.includes(cat));
            const marqueMatch = selectedMarques.length === 0 || selectedMarques.includes(productMarque);
            const searchMatch = productName.includes(searchTerm);

            const shouldShow = collectionMatch && categoryMatch && marqueMatch && searchMatch;
            $product.toggle(shouldShow);
        });

        console.log(`Nombre de produits visibles après filtrage : ${$('.product-card:visible').length}`);
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

    function addFilterTag(type, value) {
        const tagId = `${type}-${value}`.replace(/\s+/g, '-').toLowerCase();
        if ($(`#${tagId}`).length === 0) {
            const tag = $('<span>')
                .addClass('filter-tag')
                .attr('id', tagId)
                .text(value)
                .append(
                    $('<button>')
                        .addClass('remove-tag')
                        .html('&times;')
                        .on('click', function() {
                            $(`input[name="${type}[]"][value="${value}"]`).prop('checked', false).trigger('change');
                        })
                );
            $('#selected-filters').append(tag);
        }
    }

    function removeFilterTag(type, value) {
        const tagId = `${type}-${value}`.replace(/\s+/g, '-').toLowerCase();
        $(`#${tagId}`).remove();
    }

    // Cocher les cases en fonction des paramètres de l'URL au chargement de la page
    setCheckboxesFromURL();

    // Appliquer les filtres initiaux
    applyFilters();

    // Écouter les changements sur tous les filtres
    $('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]').on('change', function() {
        console.log(`Filtre modifié : ${this.name} - ${this.value} - Coché : ${this.checked}`);
        const filterType = this.name.replace('[]', '');
        if (this.checked) {
            addFilterTag(filterType, this.value);
        } else {
            removeFilterTag(filterType, this.value);
        }
        applyFilters();
        updateURL();
    });

    // Écouter les changements dans la barre de recherche
    $('#products-search').on('input', applyFilters);

    function initializeFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        const urlCollection = urlParams.get('collection');
        if (urlCollection) {
            $('input[name="collections[]"]').each(function() {
                if (this.value.toLowerCase() === urlCollection.toLowerCase()) {
                    $(this).prop('checked', true);
                }
            });
        }
    }

    initializeFiltersFromURL();
    applyFilters(); // Assurez-vous d'appliquer les filtres après avoir coché les cases
});

// Assurez-vous que cette fonction est appelée pour tous les éléments de filtre
function attachFilterListeners() {
    const filterInputs = document.querySelectorAll('input[type="checkbox"][name^="categories"], input[type="checkbox"][name^="marques"], input[type="checkbox"][name^="collections"]');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            console.log(`Filtre modifié : ${this.name} - ${this.value} - Coché : ${this.checked}`);
            applyFilters();
            updateURL();
        });
    });
}

function applyFilters() {
    console.log("Fonction applyFilters appelée");

    const selectedCollections = getSelectedValues('collections');
    const selectedCategories = getSelectedValues('categories');
    const selectedMarques = getSelectedValues('marques');

    console.log("Filtres sélectionnés:", {
        collections: selectedCollections,
        categories: selectedCategories,
        marques: selectedMarques
    });

    $('.product-card').each(function() {
        const $product = $(this);
        const productCollection = $product.data('collection');
        const productCategories = $product.data('categories') ? $product.data('categories').toString().split(',') : [];
        const productMarque = $product.data('brand');

        const collectionMatch = selectedCollections.length === 0 || selectedCollections.includes(productCollection);
        const categoryMatch = selectedCategories.length === 0 || productCategories.some(cat => selectedCategories.includes(cat));
        const marqueMatch = selectedMarques.length === 0 || selectedMarques.includes(productMarque);

        const shouldShow = collectionMatch && categoryMatch && marqueMatch;
        $product.toggle(shouldShow);
    });

    console.log(`Nombre de produits visibles après filtrage : ${$('.product-card:visible').length}`);
}

// Appelez cette fonction au chargement de la page
$(document).ready(function() {
    attachFilterListeners();
    applyFilters(); // Appliquer les filtres initiaux
});
