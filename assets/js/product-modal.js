document.addEventListener('DOMContentLoaded', function() {
    let currentProductId = null;
    let currentProductPrice = null;
    const productSize = document.getElementById('productSize');
    const sizeError = document.getElementById('sizeError');
    const productsSearch = document.getElementById('products-search');

    // Modifier la gestion des clics sur les boutons
    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Important : empêcher la propagation pour ne pas interférer avec le lien parent
            e.stopPropagation();
            e.preventDefault();
            
            currentProductId = this.dataset.productId;
            currentProductPrice = this.dataset.productPrice;

            if (productSize) {
                productSize.innerHTML = `
                    <option value="">Choisissez une taille</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                `;
            }

            document.getElementById('modal-container').classList.add('active');
        });
    });

    // S'assurer que les clics sur les liens produits fonctionnent
    document.querySelectorAll('.product-card a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Ne pas empêcher la navigation si le clic n'est pas sur le bouton panier
            if (!e.target.closest('.open-modal-btn')) {
                // Laisser la navigation se faire normalement
                return true;
            }
        });
    });

    // Gestion de la recherche
    if (productsSearch) {
        productsSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(product => {
                const productName = product.querySelector('h3').textContent.toLowerCase();
                product.style.display = productName.startsWith(searchTerm) ? '' : 'none';
            });
        });
    }

    // Gestion des événements du modal
    if (productSize) {
        productSize.addEventListener('change', function() {
            sizeError.classList.add('hidden');
        });
    }
});
