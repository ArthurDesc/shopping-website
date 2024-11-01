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

    function loadContent(section) {
        // Sélectionner tous les liens
        const allLinks = document.querySelectorAll('[id$="-link"], [id$="-link-desktop"]');
        allLinks.forEach(link => link.classList.remove('active-tab'));

        // Ajouter la classe active aux liens correspondants
        document.querySelector(`#${section}-link}`)?.classList.add('active-tab');
        document.querySelector(`#${section}-link-desktop}`)?.classList.add('active-tab');

        // Récupérer le contenu actuel
        const contentArea = document.getElementById('content-area');
        const currentContent = contentArea.firstElementChild;

        // Animer la sortie du contenu actuel
        if (currentContent) {
            currentContent.classList.add('leaving');
            currentContent.classList.remove('active');
        }

        // Charger le nouveau contenu
        fetch(`${BASE_URL}admin/content/${section}.php`)
            .then(response => response.text())
            .then(html => {
                // Créer un conteneur pour le nouveau contenu
                const newContent = document.createElement('div');
                newContent.className = 'tab-content';
                newContent.innerHTML = html;

                // Si il y a un contenu actuel, le supprimer après l'animation
                if (currentContent) {
                    setTimeout(() => {
                        currentContent.remove();
                        // Ajouter et animer le nouveau contenu
                        contentArea.appendChild(newContent);
                        // Force un reflow
                        newContent.offsetHeight;
                        newContent.classList.add('active');
                    }, 300);
                } else {
                    // Ajouter directement le nouveau contenu s'il n'y en a pas
                    contentArea.appendChild(newContent);
                    // Force un reflow
                    newContent.offsetHeight;
                    newContent.classList.add('active');
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement du contenu:', error);
                showToast('Erreur lors du chargement du contenu', 'error');
            });
    }

    // Ajouter cette fonction pour initialiser le contenu par défaut
    document.addEventListener('DOMContentLoaded', () => {
        // Charger le contenu des articles par défaut
        loadContent('articles');
    });

    return {
        setupCategorySearch: setupCategorySearch,
        setupDropdown: setupDropdown,
        loadContent: loadContent
    };
})();

window.UIManager = _UIManager;

// Ajoutez ces lignes pour déboguer
console.log("UIManager initialisé :", window.UIManager);
console.log("setupCategorySearch disponible :", typeof window.UIManager.setupCategorySearch === 'function');
console.log("setupDropdown disponible :", typeof window.UIManager.setupDropdown === 'function');
console.log("loadContent disponible :", typeof window.UIManager.loadContent === 'function');

/*
window.UIManager = {
    setupDropdown: function() {
        // Le code pour configurer le dropdown
    },
    // Autres méthodes...
};
*/
