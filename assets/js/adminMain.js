document.addEventListener('DOMContentLoaded', function() {
    // Charger le contenu initial (par exemple, les articles)
    loadContent('articles');

    // Ajouter des écouteurs d'événements pour les liens de navigation
    document.getElementById('articles-link').addEventListener('click', () => loadContent('articles'));
    document.getElementById('categories-link').addEventListener('click', () => loadContent('categories'));
});

function loadContent(section) {
    switch(section) {
        case 'articles':
            ContentLoader.loadArticles();
            break;
        case 'categories':
            ContentLoader.loadCategories();
            break;
        case 'add-article':
            ContentLoader.loadAddArticleForm();
            break;
        // ... autres cas ...
    }
}

function switchTab(tab, tabName) {
    // Implémentez la logique de changement d'onglet ici
    // Cette fonction devrait être déplacée vers contentLoader.js
}

// Note: loadParentCategories devrait être déplacé vers categoryManager.js
