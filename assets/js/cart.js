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
            
            showToast('Panier mis à jour', 'success');
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
        title: 'Attention !',
        text: 'Voulez-vous supprimer cet article du panier ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6e7881',
        confirmButtonText: 'Supprimer',
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

function showToast(message, type = 'success') {
    Swal.fire({
        text: message,
        icon: type,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}
