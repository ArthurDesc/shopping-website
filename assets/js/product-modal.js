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
            currentProductPrice = parseFloat(this.dataset.productPrice);
            
            // Réinitialiser et charger les tailles
            productSize.innerHTML = `
                <option value="">Choisissez une taille</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            `;
            
            // Mettre à jour le prix dans le bouton et le tooltip
            const addToCartBtn = document.getElementById('addToCartBtn');
            const formattedPrice = new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            }).format(currentProductPrice);
            
            // Mettre à jour le tooltip avec le prix
            addToCartBtn.setAttribute('data-tooltip', `Ajouter - ${formattedPrice}`);
            
            // Mettre à jour le prix dans le bouton
            const priceElement = document.querySelector('.cart-add-button-price');
            if (priceElement) {
                priceElement.textContent = formattedPrice;
            }
            
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
        // Définir le contenu et le style
        toast.textContent = message;
        toast.className = `fixed right-4 top-[70px] py-2 px-4 rounded shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        
        // Ajouter la classe show pour déclencher l'animation
        toast.classList.add('show');
        
        // Retirer la classe show après 3 secondes
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
}
