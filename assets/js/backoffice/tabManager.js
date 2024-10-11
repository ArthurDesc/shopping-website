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
      loadArticles();
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
        loadCategories();
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
      // Ici, vous pouvez ajouter une fonction pour charger et afficher la liste des catégories
      // Par exemple : loadCategoriesList();
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
      loadParentCategories();
      break;
    default:
      tabContent.innerHTML = "<p>Contenu non disponible</p>";
  }
}

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
    console.log("Début de loadCategories()");
    const categoriesContainer = document.getElementById('categories-container');
  
    if (!categoriesContainer) {
        console.log("categoriesContainer non trouvé");
        return;
    }
  
    // Créer le bouton dropdown et le conteneur
    categoriesContainer.innerHTML = `
        <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
            Sélectionner les catégories 
            <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
            </svg>
        </button>
        <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
            <div class="p-3">
                <label for="input-group-search" class="sr-only">Rechercher</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-group-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Rechercher une catégorie">
                </div>
            </div>
            <ul id="categories-list" class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
                <!-- Les catégories seront ajoutées ici dynamiquement -->
            </ul>
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
                <div class="flex items-center p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                    <input id="category-${category.id_categorie}" type="checkbox" name="categories[]" value="${category.id_categorie}" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                    <label for="category-${category.id_categorie}" class="w-full ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">${category.nom}</label>
                </div>
            `;
            categoriesList.appendChild(li);
        });
  
        console.log("Fin du remplissage des catégories");
        setupCategorySearch();
    })
    .catch(error => {
        console.error("Erreur lors du chargement des catégories:", error);
    });
  
    console.log("Fin de loadCategories()");
}

function setupCategorySearch() {
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