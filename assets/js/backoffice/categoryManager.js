function loadCategoriesList() {
    // Appel à l'API pour récupérer la liste des catégories
    API.getCategories().then((categories) => {
      const categoriesList = document.getElementById("categories-list");
      categoriesList.innerHTML = categories
        .map(
          (category) => `
              <div class="category-item">
                  <h3>${category.nom}</h3>
                  <p>${category.description}</p>
              </div>
          `
        )
        .join("");
    });
  }
  
  // Fonction pour charger les catégories
  function loadCategories() {
    // Appel AJAX pour récupérer les catégories
    fetch("/shopping-website/admin/get_categories.php")
      .then((response) => response.json())
      .then((categories) => {
        const categoriesSelect = document.getElementById("categories");
        if (categories.length > 0) {
          categories.forEach((category) => {
            const option = document.createElement("option");
            option.value = category.id;
            option.textContent = category.nom;
            categoriesSelect.appendChild(option);
          });
          categoriesSelect.parentElement.style.display = "block";
        } else {
          categoriesSelect.parentElement.style.display = "none";
        }
      })
      .catch((error) =>
        console.error("Erreur lors du chargement des catégories:", error)
      );
  }
  
  // Appelez loadCategories après avoir généré le formulaire
  loadCategories();