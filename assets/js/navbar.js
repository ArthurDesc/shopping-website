document.addEventListener('DOMContentLoaded', function() {
  const headerContainer = document.getElementById('header-container');
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  const closeSidebarButton = document.getElementById('close-sidebar');
  const searchInput = document.getElementById('search');
  const autocompleteResults = document.getElementById('autocomplete-results');
  let isSidebarOpen = false;
  let lastScrollTop = 0;

  // Fonction pour gérer l'affichage/masquage du header
  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 200 && !isSidebarOpen) {
      headerContainer.style.transform = 'translateY(-100%)';
    } else {
      headerContainer.style.transform = 'translateY(0)';
    }
    lastScrollTop = scrollTop;
  }

  // Écouteur d'événement pour le scroll
  window.addEventListener('scroll', handleScroll);

  // Fonction pour ouvrir la sidebar
  function openSidebar() {
    isSidebarOpen = true;
    sidebar.classList.add('open');
    document.body.style.overflow = 'hidden';
    headerContainer.style.transform = 'translateY(0)';
  }

  // Fonction pour fermer la sidebar
  function closeSidebar() {
    isSidebarOpen = false;
    sidebar.classList.remove('open');
    document.body.style.overflow = '';
    handleScroll();
  }

  // Gestion du menu burger
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openSidebar();
    });
  }

  // Gestion des sous-menus
  const subMenuToggles = document.querySelectorAll('[id$="-toggle"]');
  subMenuToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const subMenuId = this.id.replace('-toggle', '');
      const subMenu = document.getElementById(subMenuId);
      if (subMenu) {
        subMenu.classList.toggle('hidden');
      }
    });
  });

  // Fermer le sidebar en cliquant à l'extérieur
  document.addEventListener('click', function(e) {
    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      closeSidebar();
    }
  });

  // Empêcher la propagation des clics à l'intérieur du sidebar
  sidebar.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  // Gestion du bouton de fermeture de la sidebar
  if (closeSidebarButton) {
    closeSidebarButton.addEventListener('click', function(e) {
      e.preventDefault();
      closeSidebar();
    });
  }

  // Fonction pour l'autocomplétion
  function handleAutocomplete() {
    const query = searchInput.value.trim();
    if (query.length > 0) {
      fetch(`${BASE_URL}includes/autocomplete.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          displayAutocompleteResults(data);
        })
        .catch(error => {
          console.error('Erreur lors de la récupération des résultats:', error);
          hideAutocompleteResults();
        });
    } else {
      hideAutocompleteResults();
    }
  }

  // Fonction pour afficher les résultats d'autocomplétion
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
      showAutocompleteResults();
    } else {
      hideAutocompleteResults();
    }
  }

  // Fonction pour afficher les résultats d'autocomplétion
  function showAutocompleteResults() {
    const inputRect = searchInput.getBoundingClientRect();
    autocompleteResults.style.top = `${inputRect.bottom}px`;
    autocompleteResults.style.left = `${inputRect.left}px`;
    autocompleteResults.style.width = `${inputRect.width}px`;
    autocompleteResults.classList.remove('hidden');
  }

  // Fonction pour cacher les résultats d'autocomplétion
  function hideAutocompleteResults() {
    autocompleteResults.classList.add('hidden');
  }

  // Ajoutez un écouteur d'événements pour l'input de recherche
  if (searchInput) {
    searchInput.addEventListener('input', debounce(handleAutocomplete, 300));
    searchInput.addEventListener('focus', handleAutocomplete);
  }

  // Fonction debounce pour limiter les appels à l'API
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

  // Fermer les résultats d'autocomplétion en cliquant en dehors
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
      hideAutocompleteResults();
    }
  });
});
