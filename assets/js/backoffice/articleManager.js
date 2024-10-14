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
    // Rediriger vers une page d'édition avec un paramètre pour indiquer le mode édition
    window.location.href = '/shopping-website/pages/detail.php?id=' + id + '&mode=edit';
}

function loadArticles() {
    fetch('/shopping-website/admin/load_articles.php')
        .then(response => response.text())
        .then(data => {
            const tabContent = document.getElementById("tab-content");
            tabContent.innerHTML = data;

            // Ajouter des boutons d'édition à chaque article
            const articles = tabContent.querySelectorAll('[data-article-id]');
            articles.forEach(article => {
                const articleId = article.dataset.articleId;
                const actionsCell = article.querySelector('.article-actions');
                if (actionsCell) {
                    const editButton = document.createElement('button');
                    editButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>';
                    editButton.className = 'text-blue-500 hover:text-blue-700 mr-2';
                    editButton.onclick = () => editArticle(articleId);
                    actionsCell.insertBefore(editButton, actionsCell.firstChild);
                }
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des articles:', error);
            document.getElementById("tab-content").innerHTML = '<p>Une erreur est survenue lors du chargement des articles.</p>';
        });
}
