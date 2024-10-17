document.addEventListener('DOMContentLoaded', function() {
  const headerContainer = document.getElementById('header-container');
  const searchToggle = document.getElementById('search-toggle');
  const searchBar = document.getElementById('search-bar');
  let isSearchBarOpen = false;
  let lastScrollTop = 0;

  // Fonction pour gérer l'affichage/masquage du header
  function handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop && scrollTop > 200) {
      // Scroll vers le bas
      headerContainer.style.transform = 'translateY(-100%)';
    } else {
      // Scroll vers le haut ou en haut de la page
      headerContainer.style.transform = 'translateY(0)';
    }
    lastScrollTop = scrollTop;
  }

  // Écouteur d'événement pour le scroll
  window.addEventListener('scroll', handleScroll);

  // Gestion de la barre de recherche
  searchToggle.addEventListener('click', function(e) {
    e.preventDefault();
    isSearchBarOpen = !isSearchBarOpen;
    if (isSearchBarOpen) {
      searchBar.classList.add('open');
      setTimeout(() => {
        searchBar.querySelector('input').focus();
      }, 300); // Attendre la fin de l'animation avant de focus
    } else {
      searchBar.classList.remove('open');
    }
  });
});
