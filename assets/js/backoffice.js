function loadContent(page) {
    const title = document.getElementById('page-title');
    const contentArea = document.getElementById('content-area');

    // Changer le titre de la page
    switch(page) {
        case 'dashboard':
            title.textContent = 'Tableau de bord';
            break;
        case 'products':
            title.textContent = 'Gestion des produits';
            break;
        case 'categories':
            title.textContent = 'Gestion des catégories';
            break;
        case 'users':
            title.textContent = 'Gestion des utilisateurs';
            break;
    }

    // Charger le contenu
    fetch(`get_content.php?page=${page}`)
        .then(response => response.text())
        .then(data => {
            contentArea.innerHTML = data;
        })
        .catch(error => {
            console.error('Erreur:', error);
            contentArea.innerHTML = '<p>Une erreur est survenue lors du chargement du contenu.</p>';
        });
}

// Charger le tableau de bord par défaut
loadContent('dashboard');