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
                // Mettre à jour l'interface utilisateur (par exemple, le nombre d'articles dans le panier)
                updateCartUI(data.cartCount);
            } else {
                alert('Erreur lors de l\'ajout au panier : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur s\'est produite lors de l\'ajout au panier.');
        });
    }

    function updateCartUI(cartCount) {
        // Mettre à jour le nombre d'articles dans le panier dans l'interface utilisateur
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = cartCount;
        }
    }
});
