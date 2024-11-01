function updateProductDisplay(filteredProducts) {
    const container = document.querySelector('.product-grid');
    container.style.opacity = '0';
    
    setTimeout(() => {
        container.innerHTML = filteredProducts;
        
        // Animer l'apparition des nouveaux produits
        const products = container.querySelectorAll('.product-card');
        products.forEach((product, index) => {
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                product.style.transition = 'all 0.5s ease-out';
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        container.style.opacity = '1';
        
        // Réinitialiser les écouteurs d'événements
        initializeProductButtons();
    }, 300);
}

// Fonction pour gérer les changements de filtres
function handleFilterChange() {
    const formData = new FormData(document.getElementById('filter-form'));
    
    fetch('/shopping-website/ajax/filter_products.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        updateProductDisplay(html);
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Erreur lors du filtrage', 'error');
    });
}

// Ajouter les écouteurs d'événements pour les filtres
document.querySelectorAll('#filter-form input').forEach(input => {
    input.addEventListener('change', handleFilterChange);
}); 