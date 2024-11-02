// Fonction unique pour gérer l'ajout au panier
function handleAddToCart(event, options = {}) {
    event.preventDefault();
    event.stopPropagation();

    const {
        productId = null,
        size = null,
        quantity = 1,
        button = null,
        onSuccess = null,
        form = null
    } = options;

    // Vérification de la taille
    const selectedSize = size || (form?.querySelector('input[name="taille"]:checked')?.value);
    if (!selectedSize) {
        showToast('Veuillez sélectionner une taille', 'error');
        return;
    }

    // Désactiver le bouton pendant la requête
    if (button) button.disabled = true;

    const formData = new FormData();
    formData.append('id_produit', productId);
    formData.append('taille', selectedSize);
    formData.append('quantite', quantity);

    fetch('/shopping-website/ajax/add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount(data.cartCount);
            showCartToast('Article ajouté au panier', 'success');
            if (onSuccess) onSuccess(data);
        } else {
            showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Une erreur s\'est produite', 'error');
    })
    .finally(() => {
        if (button) button.disabled = false;
    });
}
