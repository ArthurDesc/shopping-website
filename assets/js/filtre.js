$(document).ready(function() {
    console.log("Document ready, initializing filters");

    // Initialiser Isotope
    var $grid = $('.products_list').isotope({
        itemSelector: '.product-card',
        layoutMode: 'fitRows'
    });
    console.log("Isotope initialized with", $('.product-card').length, "items");

    // Fonction pour obtenir les filtres de l'URL
    function getFilterFromURL() {
        var params = new URLSearchParams(window.location.search);
        var filters = {};
        ['collections', 'categories', 'marques'].forEach(function(param) {
            var value = params.get(param);
            if (value) {
                filters[param] = value.split(',');
            }
        });
        console.log("Filters from URL:", filters);
        return filters;
    }

    // Fonction pour mettre à jour l'URL
    function updateURL(filters) {
        var params = new URLSearchParams();
        for (var key in filters) {
            if (filters[key].length) {
                params.set(key, filters[key].join(','));
            }
        }
        var newURL = '?' + params.toString();
        console.log("Updating URL to:", newURL);
        history.pushState(null, '', newURL);
    }

    // Appliquer les filtres
    function applyFilters() {
        console.log("Applying filters");
        var filters = {
            categories: getSelectedValues('categories'),
            marques: getSelectedValues('marques'),
            collections: getSelectedValues('collections')
        };
        console.log("Filters:", filters);

        $grid.isotope({
            filter: function() {
                var $this = $(this);
                var isMatched = true;
                
                for (var key in filters) {
                    if (filters[key].length > 0) {
                        var productValue = $this.data(key.slice(0, -1)); // Enlever le 's' final
                        if (Array.isArray(productValue)) {
                            isMatched = isMatched && filters[key].some(value => productValue.includes(value));
                        } else {
                            isMatched = isMatched && filters[key].includes(productValue);
                        }
                    }
                }
                
                return isMatched;
            }
        });

        console.log("Filters applied, visible items:", $grid.data('isotope').filteredItems.length);
    }

    function getSelectedValues(name) {
        return $(`input[name="${name}[]"]:checked`).map(function() {
            return $(this).val();
        }).get();
    }

    // Initialiser les checkboxes à partir de l'URL
    function initializeCheckboxes() {
        var initialFilters = getFilterFromURL();
        for (var key in initialFilters) {
            initialFilters[key].forEach(function(value) {
                var $checkbox = $('input[name="' + key + '[]"][value="' + value + '"]');
                $checkbox.prop('checked', true);
                console.log("Checkbox initialized:", key, value, "Found:", $checkbox.length);
            });
        }
    }

    // Gérer les changements de checkbox
    $('input[type="checkbox"]').on('change', function() {
        applyFilters();
        updateURL(getFilterFromURL());
    });

    // Initialisation
    initializeCheckboxes();
    applyFilters();

    console.log("Filter initialization complete");

    // Fonction pour synchroniser les filtres avec le serveur (à implémenter si nécessaire)
    function syncFiltersWithServer() {
        var filters = getFilterFromURL();
        // Envoyer une requête AJAX au serveur avec les filtres actuels
        // et mettre à jour l'affichage en conséquence
    }
});
