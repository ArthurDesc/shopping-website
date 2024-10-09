const API = {
    // Fonction pour récupérer les articles
    getArticles: async function() {
        try {
            const response = await fetch(`${BASE_URL}admin/get_articles.php`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des articles:', error);
            return [];
        }
    },

    // Fonction pour récupérer les catégories
    getCategories: async function() {
        try {
            const response = await fetch(`${BASE_URL}admin/get_categories.php`);
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de la récupération des catégories:', error);
            return [];
        }
    },

    // Fonction pour ajouter un article
    addArticle: async function(articleData) {
        try {
            const response = await fetch(`${BASE_URL}admin/add_article.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(articleData)
            });
            if (!response.ok) throw new Error('Erreur réseau');
            return await response.json();
        } catch (error) {
            console.error('Erreur lors de l\'ajout de l\'article:', error);
            return { success: false, message: error.message };
        }
    },

    // Ajoutez d'autres fonctions API selon vos besoins...
};
