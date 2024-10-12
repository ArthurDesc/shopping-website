function setupCategorySearch() {
    console.log("Début de setupCategorySearch()");
    const searchInput = document.getElementById('input-group-search');
    const categoriesList = document.getElementById('categories-list');
    
    console.log("searchInput:", searchInput);
    console.log("categoriesList:", categoriesList);
  
    if (searchInput && categoriesList) {
      searchInput.addEventListener('input', function() {
        console.log("Recherche en cours:", this.value);
        const searchTerm = this.value.toLowerCase();
        const categoryItems = categoriesList.querySelectorAll('li');
    
        categoryItems.forEach(item => {
          const categoryName = item.textContent.toLowerCase();
          if (categoryName.includes(searchTerm)) {
            item.style.display = '';
          } else {
            item.style.display = 'none';
          }
        });
      });
      console.log("Écouteur d'événements ajouté pour la recherche");
    } else {
      console.log("searchInput ou categoriesList non trouvé");
    }
    console.log("Fin de setupCategorySearch()");
  }

function loadCategories() {
  // ... (code existant)
}

function loadParentCategories() {
  // ... (implémentez cette fonction si elle n'existe pas encore)
}

function setupDropdown() {
  // ... (code existant)
}

export { setupCategorySearch, loadCategories, loadParentCategories, setupDropdown };

