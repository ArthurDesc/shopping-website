document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search-input');
  const autocompleteResults = document.getElementById('autocomplete-results');
  const searchForm = document.querySelector('form[action*="recherche.php"]');

  // Vérification de l'existence des éléments
  if (!searchInput || !autocompleteResults || !searchForm) {
    console.error('Éléments de recherche manquants:', { searchInput, autocompleteResults, searchForm });
    return; // Arrêter l'exécution si les éléments n'existent pas
  }

  searchInput.addEventListener('input', debounce(function() {
    const query = this.value.trim();
    if (query.length > 0) { // Modifié pour déclencher dès le premier caractère
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
        div.textContent = result;
        div.classList.add('p-2', 'hover:bg-gray-100', 'cursor-pointer');
        div.addEventListener('click', function() {
          searchInput.value = result;
          hideAutocompleteResults();
          searchForm.submit();
        });
        autocompleteResults.appendChild(div);
      });
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
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Gestion du menu latéral
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');

  menuToggle.addEventListener('click', function() {
    sidebar.classList.toggle('-translate-x-full');
  });

  // Gestion des sous-menus
  const subMenuToggles = document.querySelectorAll('[id$="-toggle"]');
  subMenuToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      const subMenuId = this.id.replace('-toggle', '');
      const subMenu = document.getElementById(subMenuId);
      subMenu.classList.toggle('hidden');
    });
  });

  console.log('BASE_URL:', BASE_URL);
});
