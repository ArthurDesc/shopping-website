function switchTab(clickedTab, tabId) {
  document.querySelectorAll('a[onclick^="switchTab"]').forEach((tab) => {
    tab.classList.remove(
      "text-blue-600",
      "bg-gray-100",
      "border-blue-600",
      "active"
    );
    tab.classList.add(
      "text-gray-500",
      "hover:text-gray-600",
      "hover:bg-gray-50",
      "border-transparent"
    );
  });
  clickedTab.classList.remove(
    "text-gray-500",
    "hover:text-gray-600",
    "hover:bg-gray-50",
    "border-transparent"
  );
  clickedTab.classList.add(
    "text-blue-600",
    "bg-gray-100",
    "border-blue-600",
    "active"
  );

  const tabContent = document.getElementById("tab-content");
  switch (tabId) {
    case "modifier":
      ArticleManager.loadArticles();
      break;
    case "ajouter":
      tabContent.innerHTML = `
                <form id="addArticleForm" class="gradient-blue p-4 sm:p-6 rounded-lg shadow-md w-full mx-auto max-w-4xl" enctype="multipart/form-data">
                    <div class="mb-4 sm:mb-5">
                        <label for="titre" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Titre</label>
                        <input type="text" id="titre" name="titre" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez le titre">
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="description" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez la description"></textarea>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="prix" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Prix</label>
                        <div class="relative">
                            <input type="number" id="prix" name="prix" class="w-full px-3 py-1 rounded text-sm sm:text-base pr-8" placeholder="Entrez le prix">
                            <span class="absolute right-3 top-1 text-gray-500 text-sm sm:text-base">€</span>
                        </div>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="stock" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Stock</label>
                        <input type="number" id="stock" name="stock" 
                            class="w-full px-3 py-1 rounded text-sm sm:text-base" 
                            placeholder="Entrez la quantité en stock"
                            min="0" max="1000" step="1">
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="image" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Image du produit</label>
                        <div class="flex items-center">
                          <label for="image" class="cursor-pointer bg-white text-blue-500 px-4 py-2 rounded-l text-sm sm:text-base hover:bg-blue-100 font-semibold">
                            Choisir un fichier
                          </label>
                          <input type="file" id="image" name="image" accept="image/*" class="hidden">
                          <span id="file-chosen" class="flex-grow bg-white text-gray-500 px-3 py-2 rounded-r text-sm sm:text-base">Aucun fichier choisi</span>
                        </div>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="marque" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Marque</label>
                        <input type="text" id="marque" name="marque" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez la marque">
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="collection" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Collection</label>
                        <div class="relative">
                          <select id="collection" name="collection" class="w-full px-3 py-1 rounded text-sm sm:text-base appearance-none bg-white text-gray-700">
                            <option value="" disabled selected>Sélectionnez une collection</option>
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                            <option value="Enfant">Enfant</option>
                          </select>
                          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                              <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                            </svg>
                          </div>
                        </div>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="categories" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Catégories</label>
                        <div id="categories-container" class="bg-white rounded-lg p-2 max-h-40 overflow-y-auto">
                          <!-- Les catégories seront ajoutées ici dynamiquement -->
                        </div>
                    </div>
                    <div class="flex justify-center mt-6">
                        <button type="submit" class="bg-white text-blue-500 px-6 py-2 rounded text-sm sm:text-base hover:bg-blue-100 font-semibold">Valider</button>
                    </div>
                </form>
            `;

      // Ajoutez ce code après la définition du HTML
      const fileInput = document.getElementById('image');
      const fileChosen = document.getElementById('file-chosen');

      fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
          fileChosen.textContent = this.files[0].name;
        } else {
          fileChosen.textContent = 'Aucun fichier choisi';
        }
      });

      document
        .getElementById("addArticleForm")
        .addEventListener("submit", function (e) {
          e.preventDefault();
          console.log("Formulaire soumis");

          const formData = new FormData(this);
          formData.append("action", "add_article");

          // Récupérer le nom de l'image
          const imageFile = document.getElementById("image").files[0];
          if (imageFile) {
            formData.append("image_name", imageFile.name);
          }

          const errors = validateForm(formData);
          if (errors.length > 0) {
            showToast(errors.join("<br>"), "error");
            return;
          }

          console.log("FormData créé:", Object.fromEntries(formData));

          fetch("/shopping-website/admin/process_article.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => {
              if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
              }
              return response.json();
            })
            .then((data) => {
              console.log("Données reçues:", data);
              if (data.success) {
                showToast("Article ajouté avec succès !", "success");
                this.reset();
              } else {
                showToast(
                  "Erreur lors de l'ajout de l'article : " + data.message,
                  "error"
                );
              }
            })
            .catch((error) => {
              console.error("Erreur:", error);
              showToast(
                "Une erreur s'est produite lors de l'ajout de l'article.",
                "error"
              );
            });
        });
        console.log("Avant loadCategories()");
        CategoryManager.loadCategories();
        console.log("Après loadCategories()");
      break;
    default:
      tabContent.innerHTML = "<p>Contenu non disponible</p>";
  }
}
function switchCategoryTab(clickedTab, tabId) {
  document
    .querySelectorAll('a[onclick^="switchCategoryTab"]')
    .forEach((tab) => {
      tab.classList.remove(
        "text-blue-600",
        "bg-gray-100",
        "border-blue-600",
        "active"
      );
      tab.classList.add(
        "text-gray-500",
        "hover:text-gray-600",
        "hover:bg-gray-50",
        "border-transparent"
      );
    });
  clickedTab.classList.remove(
    "text-gray-500",
    "hover:text-gray-600",
    "hover:bg-gray-50",
    "border-transparent"
  );
  clickedTab.classList.add(
    "text-blue-600",
    "bg-gray-100",
    "border-blue-600",
    "active"
  );

  const tabContent = document.getElementById("category-tab-content");
  switch (tabId) {
    case "modifier":
      tabContent.innerHTML = `
        <div class="mb-4">
          <div class="relative">
            <input type="text" id="search-categories" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rechercher une catégorie...">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </div>
        <div id="categories-list" class="space-y-2">
          <!-- La liste des catégories sera chargée ici dynamiquement -->
        </div>
      `;
      loadCategoriesList();
      break;
    case "ajouter":
      tabContent.innerHTML = `
                <div class="max-w-screen-xl mx-auto px-5 bg-white min-h-screen">

        <div class="grid divide-y divide-neutral-200 max-w-xl mx-auto mt-8">
        <div class="py-5">
        <details class="group">
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none">
        <span class="text-gray-900 transition duration-500 group-open:text-[#007AFF]">Nouvelle catégorie</span>
        <span class="transition-transform duration-300">
        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24">
        <path d="M6 9l6 6 6-6"></path>
        </svg>
        </span>
        </summary>
        <div class="group-open:animate-fadeIn mt-3 text-neutral-600">
        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Nouvelle catégorie">
        <button class="bg-[#007AFF] text-white px-4 py-2 rounded-md hover:bg-[#007AFF] transition duration-300">Valider</button>
        </div>
        </details>
        </div>
        <div class="py-5">
        <details class="group">
        <summary class="flex justify-between items-center font-medium cursor-pointer list-none">
        <span class="text-gray-900 transition duration-500 group-open:text-[#007AFF]">Nouvelle sous catégorie</span>
        <span class="transition-transform duration-300">
        <svg fill="none" height="24" shape-rendering="geometricPrecision" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" width="24">
        <path d="M6 9l6 6 6-6"></path>
        </svg>
        </span>
        </summary>
        <div class="group-open:animate-fadeIn mt-3 text-neutral-600">
        <select class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
            <option value="" disabled selected>Grande catégorie</option>
            <!-- Les options seront ajoutées dynamiquement -->
        </select>
        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Nouvelle sous catégorie">
        <button class="bg-[#007AFF] text-white px-4 py-2 rounded-md hover:bg-[#007AFF] transition duration-300">Valider</button>
        </div>
        </details>
        </div>
        </div>
        </div>
            `;
      break;
    default:
      tabContent.innerHTML = "<p>Contenu non disponible</p>";
  }
}





