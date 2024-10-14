const ArticleManager = (function() {
    function deleteArticle(articleId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer cet article ?")) {
            fetch('/shopping-website/admin/delete_article.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_article: articleId })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast("Article supprimé avec succès", "success");
                    loadArticles(); // Recharger la liste des articles
                } else {
                    showToast("Erreur lors de la suppression de l'article : " + (data.message || "Erreur inconnue"), "error");
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
                showToast("Une erreur s'est produite lors de la suppression de l'article", "error");
            });
        }
    }

    function editArticle(id) {
        // Rediriger vers une page d'édition avec un paramètre pour indiquer le mode édition
        window.location.href = '/shopping-website/pages/detail.php?id=' + id + '&mode=edit';
    }

 

    return {
        deleteArticle: deleteArticle,
        editArticle: editArticle,
    };
})();
