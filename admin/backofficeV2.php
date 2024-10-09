<?php include '../includes/session.php'; ?>
<?php include '../includes/_db.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
    <title>BackOffice</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar bg-white shadow-md">
        <div class="flex justify-between items-center sm:flex-col h-full px-4 sm:px-0">
            <a id="articles-link" class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16 hover:bg-gray-100" href="#" onclick="loadContent('articles')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                <span class="text-[10px] sm:text-xs">Articles</span>
            </a>
            <a class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16" href="../index.php">
                <img src="<?php echo BASE_URL; ?>assets/images/logoF.png" alt="Logo F" class="w-6 h-6 sm:w-8 sm:h-8 object-contain mb-1 sm:mb-0">
                <span class="text-[10px] sm:text-xs">Accueil</span>
            </a>
            <a id="categories-link" class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16 hover:bg-gray-100" href="#" onclick="loadContent('categories')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                </svg>
                <span class="text-[10px] sm:text-xs">Catégories</span>
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <main class="main-content p-4">
        <div id="content-area" class="max-w-5xl mx-auto">
            <!-- Le contenu sera chargé ici dynamiquement -->
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/pagedone@1.2.2/src/js/pagedone.js"></script>
    <script>
        <!-- Ajoutez ce script à la fin de votre fichier, juste avant la fermeture de la balise </body> -->
