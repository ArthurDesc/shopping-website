// assets/js/autocomplete.js
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');

    if (!searchInput || !autocompleteResults) {
        console.error('Éléments de recherche manquants:', { searchInput, autocompleteResults });
        return; // Arrêter l'exécution si les éléments n'existent pas
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length < 2) {
            autocompleteResults.innerHTML = ''; // Réinitialiser les résultats
            autocompleteResults.classList.add('hidden'); // Cacher les résultats
            return;
        }

        fetch(`${BASE_URL}includes/autocomplete.php?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                autocompleteResults.innerHTML = ''; // Réinitialiser les résultats
                autocompleteResults.classList.remove('hidden'); // Afficher les résultats

                if (data.length > 0) {
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item.nom; // Assurez-vous que 'nom' est la clé correcte
                        div.classList.add('p-2', 'hover:bg-gray-100', 'cursor-pointer');

                        div.addEventListener('click', function() {
                            searchInput.value = item.nom; // Remplir le champ de recherche
                            autocompleteResults.classList.add('hidden'); // Cacher les résultats
                            // Optionnel : soumettre le formulaire de recherche
                            document.querySelector('form').submit();
                        });

                        autocompleteResults.appendChild(div); // Ajouter le résultat à la liste
                    });
                } else {
                    autocompleteResults.classList.add('hidden'); // Cacher si aucune suggestion
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des résultats:', error);
                autocompleteResults.classList.add('hidden'); // Cacher les résultats en cas d'erreur
            });
    });

    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !autocompleteResults.contains(event.target)) {
            autocompleteResults.classList.add('hidden'); // Cacher les résultats
        }
    });
});
