const UIManager = (function() {
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

        dropdownButton.addEventListener('click', function(event) {
            event.preventDefault();
            dropdownMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(event) {
            if (!dropdownMenu.contains(event.target) && event.target !== dropdownButton) {
                dropdownMenu.classList.add('hidden');
            }
        });
      }

      return {
        setupCategorySearch: setupCategorySearch,
        setupDropdown: setupDropdown
      };
})();
