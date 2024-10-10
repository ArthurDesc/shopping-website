function loadArticlesList() {
    // Code pour charger la liste des articles
}

// Autres fonctions liées à la gestion des articles

function addArticle(articleData) {
    return API.addArticle(articleData);
}

function updateArticle(articleId, articleData) {
    return API.updateArticle(articleId, articleData);
}

function deleteArticle(articleId) {
    return API.deleteArticle(articleId);
}