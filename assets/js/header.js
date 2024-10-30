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
  const closeButton = document.getElementById('close-sidebar');
  const sidebar = document.getElementById('sidebar');
  const body = document.body;

  // Ouvrir le menu
  menuToggle?.addEventListener('click', function() {
    sidebar.classList.add('open');
    body.classList.add('sidebar-open');
  });

  // Fermer le menu
  closeButton?.addEventListener('click', function() {
    sidebar.classList.remove('open');
    body.classList.remove('sidebar-open');
  });

  // Fermer le menu en cliquant sur l'overlay
  document.addEventListener('click', function(e) {
    if (body.classList.contains('sidebar-open') && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      sidebar.classList.remove('open');
      body.classList.remove('sidebar-open');
    }
  });

  // Gestion des dropdowns
  const dropdownContainers = document.querySelectorAll('#sidebar li');
  let activeDropdown = null;

  dropdownContainers.forEach(container => {
    const toggle = container.querySelector('[id$="-toggle"]');
    const content = container.querySelector('ul');
    
    if (!toggle || !content) return;

    // Initialiser tous les dropdowns comme fermés
    content.style.display = 'none';
    
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      // Si ce dropdown est déjà actif, le fermer
      if (activeDropdown === content) {
        content.style.display = 'none';
        toggle.querySelector('svg').style.transform = '';
        activeDropdown = null;
        return;
      }
      
      // Fermer le dropdown actif précédent
      if (activeDropdown) {
        activeDropdown.style.display = 'none';
        activeDropdown.previousElementSibling.querySelector('svg').style.transform = '';
      }
      
      // Ouvrir le nouveau dropdown
      content.style.display = 'block';
      toggle.querySelector('svg').style.transform = 'rotate(180deg)';
      activeDropdown = content;
    });
  });
});
