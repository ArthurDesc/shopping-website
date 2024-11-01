document.addEventListener('DOMContentLoaded', function() {
    const quantityForms = document.querySelectorAll('form[action="panier.php"]');
    
    quantityForms.forEach(form => {
        const decreaseButton = form.querySelector('button[value="decrease"]');
        const increaseButton = form.querySelector('button[value="increase"]');
        
        // Gestion de la diminution
        decreaseButton?.addEventListener('click', function(e) {
            e.preventDefault();
            handleQuantityChange(form, 'decrease');
        });
        
        // Gestion de l'augmentation
        increaseButton?.addEventListener('click', function(e) {
            e.preventDefault();
            handleQuantityChange(form, 'increase');
        });
    });
    
    // Gestion de la suppression
    document.querySelectorAll('a[href^="panier.php?del="]').forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.href.split('=')[1];
            await handleRemoveItem(productId, this);
        });
    });

    // Animation d'apparition des éléments du panier
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.5s ease-out';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100); // Délai progressif pour chaque élément
    });

    // Animation du résumé du panier
    const cartSummary = document.querySelector('.bg-gray-50');
    if (cartSummary) {
        cartSummary.style.opacity = '0';
        cartSummary.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            cartSummary.style.transition = 'all 0.5s ease-out';
            cartSummary.style.opacity = '1';
            cartSummary.style.transform = 'translateX(0)';
        }, cartItems.length * 100); // Apparaît après les articles
    }

    // Animation pour le panier vide
    const emptyCart = document.querySelector('.text-center');
    if (emptyCart) {
        emptyCart.style.opacity = '0';
        emptyCart.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            emptyCart.style.transition = 'all 0.5s ease-out';
            emptyCart.style.opacity = '1';
            emptyCart.style.transform = 'scale(1)';
        }, 100);
    }
});

async function handleQuantityChange(form, action) {
    const productId = form.querySelector('input[name="id_produit"]').value;
    const quantitySpan = form.querySelector('span');
    const taille = form.querySelector('input[name="taille"]')?.value;
    let currentQuantity = parseInt(quantitySpan.textContent);
    const stock = parseInt(form.dataset.stock);
    
    // Calculer la nouvelle quantité
    let newQuantity = action === 'increase' ? currentQuantity + 1 : currentQuantity - 1;
    
    if (newQuantity < 1 || newQuantity > stock) {
        if (newQuantity < 1) {
            await confirmRemoveItem(productId, form);
        } else {
            showToast('Stock insuffisant', 'error');
        }
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id_produit', productId);
        formData.append('quantite', newQuantity);
        if (taille) formData.append('taille', taille);
        
        const response = await updateCart(formData);
        
        if (response.success) {
            // Mettre à jour l'affichage avec les données du serveur
            const key = productId + (taille ? '_' + taille : '');
            quantitySpan.textContent = response.items[key];
            
            // Mettre à jour l'UI
            updateCartUI({
                totalItems: response.totalItems,
                totalPrice: response.totalPrice
            });
            
        } else {
            showToast(response.message || 'Erreur lors de la mise à jour', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Une erreur est survenue', 'error');
    }
}

async function confirmRemoveItem(productId, element) {
    const result = await Swal.fire({
        title: 'Supprimer l\'article ?',
        text: "Voulez-vous retirer cet article du panier ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    });

    if (result.isConfirmed) {
        const taille = element.querySelector('input[name="taille"]')?.value;
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('id_produit', productId);
        if (taille) formData.append('taille', taille);
        
        try {
            const response = await updateCart(formData);
            if (response.success) {
                const cartItem = element.closest('.cart-item');
                if (cartItem) {
                    cartItem.style.transition = 'opacity 0.3s ease-out';
                    cartItem.style.opacity = '0';
                    setTimeout(() => {
                        cartItem.remove();
                        if (response.totalItems === 0) {
                            location.reload(); // Recharger la page si le panier est vide
                        }
                    }, 300);
                }
                updateCartUI(response);
                showToast('Article supprimé du panier', 'success');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showToast('Erreur lors de la suppression', 'error');
        }
    }
}

async function handleRemoveItem(productId, element) {
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('id_produit', productId);
        
        const response = await updateCart(formData);
        if (response.success) {
            const cartItem = element.closest('.cart-item');
            if (cartItem) {
                cartItem.style.transition = 'opacity 0.3s ease-out';
                cartItem.style.opacity = '0';
                setTimeout(() => {
                    cartItem.remove();
                    if (response.totalItems === 0) {
                        const cartContainer = document.querySelector('.cart-container');
                        cartContainer.innerHTML = '<p class="text-center py-4">Votre panier est vide</p>';
                    }
                }, 300);
            }
            updateCartUI(response);
            showToast('Article supprimé du panier', 'success');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showToast('Erreur lors de la suppression', 'error');
    }
}

async function updateCart(formData) {
    try {
        const response = await fetch('/shopping-website/ajax/cart_operations.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Erreur fetch:', error);
        throw error;
    }
}

function updateCartUI(data) {
    const cartCount = document.querySelector('#cart-count');
    if (cartCount) {
        cartCount.textContent = data.totalItems;
    }
    
    const totalPrice = document.querySelector('#total-price');
    if (totalPrice) {
        totalPrice.textContent = `${data.totalPrice}€`;
    }

    // Vérifier si le panier est vide
    if (data.totalItems === 0) {
        location.reload(); // Recharger pour afficher le panier vide
    }
}



