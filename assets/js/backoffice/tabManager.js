function switchTab(clickedTab, tabId) {
    document.querySelectorAll('a[onclick^="switchTab"]').forEach(tab => {
        tab.classList.remove('text-blue-600', 'bg-gray-100', 'border-blue-600', 'active');
        tab.classList.add('text-gray-500', 'hover:text-gray-600', 'hover:bg-gray-50', 'border-transparent');
    });
    clickedTab.classList.remove('text-gray-500', 'hover:text-gray-600', 'hover:bg-gray-50', 'border-transparent');
    clickedTab.classList.add('text-blue-600', 'bg-gray-100', 'border-blue-600', 'active');

    const tabContent = document.getElementById('tab-content');
    switch (tabId) {
        case 'modifier':
            tabContent.innerHTML = `
                <div class="mb-4">
                    <div class="relative">
                        <input type="text" id="search-articles" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Rechercher un article...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div id="articles-list" class="space-y-2">
                    <!-- La liste des articles sera chargée ici dynamiquement -->
                </div>
            `;
            // Ici, vous pouvez ajouter une fonction pour charger et afficher la liste des articles
            // Par exemple : loadArticlesList();
            break;
        case 'ajouter':
            tabContent.innerHTML = `
                <form class="gradient-blue p-4 sm:p-6 rounded-lg shadow-md w-full mx-auto max-w-4xl">
                    <div class="mb-4 sm:mb-5">
                        <label for="titre" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Titre</label>
                        <input type="text" id="titre" name="titre" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez le titre">
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="description" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez la description"></textarea>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="image" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Nom de l'image</label>
                        <input type="text" id="image" name="image" class="w-full px-3 py-1 rounded text-sm sm:text-base" placeholder="Entrez le nom de l'image">
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
                        <select id="stock" name="stock" class="w-full px-3 py-1 rounded text-sm sm:text-base">
                            <option value="" disabled selected>Choisissez le stock</option>
                            <!-- Ajoutez ici les options de stock -->
                        </select>
                    </div>
                    <div class="mb-4 sm:mb-5">
                        <label for="categories" class="block text-white font-semibold mb-1 sm:mb-2 text-base sm:text-lg">Catégories</label>
                        <select id="categories" name="categories" class="w-full px-3 py-1 rounded text-sm sm:text-base">
                            <option value="" disabled selected>Choisissez les catégories</option>
                            <!-- Ajoutez ici les options de catégories -->
                        </select>
                    </div>
                    <button type="submit" class="bg-white text-blue-500 px-6 py-2 rounded text-sm sm:text-base hover:bg-blue-100 font-semibold w-full sm:w-auto">Valider</button>
                </form>
            `;
            break;
        default:
            tabContent.innerHTML = '<p>Contenu non disponible</p>';
    }
}
function switchCategoryTab(clickedTab, tabId) {
    document.querySelectorAll('a[onclick^="switchCategoryTab"]').forEach(tab => {
        tab.classList.remove('text-blue-600', 'bg-gray-100', 'border-blue-600', 'active');
        tab.classList.add('text-gray-500', 'hover:text-gray-600', 'hover:bg-gray-50', 'border-transparent');
    });
    clickedTab.classList.remove('text-gray-500', 'hover:text-gray-600', 'hover:bg-gray-50', 'border-transparent');
    clickedTab.classList.add('text-blue-600', 'bg-gray-100', 'border-blue-600', 'active');

    const tabContent = document.getElementById('category-tab-content');
    switch (tabId) {
        case 'modifier':
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
        case 'ajouter':
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
            tabContent.innerHTML = '<p>Contenu non disponible</p>';
    }
}

function loadArticlesList() {
    // Appel à l'API pour récupérer la liste des articles
    API.getArticles().then(articles => {
        const articlesList = document.getElementById('articles-list');
        articlesList.innerHTML = articles.map(article => `
            <div class="article-item">
                <h3>${article.nom}</h3>
                <p>${article.description}</p>
                <p>Prix : ${article.prix} €</p>
            </div>
        `).join('');
    });
}

function loadCategoriesList() {
    // Appel à l'API pour récupérer la liste des catégories
    API.getCategories().then(categories => {
        const categoriesList = document.getElementById('categories-list');
        categoriesList.innerHTML = categories.map(category => `
            <div class="category-item">
                <h3>${category.nom}</h3>
                <p>${category.description}</p>
            </div>
        `).join('');
    });
}