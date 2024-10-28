document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les boutons d'ajout au panier
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            addToCart(productId);
        });
    });

    function addToCart(productId) {
        fetch('/shopping-website/ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_produit=' + encodeURIComponent(productId)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartUI(data.cartCount);
                showToast('Article ajouté au panier', 'success');
            } else {
                showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur s\'est produite lors de l\'ajout au panier', 'error');
        });
    }

    function updateCartUI(cartCount) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
            
            // Mise à jour de la couleur du badge du panier
            if (cartCount > 0) {
                cartCountElement.classList.remove('bg-red-600');
                cartCountElement.classList.add('bg-green-600');
            } else {
                cartCountElement.classList.remove('bg-green-600');
                cartCountElement.classList.add('bg-red-600');
            }
        }
    }
});
