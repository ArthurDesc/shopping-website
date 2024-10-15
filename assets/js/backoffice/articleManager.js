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

    function loadArticles() {
        console.log("Chargement des articles");
        const tabContent = document.getElementById("tab-content");
        
        fetch('/shopping-website/admin/load_articles.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Utilisez .text() au lieu de .json()
            })
            .then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error("Réponse du serveur:", text);
                    throw new Error("La réponse du serveur n'est pas un JSON valide");
                }
            })
            .then(articles => {
                console.log("Articles chargés:", articles);
                
                if (articles.error) {
                    throw new Error(articles.error);
                }
                
                let articlesHTML = `
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                `;

                articles.forEach(article => {
                    articlesHTML += `
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <img src="${article.image_url}" alt="Image de ${article.nom}" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-lg font-semibold mb-2">${article.nom}</h3>
                                <p class="text-sm text-gray-600 mb-2">${article.marque}</p>
                                <p class="text-sm text-gray-600 mb-2">Prix : ${article.prix} €</p>
                                <p class="text-sm text-gray-600 mb-2">Catégories : ${article.categories}</p>
                                <div class="flex justify-between items-center">
                                    <button class="text-red-500 hover:text-red-700" onclick="ArticleManager.deleteArticle(${article.id_produit})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <button class="text-blue-500 hover:text-blue-700" onclick="ArticleManager.editArticle(${article.id_produit})">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                articlesHTML += `</div>`;
                tabContent.innerHTML = articlesHTML;
            })
            .catch(error => {
                console.error("Erreur lors du chargement des articles:", error);
                tabContent.innerHTML = `<p>Erreur lors du chargement des articles: ${error.message}</p>`;
                if (typeof showToast === 'function') {
                    showToast("Erreur lors du chargement des articles: " + error.message, "error");
                }
            });
    }
 

    return {
        deleteArticle: deleteArticle,
        editArticle: editArticle,
        loadArticles: loadArticles
    };
})();
