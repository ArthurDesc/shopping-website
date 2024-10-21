document.addEventListener('DOMContentLoaded', function() {
    console.log('autocomplete.js chargé');
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');

    if (!searchInput || !autocompleteResults) {
        console.error('Éléments de recherche manquants:', { searchInput, autocompleteResults });
        return;
    }

    searchInput.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        console.log('Requête de recherche:', query);
        if (query.length >= 2) {
            fetchAutocompleteResults(query);
        } else {
            hideAutocompleteResults();
        }
    }, 300));

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
            hideAutocompleteResults();
        }
    });

    function fetchAutocompleteResults(query) {
        console.log('Fetching results for:', query);
        fetch(`${BASE_URL}includes/autocomplete.php?q=${encodeURIComponent(query)}`)
            .then(response => {
                console.log('Réponse reçue:', response);
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data);
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
            const ul = document.createElement('ul');
            ul.className = 'py-2';
            results.forEach(result => {
                const li = document.createElement('li');
                li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                li.textContent = result.nom;
                li.addEventListener('click', () => {
                    searchInput.value = result.nom;
                    hideAutocompleteResults();
                    // Optionnel : rediriger vers la page du produit
                    // window.location.href = `${BASE_URL}pages/detail.php?id=${result.id_produit}`;
                });
                ul.appendChild(li);
            });
            autocompleteResults.appendChild(ul);
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
