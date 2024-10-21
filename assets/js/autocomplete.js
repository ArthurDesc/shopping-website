document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');

    if (!searchInput || !autocompleteResults) {
        console.error('Éléments de recherche manquants');
        return;
    }

    searchInput.addEventListener('input', debounce(function() {
        const query = this.value.trim();
        if (query.length >= 2) {
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
            showAutocompleteResults();
        } else {
            hideAutocompleteResults();
        }
    }

    function showAutocompleteResults() {
        autocompleteResults.classList.remove('hidden');
    }

    function hideAutocompleteResults() {
        autocompleteResults.classList.add('hidden');
    }

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Fermer les résultats d'autocomplétion lors d'un clic en dehors
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
            hideAutocompleteResults();
        }
    });
});
