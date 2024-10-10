// categoryManager.js

// Fonction pour charger les catégories parentes
async function loadParentCategories() {
    // Dans un environnement de production, vous feriez probablement un appel API ici
    // Pour l'instant, nous utilisons des données simulées
    const parentCategories = await getParentCategories();
    
    updateDesktopCategories(parentCategories);
    updateMobileCategories(parentCategories);
}

// Fonction pour obtenir les catégories parentes (simulation d'un appel API)
function getParentCategories() {
    return new Promise((resolve) => {
        setTimeout(() => {
            resolve([
                { id: 1, name: 'Électronique' },
                { id: 2, name: 'Vêtements' },
                { id: 3, name: 'Livres' },
                { id: 4, name: 'Maison' },
                { id: 5, name: 'Sports' }
            ]);
        }, 100); // Simule un délai réseau
    });
}

// Fonction pour mettre à jour les catégories sur desktop
function updateDesktopCategories(categories) {
    const parentCategoriesContainer = document.getElementById('parent-categorie-desktop');
    if (parentCategoriesContainer) {
        parentCategoriesContainer.innerHTML = categories.map(category => `
            <div class="flex items-center">
                <input type="checkbox" id="parent-${category.id}" name="parent-categories[]" value="${category.id}" class="mr-2">
                <label for="parent-${category.id}" class="text-white">${category.name}</label>
            </div>
        `).join('');
    }
}

// Fonction pour mettre à jour les catégories sur mobile
function updateMobileCategories(categories) {
    const parentCategorySelect = document.getElementById('parent-categorie-mobile');
    if (parentCategorySelect) {
        parentCategorySelect.innerHTML = '<option value="" disabled selected>Grande catégorie</option>';
        categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            parentCategorySelect.appendChild(option);
        });
    }
}

// Fonction pour gérer l'ajout d'une nouvelle catégorie
function addNewCategory(categoryName) {
    // Implémentez la logique pour ajouter une nouvelle catégorie
    console.log(`Ajout de la nouvelle catégorie : ${categoryName}`);
    // Ici, vous feriez probablement un appel API pour ajouter la catégorie à la base de données
}

// Fonction pour gérer l'ajout d'une nouvelle sous-catégorie
function addNewSubcategory(parentCategoryId, subcategoryName) {
    // Implémentez la logique pour ajouter une nouvelle sous-catégorie
    console.log(`Ajout de la nouvelle sous-catégorie "${subcategoryName}" à la catégorie parent ${parentCategoryId}`);
    // Ici, vous feriez probablement un appel API pour ajouter la sous-catégorie à la base de données
}


function addCategory(categoryData) {
    return API.addCategory(categoryData);
}

function updateCategory(categoryId, categoryData) {
    return API.updateCategory(categoryId, categoryData);
}

function deleteCategory(categoryId) {
    return API.deleteCategory(categoryId);
}

// Exportez les fonctions qui doivent être accessibles depuis d'autres fichiers
export { loadParentCategories, addNewCategory, addNewSubcategory };