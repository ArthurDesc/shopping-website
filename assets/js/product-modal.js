document.addEventListener('DOMContentLoaded', function() {
    let currentProductId = null;
    let currentProductPrice = null;
    const productSize = document.getElementById('productSize');
    const sizeError = document.getElementById('sizeError');
    const productsSearch = document.getElementById('products-search');

    // Ouvrir le modal
    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function() {
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
