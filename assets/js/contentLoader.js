// contentLoader.js
const ContentLoader = {
    loadArticles: async function() {
        const articles = await API.getArticles();
        let html = '<h2 class="text-2xl font-bold mb-4">Articles</h2>';
        html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';
        articles.forEach(article => {
            html += `
                <div class="bg-white shadow rounded-lg p-4">
                    <h3 class="text-lg font-semibold">${article.nom}</h3>
                    <p class="text-gray-600">${article.description}</p>
                    <p class="text-blue-600 font-bold mt-2">${article.prix} €</p>
                </div>
            `;
        });
        html += '</div>';
        document.getElementById('content-area').innerHTML = html;
    },

    loadCategories: async function() {
        const categories = await API.getCategories();
        let html = '<h2 class="text-2xl font-bold mb-4">Catégories</h2>';
        html += '<ul class="list-disc list-inside">';
        categories.forEach(category => {
            html += `<li>${category.nom}</li>`;
        });
        html += '</ul>';
        document.getElementById('content-area').innerHTML = html;
    },

    loadAddArticleForm: function() {
        let html = `
            <h2 class="text-2xl font-bold mb-4">Ajouter un article</h2>
            <form id="add-article-form" class="space-y-4">
                <div>
                    <label for="nom" class="block mb-1">Nom</label>
                    <input type="text" id="nom" name="nom" required class="w-full px-3 py-2 border rounded">
                </div>
                <div>
                    <label for="description" class="block mb-1">Description</label>
                    <textarea id="description" name="description" required class="w-full px-3 py-2 border rounded"></textarea>
                </div>
                <div>
                    <label for="prix" class="block mb-1">Prix</label>
                    <input type="number" id="prix" name="prix" step="0.01" required class="w-full px-3 py-2 border rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Ajouter</button>
            </form>
        `;
        document.getElementById('content-area').innerHTML = html;
        this.setupAddArticleForm();
    },

    setupAddArticleForm: function() {
        const form = document.getElementById('add-article-form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(form);
            const articleData = Object.fromEntries(formData.entries());
            const result = await API.addArticle(articleData);
            if (result.success) {
                alert('Article ajouté avec succès!');
                this.loadArticles(); // Recharger la liste des articles
            } else {
                alert('Erreur lors de l\'ajout de l\'article: ' + result.message);
            }
        });
    },

    // Ajoutez d'autres méthodes de chargement selon vos besoins...
};