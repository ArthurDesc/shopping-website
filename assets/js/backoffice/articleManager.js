function deleteArticle(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet article ?")) {
        fetch('/shopping-website/admin/delete_article.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la liste des articles
                loadArticles();
            } else {
                alert("Erreur lors de la suppression de l'article");
            }
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