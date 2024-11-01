document.addEventListener('DOMContentLoaded', function() {
    initWishlistButtons();
    updateWishlistCount();
});

function initWishlistButtons() {
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        const input = button.querySelector('.wishlist-input');
        const productId = input.dataset.productId;
        
        // Vérifier l'état initial
        checkWishlistStatus(productId, input);
        
        // Ajouter l'écouteur d'événement
        input.addEventListener('change', function() {
            handleWishlistToggle(this, productId);
        });
    });
}

function handleWishlistToggle(checkbox, productId) {
    const action = checkbox.checked ? 'add' : 'remove';
    
    fetch('/shopping-website/ajax/wishlist_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: action,
            id_produit: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateWishlistCount();
        } else {
            checkbox.checked = !checkbox.checked; // Rétablir l'état précédent
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                showToast(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        checkbox.checked = !checkbox.checked;
        showToast('Une erreur est survenue', 'error');
    });
}

function checkWishlistStatus(productId, checkbox) {
    fetch('/shopping-website/ajax/wishlist_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'check',
            id_produit: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            checkbox.checked = data.inWishlist;
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function updateWishlistCount() {
    fetch('/shopping-website/ajax/wishlist_handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'count'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour le compteur dans le header
            const wishlistCounter = document.querySelector('.wishlist-counter');
            if (wishlistCounter) {
                wishlistCounter.textContent = data.count;
                wishlistCounter.style.display = data.count > 0 ? 'block' : 'none';
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (toast) {
        toast.textContent = message;
        toast.className = `fixed right-4 top-[70px] py-2 px-4 rounded shadow-lg transition-opacity duration-300 z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        
        // Afficher le toast
        toast.style.opacity = '1';
        
        // Cacher le toast après 3 secondes
        setTimeout(() => {
            toast.style.opacity = '0';
        }, 3000);
    }
} 