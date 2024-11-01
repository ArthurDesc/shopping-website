document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-container');
    const productSize = document.getElementById('productSize');
    const sizeError = document.getElementById('sizeError');
    let currentProductId = null;
    let currentProductPrice = null;

    // Utiliser la délégation d'événements au niveau du document
    document.addEventListener('click', function(e) {
        if (e.target.closest('.open-modal-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.open-modal-btn');
            currentProductId = button.dataset.productId;
            currentProductPrice = button.dataset.productPrice;
            
            // Mettre à jour le prix dans le bouton d'ajout au panier
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (addToCartBtn) {
                addToCartBtn.setAttribute('data-tooltip', currentProductPrice + ' €');
            }
            
            // Réinitialiser et charger les tailles
            productSize.innerHTML = `
                <option value="">Choisissez une taille</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            `;
            
            modal.classList.add('active');
        }
    });

    // Ajouter un écouteur pour le bouton de réinitialisation des filtres
    document.getElementById('reset-filters').addEventListener('click', function() {
        // Attendre que le DOM soit mis à jour après la réinitialisation des filtres
        setTimeout(() => {
            initializeProductButtons();
        }, 100);
    });

    // Gestionnaire pour le bouton Ajouter au panier
    document.getElementById('addToCartBtn').addEventListener('click', (e) => {
        handleAddToCart(e, {
            productId: currentProductId,
            size: productSize.value,
            button: e.target,
            onSuccess: () => modal.classList.remove('active')
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


