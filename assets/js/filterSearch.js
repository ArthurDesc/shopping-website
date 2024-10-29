document.addEventListener('DOMContentLoaded', function() {
    // Fonction générique pour la recherche dans les listes
    function setupSearch(searchInputId, listId) {
        const searchInput = document.getElementById(searchInputId);
        const list = document.getElementById(listId);

        if (searchInput && list) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const items = list.querySelectorAll('.checkbox-container');
                
                items.forEach(item => {
                    const text = item.querySelector('span').textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }

    // Configuration des différentes recherches
    setupSearch('categories-search', 'categories-list');
    setupSearch('marques-search', 'marques-list');
    
    // Recherche générale des produits
    const productsSearch = document.getElementById('products-search');
    if (productsSearch) {
        productsSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
                const productBrand = card.querySelector('.product-brand')?.textContent.toLowerCase() || '';
                
                if (productName.includes(searchTerm) || productBrand.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
