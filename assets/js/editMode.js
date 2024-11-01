// ... autres fonctions existantes ...

// Fonction pour mettre à jour un champ générique
function updateField(field, newValue) {
    fetch('/shopping-website/admin/update_article.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_produit: document.getElementById('id_produit').value,
            field: field,
            new_value: newValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(field + ' mis à jour avec succès', 'success');
        } else {
            console.error('Erreur lors de la mise à jour de ' + field + ':', data.message);
            showToast('Erreur lors de la mise à jour de ' + field + ': ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Une erreur s\'est produite lors de la mise à jour de ' + field, 'error');
    });
}

// Fonctions spécifiques pour chaque champ
function updateTitle(newTitle) {
    if (newTitle.trim().length < 3) {
        showToast('Le titre doit contenir au moins 3 caractères', 'error');
        return;
    }
    updateField('nom', newTitle);
}

function updateDescription(newDescription) {
    if (newDescription.trim().length < 10) {
        showToast('La description doit contenir au moins 10 caractères', 'error');
        return;
    }
    updateField('description', newDescription);
}

function updatePrice(newPrice) {
    const price = parseFloat(newPrice);
    if (isNaN(price) || price <= 0) {
        showToast('Veuillez entrer un prix valide', 'error');
        return;
    }
    updateField('prix', price);
}

function updateBrand(newBrand) {
    if (newBrand.trim().length < 2) {
        showToast('La marque doit contenir au moins 2 caractères', 'error');
        return;
    }
    updateField('marque', newBrand);
}

function updateCollection(newCollection) {
    const validCollections = ['Homme', 'Femme', 'Enfant'];
    if (!validCollections.includes(newCollection)) {
        showToast('Collection invalide. Choisissez parmi : Homme, Femme, Enfant', 'error');
        return;
    }
    updateField('collection', newCollection);
}

function updateImage(file) {
    if (!file) return;

    // Vérification du type de fichier
    if (!file.type.startsWith('image/')) {
        showToast('Veuillez sélectionner une image valide', 'error');
        return;
    }

    // Vérification de la taille (5MB max)
    if (file.size > 5 * 1024 * 1024) {
        showToast('L\'image ne doit pas dépasser 5MB', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('image', file);
    formData.append('id_produit', document.getElementById('id_produit').value);
    formData.append('action', 'update_image');

    // Afficher un indicateur de chargement
    const imgContainer = document.querySelector('.image-container');
    imgContainer.style.opacity = '0.5';

    fetch('/shopping-website/admin/update_article.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mettre à jour l'image affichée
            const imgElement = document.querySelector('.image-container img');
            imgElement.src = data.new_image_url + '?t=' + new Date().getTime(); // Ajout timestamp pour éviter le cache
            showToast('Image mise à jour avec succès', 'success');
        } else {
            showToast('Erreur lors de la mise à jour de l\'image: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('Une erreur s\'est produite lors de la mise à jour de l\'image', 'error');
    })
    .finally(() => {
        // Restaurer l'opacité de l'image
        imgContainer.style.opacity = '1';
    });
}

// Fonction utilitaire pour afficher les notifications



// Initialisation des événements au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes en mode édition
    const isEditMode = document.querySelector('body').contains(
        document.querySelector('#image-upload')
    );

    if (!isEditMode) return;

    // Initialiser les écouteurs d'événements si nécessaire
    console.log('Mode édition activé');
});

// Ajouter aux fonctions existantes
function updateStock(newStock) {
    // Vérification que le stock est un nombre positif
    const stockValue = parseInt(newStock);
    if (isNaN(stockValue) || stockValue < 0) {
        showToast('Le stock doit être un nombre positif', 'error');
        return;
    }
    
    updateField('stock', stockValue);
}
