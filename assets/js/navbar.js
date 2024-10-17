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

  // Fonction pour fermer la barre de recherche
  function closeSearchBar() {
    isSearchBarOpen = false;
    searchBar.style.height = '0';
    searchBar.classList.remove('shadow-md', 'open');
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
      isSearchBarOpen = !isSearchBarOpen;
      if (isSearchBarOpen) {
        searchBar.style.height = '60px';
        searchBar.classList.add('shadow-md', 'open');
        closeSidebar();
      } else {
        closeSearchBar();
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
});
