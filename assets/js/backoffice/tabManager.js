// Début du fichier tabManager.js

// Fonction switchTab existante
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
            .then((response) => response.text())
            .then((text) => {
              console.log("Réponse brute du serveur:", text); // Pour le débogage
              try {
                return JSON.parse(text);
              } catch (e) {
                console.error("Erreur de parsing JSON:", e);
                throw new Error("La réponse du serveur n'est pas un JSON valide: " + text);
              }
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
              console.error("Erreur complète:", error);
              showToast(
                "Une erreur s'est produite lors de l'ajout de l'article: " + error.message,
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

// Nouvelle fonction switchCategoryTab
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
        <div id="category-management" class="p-4">

          <!-- Liste des catégories -->
          <div id="categories-list" class="space-y-4">
            <!-- Les catégories seront ajoutées ici dynamiquement -->
          </div>


        <!-- Template pour une catégorie (sera cloné par JavaScript) -->
        <template id="category-template">
          <div class="category bg-white p-4 rounded-lg shadow">
            <div class="flex justify-between items-center">
              <span class="category-name font-semibold"></span>
              <div class="space-x-2">
                <button class="edit-category text-blue-500 hover:text-blue-700">Modifier</button>
                <button class="delete-category text-red-500 hover:text-red-700">Supprimer</button>
                <button class="toggle-subcategories text-gray-500 hover:text-gray-700">▼</button>
              </div>
            </div>
            <div class="subcategories mt-2 ml-4 hidden">
              <!-- Les sous-catégories seront ajoutées ici dynamiquement -->
            </div>
          </div>
        </template>
      `;
      loadCategoriesList();
      break;
    case "ajouter":
      tabContent.innerHTML = `
        <div x-data="{ openTab: null }" class="w-full max-w-lg px-10 py-8 mx-auto bg-white rounded-lg shadow-xl">
          <div class="space-y-4">
            <!-- Nouvelle catégorie principale -->
            <div class="border-b">
              <div @click="openTab = openTab === 'main' ? null : 'main'" class="flex items-center justify-between cursor-pointer py-4">
                <span class="font-semibold text-gray-600">Nouvelle catégorie principale</span>
                <svg :class="{'rotate-180': openTab === 'main'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
              <div x-show="openTab === 'main'" x-collapse>
                <div class="py-4">
                  <input type="text" id="newMainCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Nouvelle catégorie principale">
                  <button onclick="addNewMainCategory()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Ajouter
                  </button>
                </div>
              </div>
            </div>

            <!-- Nouvelle sous-catégorie -->
            <div class="border-b">
              <div @click="openTab = openTab === 'sub' ? null : 'sub'" class="flex items-center justify-between cursor-pointer py-4">
                <span class="font-semibold text-gray-600">Nouvelle sous-catégorie</span>
                <svg :class="{'rotate-180': openTab === 'sub'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
              <div x-show="openTab === 'sub'" x-collapse>
                <div class="py-4">
                  <select id="mainCategorySelect" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2">
                    <option value="" disabled selected>Choisissez une catégorie principale</option>
                  </select>
                  <input type="text" id="newSubCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Nouvelle sous-catégorie">
                  <button onclick="addNewSubCategory()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Ajouter
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Charger les catégories principales dans le select
      loadMainCategories().then(mainCategories => {
        const select = document.getElementById('mainCategorySelect');
        mainCategories.forEach(category => {
          const option = document.createElement('option');
          option.value = category.id_categorie;
          option.textContent = category.nom;
          select.appendChild(option);
        });
      });
      
      break;
    default:
      tabContent.innerHTML = "<p>Contenu non disponible</p>";
  }
}

// Fonction setupAddCategoryForm
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

function initAccordion() {
  console.log("Début de initAccordion");
  if (typeof window.Accordion === 'undefined') {
    console.error("La bibliothèque Flowbite n'est pas chargée correctement");
    return;
  }

  const accordionElement = document.getElementById('accordion-color');
  if (accordionElement) {
    console.log("Élément d'accordéon trouvé, initialisation...");
    const accordion = new window.Accordion(accordionElement, {
      onToggle: (item) => {
        console.log('Toggled accordion item:', item);
      }
    });
    console.log("Accordéon initialisé");
  } else {
    console.error("L'élément accordion-color n'a pas été trouvé");
  }
}

// Fonction loadCategoriesList
function loadCategoriesList() {
    console.log("Début de loadCategoriesList");
    const categoriesList = document.getElementById('categories-list');
    
    if (!categoriesList) {
        console.error("L'élément 'categories-list' n'a pas été trouvé");
        return;
    }

    fetch('/shopping-website/admin/get_categories.php')
        .then(response => response.json())
        .then(categories => {
            console.log("Catégories reçues:", categories);
            categoriesList.innerHTML = ''; // Vider la liste existante

            categories.forEach(category => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                        <input id="checkbox-item-${category.id_categorie}" type="checkbox" value="${category.id_categorie}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                        <label for="checkbox-item-${category.id_categorie}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">${category.nom}</label>
                    </div>
                `;
                categoriesList.appendChild(li);
            });

            console.log("Fin du remplissage des catégories");
        })
        .catch(error => {
            console.error("Erreur lors du chargement des catégories:", error);
        });
}

function updateCategoryName(categoryId, newName) {
  fetch('/shopping-website/admin/update_category.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id_categorie: categoryId, nom: newName })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast("Nom de la catégorie mis à jour avec succès", "success");
      loadCategoriesList();
    } else {
      showToast("Erreur lors de la mise à jour du nom de la catégorie : " + (data.message || "Erreur inconnue"), "error");
    }
  })
  .catch(error => {
    console.error("Erreur:", error);
    showToast("Une erreur s'est produite lors de la mise à jour du nom de la catégorie", "error");
  });
}

function createCategoryAccordionItem(category, index, allCategories) {
  console.log("Création de l'élément d'accordéon pour la catégorie:", category.nom);
  const item = document.createElement('div');
  
  const subCategories = allCategories.filter(cat => cat.parent_id === category.id_categorie);
  const subCategoryCount = subCategories.length;
  const hasSubCategories = subCategoryCount > 0;
  
  item.setAttribute('x-data', `{ 
    open${index}: false, 
    editing: false, 
    categoryName: '${category.nom}',
    hasSubCategories: ${hasSubCategories},
    subCategoryCount: ${subCategoryCount},
    editName() {
      this.editing = true;
      this.$nextTick(() => this.$refs.nameInput.focus());
    },
    saveName() {
      if (this.categoryName.trim() !== '') {
        updateCategoryName(${category.id_categorie}, this.categoryName);
        this.editing = false;
      }
    }
  }`);
  
  item.innerHTML = `
    <div class='flex items-center justify-between text-gray-600 w-full border-b overflow-hidden mt-5 mb-5 mx-auto'>
      <div class='flex items-center'>
        <div @click="open${index} = !open${index}" class='w-10 border-r px-2 transform transition duration-300 ease-in-out cursor-pointer flex items-center justify-center' :class="{'rotate-90': open${index},' -translate-y-0.0': !open${index} }">
          <svg x-show="hasSubCategories" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
          </svg>          
        </div>
        <div class='flex items-center px-2 py-3'>
          <div class='mx-3' x-show="!editing" @dblclick="editName()">
            <span class="hover:underline" x-text="categoryName"></span>
            <span class="text-sm text-gray-500 ml-2">(<span x-text="subCategoryCount"></span>)</span>
          </div>
          <div x-show="editing" class="mx-3">
            <input 
              x-ref="nameInput"
              x-model="categoryName" 
              @blur="saveName()" 
              @keyup.enter="saveName()"
              class="border rounded px-2 py-1 text-sm"
            >
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col p-5 md:p-0 w-full transform transition duration-300 ease-in-out border-b pb-10"
      x-cloak x-show="open${index}" x-collapse x-collapse.duration.500ms>
      ${subCategories.map(subCat => `
        <div class="mb-2 flex justify-between items-center" x-data="{ editing: false, subCategoryName: '${subCat.nom}' }">
          <span x-show="!editing" x-text="subCategoryName"></span>
          <input 
            x-show="editing" 
            x-model="subCategoryName" 
            @blur="updateSubCategoryName(${subCat.id_categorie}, subCategoryName); editing = false"
            @keyup.enter="updateSubCategoryName(${subCat.id_categorie}, subCategoryName); editing = false"
            class="border rounded px-2 py-1 text-sm"
          >
          <div>
            <button @click="editing = true" class="text-blue-500 hover:text-blue-700 ml-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
              </svg>
            </button>
            <button onclick="deleteCategory(${subCat.id_categorie})" class="text-red-500 hover:text-red-700 ml-2">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
              </svg>
            </button>
          </div>
        </div>
      `).join('')}
    </div>
  `;
  
  console.log("Élément d'accordéon créé pour la catégorie:", category.nom);
  return item;
}


// Fonction editCategory
function editCategory(categoryId) {
  // Implémentez la logique pour éditer une catégorie
  console.log("Édition de la catégorie:", categoryId);
  // Vous pouvez ouvrir un modal ou rediriger vers une page d'édition
}

// Fonction deleteCategory
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

function updateSubCategoryName(categoryId, newName) {
  fetch('/shopping-website/admin/update_category.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id_categorie: categoryId, nom: newName })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showToast("Nom de la sous-catégorie mis à jour avec succès", "success");
      loadCategoriesList(); // Recharger la liste des catégories
    } else {
      showToast("Erreur lors de la mise à jour du nom de la sous-catégorie : " + data.message, "error");
    }
  })
  .catch(error => {
    console.error("Erreur:", error);
    showToast("Une erreur s'est produite lors de la mise à jour du nom de la sous-catégorie", "error");
  });
}

// N'oubliez pas d'exposer cette fonction globalement
window.updateSubCategoryName = updateSubCategoryName;

// Ajoutez cette nouvelle fonction après la fonction loadCategoriesList
function loadMainCategories() {
  return fetch('/shopping-website/admin/get_categories.php')
    .then(response => response.json())
    .then(categories => {
      const mainCategories = categories.filter(cat => cat.parent_id === null);
      const select = document.getElementById('mainCategorySelect');
      select.innerHTML = '<option value="" disabled selected>Choisissez une catégorie principale</option>';
      mainCategories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.id_categorie;
        option.textContent = category.nom;
        select.appendChild(option);
      });
      return mainCategories;
    });
}

// Ajoutez ces nouvelles fonctions à la fin du fichier
function addNewMainCategory() {
  const newCategoryName = document.getElementById('newMainCategory').value.trim();
  console.log("Tentative d'ajout de la catégorie principale:", newCategoryName);
  if (newCategoryName) {
    CategoryManager.addNewCategory(newCategoryName)
      .then(response => {
        console.log("Réponse reçue de addNewCategory:", response);
        if (response.success) {
          showToast("Nouvelle catégorie principale ajoutée avec succès", "success");
          document.getElementById('newMainCategory').value = '';
          loadMainCategories();
          loadCategoriesList();
        } else {
          showToast("Erreur lors de l'ajout de la catégorie : " + (response.message || "Erreur inconnue"), "error");
        }
      })
      .catch(error => {
        console.error("Erreur complète dans addNewMainCategory:", error);
        showToast("Une erreur s'est produite lors de l'ajout de la catégorie. Vérifiez la console pour plus de détails.", "error");
      });
  } else {
    console.log("Tentative d'ajout d'une catégorie avec un nom vide");
    showToast("Veuillez entrer un nom pour la nouvelle catégorie", "error");
  }
}

function addNewSubCategory() {
  const mainCategoryId = document.getElementById('mainCategorySelect').value;
  const newSubCategoryName = document.getElementById('newSubCategory').value;
  if (mainCategoryId && newSubCategoryName) {
    CategoryManager.addNewSubCategory(mainCategoryId, newSubCategoryName)
      .then(response => {
        if (response.success) {
          showToast("Nouvelle sous-catégorie ajoutée avec succès", "success");
          loadCategoriesList(); // Recharger la liste des catégories
          document.getElementById('newSubCategory').value = ''; // Vider le champ
        } else {
          showToast("Erreur lors de l'ajout de la sous-catégorie : " + response.message, "error");
        }
      })
      .catch(error => {
        console.error("Erreur:", error);
        showToast("Une erreur s'est produite lors de l'ajout de la sous-catégorie", "error");
      });
  } else {
    showToast("Veuillez sélectionner une catégorie principale et entrer un nom pour la sous-catégorie", "error");
  }
}

// N'oubliez pas d'exposer ces nouvelles fonctions globalement
window.addNewMainCategory = addNewMainCategory;
window.addNewSubCategory = addNewSubCategory;

// Exposer les fonctions nécessaires globalement
window.switchTab = switchTab;
window.switchCategoryTab = switchCategoryTab;
window.editCategory = editCategory;
window.deleteCategory = deleteCategory;
window.deleteArticle = ArticleManager.deleteArticle;
window.editArticle = ArticleManager.editArticle;
window.addNewCategory = CategoryManager.addNewCategory;

function loadCategories() {
  console.log("Début de loadCategories()");
  const categoriesContainer = document.getElementById('categories-container');
  const categoriesList = document.getElementById('categories-list');

  if (!categoriesContainer) {
    console.error("L'élément 'categories-container' n'a pas été trouvé");
    return;
  }

  if (!categoriesList) {
    console.error("L'élément 'categories-list' n'a pas été trouvé");
    return;
  }

  console.log("Éléments trouvés, tentative de mise à jour du contenu");

  // Le reste de votre code pour charger les catégories...

  fetch('/shopping-website/admin/backofficeV2.php', {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(categories => {
    console.log("Catégories reçues:", categories);
    categoriesList.innerHTML = ''; // Vider la liste existante

    categories.forEach(category => {
      // Votre code pour ajouter chaque catégorie...
    });
  })
  .catch(error => {
    console.error("Erreur lors du chargement des catégories:", error);
  });
}

// Fin du fichier tabManager.js