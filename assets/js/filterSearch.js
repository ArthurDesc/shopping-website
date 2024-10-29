document.addEventListener('DOMContentLoaded', function() {
    // Recherche générale des produits
    const productsSearch = document.getElementById('products-search');
    const productCards = document.querySelectorAll('.product-card');

    if (productsSearch) {
        productsSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            productCards.forEach(card => {
                const productName = card.querySelector('.product-name').textContent.toLowerCase();
                const productDescription = card.querySelector('.product-description')?.textContent.toLowerCase() || '';
                
                if (productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Recherche dans les catégories
    const categoriesSearch = document.getElementById('categories-search');
    const categoriesList = document.getElementById('categories-list');

    if (categoriesSearch && categoriesList) {
        categoriesSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const categoryItems = categoriesList.querySelectorAll('.checkbox-container');
            
            categoryItems.forEach(item => {
                const categoryName = item.querySelector('span').textContent.toLowerCase();
                if (categoryName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
