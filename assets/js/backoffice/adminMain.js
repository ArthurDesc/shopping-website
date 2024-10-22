document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM chargé");
    if (window.UIManager) {
        console.log("UIManager disponible:", window.UIManager);
        if (typeof window.UIManager.setupCategorySearch === 'function') {
            console.log("setupCategorySearch disponible:", true);
        } else {
            console.log("setupCategorySearch n'est pas disponible");
        }
    } else {
        console.log("UIManager n'est pas disponible");
    }
    
    if (typeof CategoryManager === 'undefined') {
        console.error("CategoryManager n'est pas défini. Vérifiez l'ordre de chargement des scripts.");
        return;
    }
    loadContent('articles');

    // Attendre un court instant pour que le contenu soit chargé
    setTimeout(() => {
        const form = document.querySelector('form');
        if (form) {
            console.log('Formulaire trouvé');
            // Ajoutez ici la logique pour gérer le formulaire
        } else {
            console.log('Formulaire non trouvé');
        }
    }, 100);
});

function loadContent(section) {
    const contentArea = document.getElementById('content-area');
    const articlesLink = document.getElementById('articles-link');
    const categoriesLink = document.getElementById('categories-link');
    const articlesLinkDesktop = document.getElementById('articles-link-desktop');
    const categoriesLinkDesktop = document.getElementById('categories-link-desktop');

    // Réinitialiser les styles pour mobile et desktop
    [articlesLink, categoriesLink, articlesLinkDesktop, categoriesLinkDesktop].forEach(link => {
        link.classList.remove('active-tab');
    });

    switch (section) {
        case 'articles':
            articlesLink.classList.add('active-tab');
            articlesLinkDesktop.classList.add('active-tab');
            contentArea.innerHTML = `
                <div class="bg-white shadow-md rounded-lg p-6">
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
                        <!-- Le contenu des onglets sera chargé ici dynamiquement -->
                    </div>
                </div>
            `;
            switchTab(document.querySelector('[onclick="switchTab(this, \'ajouter\')"]'), 'ajouter');
            break;
        case 'categories':
            categoriesLink.classList.add('active-tab');
            categoriesLinkDesktop.classList.add('active-tab');
            contentArea.innerHTML = `
                <div class="bg-white shadow-md rounded-lg p-6">
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
                        <!-- Le contenu des onglets de catégories sera chargé ici dynamiquement -->
                    </div>
                </div>
            `;
            switchCategoryTab(document.querySelector('[onclick="switchCategoryTab(this, \'ajouter\')"]'), 'ajouter');
            break;
        default:
            contentArea.innerHTML = '<p>Sélectionnez une option dans la barre latérale</p>';
    }
}