function setupAddCategoryForm() {
    const addCategoryForm = document.getElementById('addCategoryForm');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const newCategoryName = document.getElementById('newCategoryName').value;
            if (newCategoryName) {
                addNewCategory(newCategoryName);
            }
        });
    } else {
        console.log("Formulaire d'ajout de catégorie non trouvé");
    }
}









// Assurez-vous que cette fonction est accessible globalement
window.deleteArticle = ArticleManager.deleteArticle;

function loadCategoriesList() {
  const categoriesList = document.getElementById('categories-list');
  
  fetch('/shopping-website/admin/get_categories.php')
    .then(response => response.json())
    .then(categories => {
      categoriesList.innerHTML = '';
      categories.forEach(category => {
        const categoryItem = document.createElement('div');
        categoryItem.className = 'flex items-center justify-between p-2 bg-white rounded-lg shadow';
        categoryItem.innerHTML = `
          <span>${category.nom}</span>
          <div>
            <button onclick="editCategory(${category.id_categorie})" class="text-blue-500 hover:text-blue-700 mr-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
              </svg>
            </button>
            <button onclick="deleteCategory(${category.id_categorie})" class="text-red-500 hover:text-red-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </button>
          </div>
        `;
        categoriesList.appendChild(categoryItem);
      });
    })
    .catch(error => {
      console.error("Erreur lors du chargement des catégories:", error);
      showToast("Erreur lors du chargement des catégories", "error");
    });
}

function editCategory(categoryId) {
  // Implémentez la logique pour éditer une catégorie
  console.log("Édition de la catégorie:", categoryId);
  // Vous pouvez ouvrir un modal ou rediriger vers une page d'édition
}

function deleteCategory(categoryId) {
  if (confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?")) {
    fetch('/shopping-website/admin/delete_category.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ id_categorie: categoryId })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showToast("Catégorie supprimée avec succès", "success");
        loadCategoriesList(); // Recharger la liste des catégories
      } else {
        showToast("Erreur lors de la suppression de la catégorie : " + data.message, "error");
      }
    })
    .catch(error => {
      console.error("Erreur:", error);
      showToast("Une erreur s'est produite lors de la suppression de la catégorie", "error");
    });
  }
}

// Assurez-vous que ces fonctions sont accessibles globalement
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;

// Exposez les fonctions nécessaires globalement
window.deleteArticle = ArticleManager.deleteArticle;
window.editArticle = ArticleManager.editArticle;
window.addNewCategory = CategoryManager.addNewCategory;