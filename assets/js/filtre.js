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
        var filters = getFilterFromURL();
        console.log("Filters:", filters);
        var visibleCount = 0;
        $grid.isotope({
            filter: function() {
                var $this = $(this);
                var isMatched = true;
                for (var key in filters) {
                    if (!filters[key].length) continue;
                    var attr = $this.attr('data-' + key.slice(0, -1)); // Enlever le 's' final
                    isMatched = isMatched && (filters[key].indexOf(attr) != -1);
                }
                if (isMatched) visibleCount++;
                return isMatched;
            }
        });
        console.log("Visible items after filtering:", visibleCount);
    }

    // Initialiser les checkboxes à partir de l'URL
    var initialFilters = getFilterFromURL();
    for (var key in initialFilters) {
        initialFilters[key].forEach(function(value) {
            var $checkbox = $('input[name="' + key + '[]"][value="' + value + '"]');
            $checkbox.prop('checked', true);
            console.log("Checkbox initialized:", key, value, "Found:", $checkbox.length);
        });
    }

    // Gérer les changements de checkbox
    $('input[type="checkbox"]').on('change', function() {
        var $checkbox = $(this);
        var filterGroup = $checkbox.attr('name').replace('[]', '');
        var filters = getFilterFromURL();
        filters[filterGroup] = $('input[name="' + filterGroup + '[]"]:checked').map(function() {
            return this.value;
        }).get();
        console.log("Checkbox changed:", filterGroup, "New values:", filters[filterGroup]);
        updateURL(filters);
        applyFilters();
    });

    // Appliquer les filtres initiaux
    console.log("Applying initial filters");
    applyFilters();

    console.log("Filter initialization complete");
});
