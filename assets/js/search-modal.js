document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-container');
    const productSize = document.getElementById('productSize');
    const sizeError = document.getElementById('sizeError');
    let currentProductId = null;
    let currentProductPrice = null;

    // Gestionnaire pour les boutons d'ouverture du modal
    document.addEventListener('click', function(e) {
        const openModalBtn = e.target.closest('.open-modal-btn');
        if (openModalBtn) {
            e.preventDefault();
            e.stopPropagation();
            
            currentProductId = openModalBtn.dataset.productId;
            currentProductPrice = openModalBtn.dataset.productPrice;
            const productSizes = openModalBtn.dataset.productSizes.split(',');
            
            // Mettre à jour le prix dans le bouton d'ajout au panier
            const addToCartBtn = document.getElementById('addToCartBtn');
            if (addToCartBtn) {
                addToCartBtn.setAttribute('data-tooltip', currentProductPrice + ' €');
            }
            
            // Réinitialiser et charger les tailles disponibles
            productSize.innerHTML = '<option value="">Choisissez une taille</option>';
            productSizes.forEach(size => {
                if (size.trim()) {
                    const option = document.createElement('option');
                    option.value = size.trim();
                    option.textContent = size.trim();
                    productSize.appendChild(option);
                }
            });
            
            modal.classList.remove('hidden');
        }
    });

    // Gestionnaire pour les boutons Ajouter au panier
    const addToCartBtnDesktop = document.getElementById('addToCartBtn');
    const addToCartBtnMobile = document.getElementById('addToCartBtnMobile');

    if (addToCartBtnDesktop) {
        addToCartBtnDesktop.addEventListener('click', handleAddToCart);
    }

    if (addToCartBtnMobile) {
        addToCartBtnMobile.addEventListener('click', handleAddToCart);
    }

    function handleAddToCart() {
        const selectedSize = productSize.value;
        
        if (!selectedSize) {
            sizeError.textContent = 'Veuillez choisir une taille';
            sizeError.classList.remove('hidden');
            return;
        }

        sizeError.classList.add('hidden');

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
                closeModal();
                updateCartCount(data.cartCount);
                showToast('Article ajouté au panier', 'success');
            } else {
                showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showToast('Une erreur s\'est produite', 'error');
        });
    }

    // Gestionnaire pour le bouton Annuler
    document.getElementById('cancelBtn').addEventListener('click', function(e) {
        e.preventDefault();
        closeModal();
    });

    // Fermeture du modal en cliquant à l'extérieur
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    function closeModal() {
        modal.classList.add('hidden');
        productSize.value = '';
        sizeError.classList.add('hidden');
    }

    function updateCartCount(count) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
            if (count > 0) {
                cartCountElement.classList.remove('bg-red-600');
                cartCountElement.classList.add('bg-green-600');
            }
        }
    }
});
