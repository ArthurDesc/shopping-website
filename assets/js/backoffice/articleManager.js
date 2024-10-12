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
                return response.text().then(text => {
                    throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                });
            }
            return response.text();
        })
        .then(text => {
            console.log("Réponse brute du serveur:", text); // Pour le débogage
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error("Erreur lors du parsing JSON:", error);
                throw new Error("Réponse invalide du serveur");
            }
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
            showToast("Une erreur s'est produite lors de la suppression de l'article: " + error.message, "error");
        });
    }
}

function editArticle(id) {
    // Rediriger vers une page d'édition ou ouvrir un modal pour éditer l'article
    window.location.href = '/shopping-website/admin/edit_article.php?id=' + id;
}

function loadArticles() {
    fetch('/shopping-website/admin/load_articles.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById("tab-content").innerHTML = data;
        })
        .catch(error => {
            console.error('Erreur lors du chargement des articles:', error);
            document.getElementById("tab-content").innerHTML = '<p>Une erreur est survenue lors du chargement des articles.</p>';
        });
}

function loadArticles() {
    fetch('/shopping-website/admin/load_articles.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById("tab-content").innerHTML = data;
        })
        .catch(error => {
            console.error('Erreur lors du chargement des articles:', error);
            document.getElementById("tab-content").innerHTML = '<p>Une erreur est survenue lors du chargement des articles.</p>';
        });
}