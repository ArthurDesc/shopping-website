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

        fetch('ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                id_produit: currentProductId,
                taille: selectedSize,
                quantite: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.classList.remove('active');
                updateCartCount(data.cartCount);
                showToast('Article ajouté au panier', 'success');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Erreur lors de l\'ajout au panier', 'error');
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
