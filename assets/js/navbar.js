document.addEventListener('DOMContentLoaded', function() {
  const headerContainer = document.getElementById('header-container');
  const searchToggle = document.getElementById('search-toggle');
  const searchBar = document.getElementById('search-bar');
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  const closeSidebarButton = document.getElementById('close-sidebar');
  let isSearchBarOpen = false;
  let isSidebarOpen = false;
  let lastScrollTop = 0;

  // Ajoutez ces nouvelles variables
  const searchInput = document.getElementById('search-input');
  const autocompleteResults = document.getElementById('autocomplete-results');

  // Fonction pour gérer l'affichage/masquage du header et de la barre de recherche
  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 200 && !isSidebarOpen) {
      headerContainer.style.transform = 'translateY(-100%)';
      if (isSearchBarOpen) {
        searchBar.style.transform = 'translateY(-100%)';
      }
    } else {
      headerContainer.style.transform = 'translateY(0)';
      if (isSearchBarOpen) {
        searchBar.style.transform = 'translateY(0)';
      }
    }
    lastScrollTop = scrollTop;
  }

  // Écouteur d'événement pour le scroll
  window.addEventListener('scroll', handleScroll);

  // Fonction pour fermer la barre de recherche
  function closeSearchBar() {
    isSearchBarOpen = false;
    searchBar.style.height = '0';
    searchBar.style.transform = 'translateY(-100%)';
    searchBar.classList.remove('shadow-md', 'open');
    hideAutocompleteResults(); // Cacher les résultats d'autocomplétion
  }

  // Fonction pour ouvrir la barre de recherche
  function openSearchBar() {
    isSearchBarOpen = true;
    searchBar.style.height = '60px'; // Hauteur initiale
    searchBar.style.transform = 'translateY(0)';
    searchBar.classList.add('shadow-md', 'open');
    hideAutocompleteResults(); // Réinitialiser l'autocomplétion
  }

  // Fonction pour ouvrir la sidebar
  function openSidebar() {
    isSidebarOpen = true;
    sidebar.classList.add('open');
    document.body.style.overflow = 'hidden';
    headerContainer.style.transform = 'translateY(0)'; // Assurez-vous que le header est visible
  }

  // Fonction pour fermer la sidebar
  function closeSidebar() {
    isSidebarOpen = false;
    sidebar.classList.remove('open');
    document.body.style.overflow = '';
    handleScroll(); // Réappliquez la logique de défilement après la fermeture
  }

  // Gestion de la barre de recherche
  if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (isSearchBarOpen) {
        closeSearchBar();
      } else {
        openSearchBar();
        closeSidebar();
      }
    });
  }

  // Gestion du menu burger
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      openSidebar();
      closeSearchBar();
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

  // Fermer le sidebar et la barre de recherche en cliquant à l'extérieur
  document.addEventListener('click', function(e) {
    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
      closeSidebar();
    }
    if (!searchBar.contains(e.target) && !searchToggle.contains(e.target)) {
      closeSearchBar();
    }
  });

  // Empêcher la propagation des clics à l'intérieur du sidebar et de la barre de recherche
  sidebar.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  searchBar.addEventListener('click', function(e) {
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
      autocompleteResults.classList.remove('hidden');
      
      // Calculer la nouvelle hauteur
      const resultHeight = 60; // Hauteur de chaque résultat
      const maxResults = 5; // Nombre maximum de résultats à afficher
      const totalResultsHeight = Math.min(results.length, maxResults) * resultHeight;
      const newHeight = 60 + totalResultsHeight; // 60px pour l'input + hauteur des résultats
      
      // Animer l'ouverture de la barre de recherche
      searchBar.style.height = `${newHeight}px`;
    } else {
      hideAutocompleteResults();
    }
  }

  // Fonction pour cacher les résultats d'autocomplétion
  function hideAutocompleteResults() {
    autocompleteResults.classList.add('hidden');
    if (isSearchBarOpen) {
      searchBar.style.height = '60px'; // Remettre la hauteur initiale
    }
  }

  // Ajoutez un écouteur d'événements pour l'input de recherche
  if (searchInput) {
    searchInput.addEventListener('input', debounce(handleAutocomplete, 300));
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
});
