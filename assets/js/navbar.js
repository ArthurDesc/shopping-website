document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menu-toggle');
  const sidebar = document.getElementById('sidebar');
  const closeFilters = document.getElementById('closeFilters');
  const searchToggle = document.getElementById('search-toggle');
  const searchBar = document.getElementById('search-bar');

  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function() {
      sidebar.classList.toggle('-translate-x-full');
    });
  }

  if (closeFilters && sidebar) {
    closeFilters.addEventListener('click', function() {
      sidebar.classList.add('-translate-x-full');
    });
  }

  if (searchToggle && searchBar) {
    searchToggle.addEventListener('click', function(e) {
      e.preventDefault();
      searchBar.classList.toggle('open');
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
