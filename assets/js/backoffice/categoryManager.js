const CategoryManager = (function(UIManager) {
    function loadCategories() {
        console.log("Début de loadCategories()");
        const categoriesContainer = document.getElementById('categories-container');
  
        if (!categoriesContainer) {
            console.log("categoriesContainer non trouvé");
            return;
        }
  
        categoriesContainer.innerHTML = `
            <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" class="w-full inline-flex items-center justify-between px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300" type="button">
                Sélectionner les catégories 
                <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>

            <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-full md:w-60 mt-2 absolute">
                <div class="p-3">
                    <label for="input-group-search" class="sr-only">Rechercher</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                        <input type="text" id="input-group-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Rechercher une catégorie">
                    </div>
                </div>
                <ul id="categories-list" class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700" aria-labelledby="dropdownSearchButton">
                    <!-- Les catégories seront ajoutées ici dynamiquement -->
                </ul>
                <div class="p-3 border-t border-gray-200">
                    <form id="addCategoryForm" class="flex items-center">
                        <input type="text" id="newCategoryName" name="newCategoryName" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Nouvelle catégorie" required>
                        <button type="submit" class="ml-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Ajouter</button>
                    </form>
                </div>
            </div>
        `;
  
        const categoriesList = document.getElementById('categories-list');
  
        fetch('/shopping-website/admin/backofficeV2.php', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(categories => {
            console.log("Catégories reçues:", categories);
  
            categories.forEach(category => {
                console.log("Ajout de la catégorie:", category);
                const li = document.createElement('li');
                li.innerHTML = `
                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                        <input id="category-${category.id_categorie}" type="checkbox" name="categories[]" value="${category.id_categorie}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="category-${category.id_categorie}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">${category.nom}</label>
                    </div>
                `;
                categoriesList.appendChild(li);
            });
  
            console.log("Fin du remplissage des catégories");
            UIManager.setupCategorySearch();
            UIManager.setupDropdown();
        })
        .catch(error => {
            console.error("Erreur lors du chargement des catégories:", error);
        });
  
        console.log("Fin de loadCategories()");

        // Appeler la fonction pour configurer le formulaire d'ajout de catégorie
        setupAddCategoryForm();
    }

    function addNewCategory(categoryName) {
        fetch('/shopping-website/admin/add_category.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `categoryName=${encodeURIComponent(categoryName)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Catégorie ajoutée avec succès', 'success');
                loadCategories(); // Recharger la liste des catégories
            } else {
                showToast('Erreur lors de l\'ajout de la catégorie: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur s\'est produite lors de l\'ajout de la catégorie', 'error');
        });
    }

    return {
        loadCategories: loadCategories,
        addNewCategory: addNewCategory
    };
})(UIManager);  // Passez UIManager comme dépendance ici
