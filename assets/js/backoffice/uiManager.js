let _UIManager = (function() {
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

    function setupDropdown() {
        const dropdownButton = document.getElementById('dropdownSearchButton');
        const dropdownMenu = document.getElementById('dropdownSearch');
        
        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });

            // Fermer le dropdown si on clique en dehors
            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        } else {
            console.warn("Éléments du dropdown non trouvés");
        }
    }

    return {
        setupCategorySearch: setupCategorySearch,
        setupDropdown: setupDropdown
    };
})();

window.UIManager = _UIManager;

// Ajoutez ces lignes pour déboguer
console.log("UIManager initialisé :", window.UIManager);
console.log("setupCategorySearch disponible :", typeof window.UIManager.setupCategorySearch === 'function');
console.log("setupDropdown disponible :", typeof window.UIManager.setupDropdown === 'function');

/*
window.UIManager = {
    setupDropdown: function() {
        // Le code pour configurer le dropdown
    },
    // Autres méthodes...
};
*/
