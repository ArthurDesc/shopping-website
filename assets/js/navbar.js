document.addEventListener('DOMContentLoaded', function() {
  const headerContainer = document.getElementById('header-container');
  const searchToggle = document.getElementById('search-toggle');
  const searchBar = document.getElementById('search-bar');
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  let isSearchBarOpen = false;
  let lastScrollTop = 0;

  // Fonction pour gérer l'affichage/masquage du header
  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 200) {
      headerContainer.style.transform = 'translateY(-100%)';
    } else {
      headerContainer.style.transform = 'translateY(0)';
    }
    lastScrollTop = scrollTop;
  }

  // Écouteur d'événement pour le scroll
  window.addEventListener('scroll', handleScroll);

  // Gestion de la barre de recherche
  if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', function(e) {
      e.preventDefault();
      isSearchBarOpen = !isSearchBarOpen;
      if (isSearchBarOpen) {
        searchBar.style.height = '60px'; // Ajustez cette valeur selon vos besoins
      } else {
        searchBar.style.height = '0';
      }
    });
  }

  // Gestion du menu burger
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('-translate-x-full');
    });
  }

  // Gestion des sous-menus
  const subMenuToggles = document.querySelectorAll('[id$="-toggle"]');
  subMenuToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      const subMenuId = this.id.replace('-toggle', '');
      const subMenu = document.getElementById(subMenuId);
      if (subMenu) {
        subMenu.classList.toggle('hidden');
      }
    });
  });
});
