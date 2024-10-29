document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-container');
    const productSize = document.getElementById('productSize');
    const sizeError = document.getElementById('sizeError');
    let currentProductId = null;
    let currentProductPrice = null;

    // Gestionnaire pour les boutons d'ajout au panier
    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            currentProductId = this.dataset.productId;
            currentProductPrice = this.dataset.productPrice;
            
            // Réinitialiser et charger les tailles
            productSize.innerHTML = `
                <option value="">Choisissez une taille</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            `;
            
            modal.classList.add('active');
        });
    });

    // Gestionnaire pour le bouton Ajouter au panier
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        const selectedSize = productSize.value;
        
        if (!selectedSize) {
            sizeError.textContent = 'Veuillez choisir une taille';
            sizeError.classList.remove('hidden');
            return;
        }

        // Cacher le message d'erreur
        sizeError.classList.add('hidden');

        // Utiliser FormData pour la cohérence
        const formData = new FormData();
        formData.append('id_produit', currentProductId);
        formData.append('taille', selectedSize);
        formData.append('quantite', '1');

        fetch('/shopping-website/ajax/add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.classList.remove('active');
                updateCartUI(data.cartCount);
                showToast('Article ajouté au panier', 'success');
            } else {
                showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur s\'est produite', 'error');
        });
    });

    // Gestionnaire pour le bouton Annuler
    document.getElementById('cancelBtn').addEventListener('click', function(e) {
        e.preventDefault();
        modal.classList.remove('active');
        productSize.value = '';
        sizeError.classList.add('hidden');
    });

    // Fermeture du modal en cliquant à l'extérieur
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
            productSize.value = '';
            sizeError.classList.add('hidden');
        }
    });
});

function updateCartUI(cartCount) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
        
        // Mise à jour de la couleur du badge
        if (cartCount > 0) {
            cartCountElement.classList.remove('bg-red-600');
            cartCountElement.classList.add('bg-green-600');
        }
    }
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.classList.remove('opacity-0');
        toast.classList.add('opacity-100');
        
        // Masquer le toast après 3 secondes
        setTimeout(() => {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0');
        }, 3000);
    }
}