document.addEventListener('DOMContentLoaded', function () {
    const accordionElement = document.getElementById('accordion-color');

    // create an array of objects with the id, trigger element (eg. button), and the content element
    const accordionItems = [
        {
            id: 'accordion-color-heading-1',
            triggerEl: document.querySelector('#accordion-color-heading-1 button'),
            targetEl: document.querySelector('#accordion-color-body-1'),
            active: true
        },
        {
            id: 'accordion-color-heading-2',
            triggerEl: document.querySelector('#accordion-color-heading-2 button'),
            targetEl: document.querySelector('#accordion-color-body-2'),
            active: false
        }
    ];

    // options with default values
    const options = {
        alwaysOpen: false,
        activeClasses: 'bg-blue-100 dark:bg-gray-800 text-blue-600 dark:text-white',
        inactiveClasses: 'text-gray-500 dark:text-gray-400',
        onOpen: (item) => {
            console.log('accordion item has been shown');
            console.log(item);
        },
        onClose: (item) => {
            console.log('accordion item has been hidden');
            console.log(item);
        },
        onToggle: (item) => {
            console.log('accordion item has been toggled');
            console.log(item);
        },
    };

    // instance options object
    const instanceOptions = {
        id: 'accordion-color',
        override: true
    };

    // create an instance of the Accordion
    const accordion = new Accordion(accordionElement, accordionItems, options, instanceOptions);

    // you can use the instance's methods
    accordion.open(1); // opens the second accordion item
});
        function loadContent(section) {
            const contentArea = document.getElementById('content-area');
            const articlesLink = document.getElementById('articles-link');
            const categoriesLink = document.getElementById('categories-link');

            // Réinitialiser les styles
            articlesLink.classList.remove('text-blue-500');
            categoriesLink.classList.remove('text-blue-500');

            switch (section) {
                case 'articles':
                    articlesLink.classList.add('text-blue-500');
                    contentArea.innerHTML = `
                    <h1 class="text-2xl font-normal mb-4 text-center alata-font">Articles</h1>
                    <div class="shadow-md">
                        <ul class="flex text-xs sm:text-sm font-medium text-center text-gray-500 bg-white rounded-t-lg w-full">
                            <li class="flex-1">
                                <a href="#" onclick="switchTab(this, 'ajouter')" class="inline-flex flex-col items-center justify-center w-full p-2 sm:p-3 text-blue-600 bg-gray-100 border-b-2 border-blue-600 rounded-tl-lg active group" aria-current="page">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-1 sm:mb-2">
                                        <path d="M9 15H11V11H15V9H11V5H9V9H5V11H9V15ZM10 20C8.61667 20 7.31667 19.7417 6.1 19.225C4.88333 18.6917 3.825 17.975 2.925 17.075C2.025 16.175 1.30833 15.1167 0.775 13.9C0.258333 12.6833 0 11.3833 0 10C0 8.61667 0.258333 7.31667 0.775 6.1C1.30833 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.31667 6.1 0.799999C7.31667 0.266666 8.61667 0 10 0C11.3833 0 12.6833 0.266666 13.9 0.799999C15.1167 1.31667 16.175 2.025 17.075 2.925C17.975 3.825 18.6833 4.88333 19.2 6.1C19.7333 7.31667 20 8.61667 20 10C20 11.3833 19.7333 12.6833 19.2 13.9C18.6833 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6917 13.9 19.225C12.6833 19.7417 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="#007AFF"/>
                                    </svg>
                                    Ajouter
                                </a>
                            </li>
                            <li class="flex-1">
                                <a href="#" onclick="switchTab(this, 'modifier')" class="inline-flex flex-col items-center justify-center w-full p-2 sm:p-3 border-b-2 border-transparent rounded-tr-lg hover:text-gray-600 hover:bg-gray-50 group">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-1 sm:mb-2">
                                        <path d="M9.625 18.1667H18.25M13.9375 2.35416C14.3187 1.97291 14.8358 1.75873 15.375 1.75873C15.642 1.75873 15.9063 1.81131 16.153 1.91348C16.3996 2.01564 16.6237 2.16538 16.8125 2.35416C17.0013 2.54294 17.151 2.76704 17.2532 3.01369C17.3553 3.26034 17.4079 3.52469 17.4079 3.79166C17.4079 4.05863 17.3553 4.32298 17.2532 4.56963C17.151 4.81628 17.0013 5.04038 16.8125 5.22916L4.83333 17.2083L1 18.1667L1.95833 14.3333L13.9375 2.35416Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Modifier
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="tab-content" class="mt-4">
                        <!-- Le contenu des onglets sera chargé ici -->
                    </div>
                    `;
                    switchTab(document.querySelector('[aria-current="page"]'), 'ajouter');
                    break;
                case 'categories':
                    categoriesLink.classList.add('text-blue-500');
                    contentArea.innerHTML = `
                        <h1 class="text-2xl font-normal mb-4 text-center alata-font">Catégories</h1>
                        <div class="shadow-md">
                            <ul class="flex text-xs sm:text-sm font-medium text-center text-gray-500 bg-white rounded-t-lg w-full">
                                <li class="flex-1">
                                    <a href="#" onclick="switchCategoryTab(this, 'ajouter')" class="inline-flex flex-col items-center justify-center w-full p-2 sm:p-3 text-blue-600 bg-gray-100 border-b-2 border-blue-600 rounded-tl-lg active group" aria-current="page">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-1 sm:mb-2">
                                            <path d="M9 15H11V11H15V9H11V5H9V9H5V11H9V15ZM10 20C8.61667 20 7.31667 19.7417 6.1 19.225C4.88333 18.6917 3.825 17.975 2.925 17.075C2.025 16.175 1.30833 15.1167 0.775 13.9C0.258333 12.6833 0 11.3833 0 10C0 8.61667 0.258333 7.31667 0.775 6.1C1.30833 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.31667 6.1 0.799999C7.31667 0.266666 8.61667 0 10 0C11.3833 0 12.6833 0.266666 13.9 0.799999C15.1167 1.31667 16.175 2.025 17.075 2.925C17.975 3.825 18.6833 4.88333 19.2 6.1C19.7333 7.31667 20 8.61667 20 10C20 11.3833 19.7333 12.6833 19.2 13.9C18.6833 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6917 13.9 19.225C12.6833 19.7417 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="#007AFF"/>
                                        </svg>
                                        Ajouter
                                    </a>
                                </li>
                                <li class="flex-1">
                                    <a href="#" onclick="switchCategoryTab(this, 'modifier')" class="inline-flex flex-col items-center justify-center w-full p-2 sm:p-3 border-b-2 border-transparent rounded-tr-lg hover:text-gray-600 hover:bg-gray-50 group">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-1 sm:mb-2">
                                            <path d="M9.625 18.1667H18.25M13.9375 2.35416C14.3187 1.97291 14.8358 1.75873 15.375 1.75873C15.642 1.75873 15.9063 1.81131 16.153 1.91348C16.3996 2.01564 16.6237 2.16538 16.8125 2.35416C17.0013 2.54294 17.151 2.76704 17.2532 3.01369C17.3553 3.26034 17.4079 3.52469 17.4079 3.79166C17.4079 4.05863 17.3553 4.32298 17.2532 4.56963C17.151 4.81628 17.0013 5.04038 16.8125 5.22916L4.83333 17.2083L1 18.1667L1.95833 14.3333L13.9375 2.35416Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Modifier
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div id="category-tab-content" class="mt-4">
                            <!-- Le contenu des onglets sera chargé ici -->
                        </div>
                    `;
                    switchCategoryTab(document.querySelector('[aria-current="page"]'), 'ajouter');
                    break;
                default:
                    contentArea.innerHTML = '<p>Sélectionnez une option dans la barre latérale</p>';
            }
        }

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
  <div class="flex flex-col items-center">
    <h2 class="font-bold text-3xl mt-5 tracking-tight">
      Gestion des catégories
    </h2>
  </div>
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

        function loadParentCategories() {
            // Simulons le chargement des catégories parentes
            const parentCategories = [{
                    id: 1,
                    name: 'Électronique'
                },
                {
                    id: 2,
                    name: 'Vêtements'
                },
                {
                    id: 3,
                    name: 'Livres'
                },
                {
                    id: 4,
                    name: 'Maison'
                },
                {
                    id: 5,
                    name: 'Sports'
                }
            ];

            const parentCategoriesContainer = document.getElementById('parent-categorie-desktop');
            parentCategoriesContainer.innerHTML = parentCategories.map(category => `
                <div class="flex items-center">
                    <input type="checkbox" id="parent-${category.id}" name="parent-categories[]" value="${category.id}" class="mr-2">
                    <label for="parent-${category.id}" class="text-white">${category.name}</label>
                </div>
            `).join('');

            // Ajouter les options au select mobile
            const parentCategorySelect = document.getElementById('parent-categorie-mobile');
            parentCategories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                parentCategorySelect.appendChild(option);
            });
        }

        // Chargez le contenu initial (par exemple, les articles)
        loadContent('articles');
    </script>
    <!-- Ajoutez ce script à la fin de votre fichier, juste avant la fermeture de la balise </body> -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const accordionElement = document.querySelector('[data-accordion="categories-accordion"]');
        const accordionOptions = {
            alwaysOpen: false,
            activeClasses: 'bg-indigo-50',
            inactiveClasses: 'bg-[#007AFF]',
            onOpen: (item) => {
                const icon = item.el.querySelector('svg');
                icon.classList.add('rotate-180');
            },
            onClose: (item) => {
                const icon = item.el.querySelector('svg');
                icon.classList.remove('rotate-180');
            },
            onToggle: (item) => {
                console.log('accordion item has been toggled');
                console.log(item);
            },
        };
        const accordion = new Accordion(accordionElement, accordionOptions);
    });
    </script>
</body>

</html>