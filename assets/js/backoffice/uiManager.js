let _UIManager = (function() {
    function setupCategorySearch() {
        console.log("Début de setupCategorySearch()");
        const searchInput = document.getElementById('input-group-search');
        const categoriesList = document.getElementById('categories-list');
        
        if (searchInput && categoriesList) {
            searchInput.addEventListener('input', function() {
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
        }
    }

    function setupDropdown() {
        const dropdownButton = document.getElementById('dropdownSearchButton');
        const dropdownMenu = document.getElementById('dropdownSearch');
        
        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', function() {
                dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', function(event) {
                if (!dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }
    }

    async function loadContent(section) {
        // Sélectionner tous les liens
        const allLinks = document.querySelectorAll('[id$="-link"], [id$="-link-desktop"]');
        allLinks.forEach(link => link.classList.remove('active-tab'));

        // Ajouter la classe active aux liens correspondants
        const mobileLink = document.getElementById(`${section}-link`);
        const desktopLink = document.getElementById(`${section}-link-desktop`);
        if (mobileLink) mobileLink.classList.add('active-tab');
        if (desktopLink) desktopLink.classList.add('active-tab');

        // Récupérer le contenu actuel
        const contentArea = document.getElementById('content-area');
        
        try {
            // Charger le nouveau contenu
            const response = await fetch(`${BASE_URL}admin/content/${section}.php`);
            if (!response.ok) throw new Error('Erreur lors du chargement du contenu');
            const content = await response.text();
            
            // Animer la transition
            contentArea.style.opacity = '0';
            setTimeout(() => {
                contentArea.innerHTML = content;
                contentArea.style.opacity = '1';
                
                // Réinitialiser les fonctionnalités après le chargement
                if (section === 'categories') {
                    setupCategorySearch();
                    setupDropdown();
                }
            }, 300);
        } catch (error) {
            console.error('Erreur:', error);
            contentArea.innerHTML = '<div class="text-red-500">Erreur lors du chargement du contenu</div>';
        }
    }

    // Charger le contenu des articles par défaut au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        loadContent('articles');
    });

    return {
        setupCategorySearch,
        setupDropdown,
        loadContent
    };
})();

// Exposer la fonction loadContent globalement
window.loadContent = _UIManager.loadContent;
