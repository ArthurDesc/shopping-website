document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');

    if (!searchInput || !autocompleteResults) {
        console.error('Éléments de recherche manquants');
        return;
    }

    searchInput.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        if (query.length > 0) {
            fetchAutocompleteResults(query);
        } else {
            hideAutocompleteResults();
        }
    }, 300));

    function fetchAutocompleteResults(query) {
        fetch(`${BASE_URL}includes/autocomplete.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displayAutocompleteResults(data);
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des résultats:', error);
                hideAutocompleteResults();
            });
    }

    function displayAutocompleteResults(results) {
        autocompleteResults.innerHTML = '';
        if (results.length > 0) {
            results.forEach(result => {
                const div = document.createElement('div');
                div.textContent = result.name;
                div.classList.add('p-2', 'hover:bg-gray-100', 'cursor-pointer');
                div.addEventListener('click', () => {
                    searchInput.value = result.name;
                    hideAutocompleteResults();
                });
                autocompleteResults.appendChild(div);
            });
            autocompleteResults.classList.remove('hidden');
        } else {
            hideAutocompleteResults();
        }
    }

    function hideAutocompleteResults() {
        autocompleteResults.classList.add('hidden');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});
