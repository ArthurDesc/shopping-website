class CategorySelector {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Container with id '${containerId}' not found`);
            return;
        }
    }

    init() {
        this.render();
        this.setupEventListeners();
        this.loadCategories();
    }

    render() {
        this.container.innerHTML = `
            <div class="flex gap-2 items-start">
                <div class="relative w-full sm:w-64 md:w-72 lg:w-96">
                    <button id="dropdownSearchButton" 
                            data-dropdown-toggle="dropdownSearch" 
                            class="w-full inline-flex items-center justify-between px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300" 
                            type="button">
                        Sélectionner les catégories 
                        <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </button>

                    <div id="dropdownSearch" 
                         class="z-10 hidden bg-white rounded-lg shadow w-full mt-2 absolute">
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
                        </ul>
                    </div>
                </div>
                <button id="save-categories" 
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300">
                    Valider
                </button>
            </div>
        `;
    }

    setupEventListeners() {
        const searchInput = document.getElementById('input-group-search');
        const dropdownButton = document.getElementById('dropdownSearchButton');
        const dropdownMenu = document.getElementById('dropdownSearch');

        // Setup search functionality
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e));
        }

        // Setup dropdown toggle
        if (dropdownButton && dropdownMenu) {
            dropdownButton.addEventListener('click', (e) => {
                e.preventDefault();
                dropdownMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!dropdownMenu.contains(e.target) && e.target !== dropdownButton) {
                    dropdownMenu.classList.add('hidden');
                }
            });
        }

        // Ajouter l'écouteur pour le bouton de sauvegarde
        const saveButton = document.getElementById('save-categories');
        if (saveButton) {
            saveButton.addEventListener('click', () => this.saveCategories());
        }
    }

    handleSearch(event) {
        const searchTerm = event.target.value.toLowerCase();
        const categoryItems = document.querySelectorAll('#categories-list li');

        categoryItems.forEach(item => {
            const categoryName = item.textContent.toLowerCase();
            item.style.display = categoryName.includes(searchTerm) ? '' : 'none';
        });
    }

    loadCategories() {
        fetch('/shopping-website/admin/backofficeV2.php', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(categories => {
            const categoriesList = document.getElementById('categories-list');
            categoriesList.innerHTML = '';

            categories.forEach(category => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                        <input id="category-${category.id_categorie}" type="checkbox" name="categories[]" value="${category.id_categorie}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="category-${category.id_categorie}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded">${category.nom}</label>
                    </div>
                `;
                categoriesList.appendChild(li);
            });
        })
        .catch(error => {
            console.error("Erreur lors du chargement des catégories:", error);
        });
    }

    getSelectedCategories() {
        const selectedCategories = [];
        document.querySelectorAll('#categories-list input[type="checkbox"]:checked').forEach(checkbox => {
            selectedCategories.push(checkbox.value);
        });
        return selectedCategories;
    }

    setSelectedCategories(categoryIds) {
        if (!Array.isArray(categoryIds)) return;
        
        // Attendre que les catégories soient chargées
        const checkInterval = setInterval(() => {
            const allCheckboxesExist = categoryIds.every(id => 
                document.querySelector(`#category-${id}`)
            );
            
            if (allCheckboxesExist) {
                clearInterval(checkInterval);
                categoryIds.forEach(id => {
                    const checkbox = document.querySelector(`#category-${id}`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        }, 100);

        // Arrêter l'intervalle après 5 secondes si les checkboxes ne sont pas trouvées
        setTimeout(() => clearInterval(checkInterval), 5000);
    }

    saveCategories() {
        const selectedCategories = this.getSelectedCategories();
        const productId = document.getElementById('id_produit').value;

        fetch('/shopping-website/admin/update_article.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id_produit: productId,
                field: 'categories',
                new_value: selectedCategories
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Catégories mises à jour avec succès', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Erreur lors de la mise à jour des catégories', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de la mise à jour des catégories', 'error');
        });
    }
}

// Exposer la classe globalement
window.CategorySelector = CategorySelector;
