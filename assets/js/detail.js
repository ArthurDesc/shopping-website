document.addEventListener('DOMContentLoaded', function() {
    // Fonction pour gérer le formulaire de commentaires
    function handleCommentForm() {
        const starRating = document.getElementById('comment-form');
        if (!starRating) {
            console.warn("Le formulaire de commentaire n'a pas été trouvé.");
            return;
        }
        const stars = starRating.querySelectorAll('input[name="note"]');
        const noteInput = document.createElement('input');
        noteInput.type = 'hidden';
        noteInput.name = 'note';
        starRating.appendChild(noteInput);

        stars.forEach(star => {
            star.addEventListener('change', function() {
                const rating = this.value;
                noteInput.value = rating;
                highlightStars(rating);
            });

            star.nextElementSibling.addEventListener('mouseover', function() {
                const rating = this.previousElementSibling.value;
                highlightStars(rating);
            });

            star.nextElementSibling.addEventListener('mouseout', function() {
                const currentRating = noteInput.value || 0;
                highlightStars(currentRating);
            });
        });

        function highlightStars(rating) {
            stars.forEach(star => {
                const starLabel = star.nextElementSibling;
                const starIcon = starLabel.querySelector('svg');
                if (star.value <= rating) {
                    starIcon.classList.remove('text-gray-300');
                    starIcon.classList.add('text-yellow-400');
                } else {
                    starIcon.classList.remove('text-yellow-400');
                    starIcon.classList.add('text-gray-300');
                }
            });
        }

        starRating.addEventListener('submit', function(e) {
            if (!noteInput.value) {
                e.preventDefault();
                alert('Veuillez sélectionner une note avant de soumettre votre avis.');
            }
        });
    }

    // Fonction pour gérer le bouton d'ajout au panier
    function handleAddToCartSubmit() {
        const form = document.getElementById('add-to-cart-form');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const tailleError = document.getElementById('taille-error');
        
        if (!form || !addToCartBtn) {
            console.warn("Le formulaire ou le bouton d'ajout au panier n'a pas été trouvé.");
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const selectedSize = form.querySelector('input[name="taille"]:checked')?.value;
            if (!selectedSize) {
                tailleError.classList.remove('hidden');
                return;
            }

            const formData = new FormData(this);
            
            fetch('/shopping-website/ajax/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cartCount);
                    if (typeof showCartToast === 'function') {
                        showCartToast('Article ajouté au panier', 'success');
                    } else if (typeof showToast === 'function') {
                        showToast('Article ajouté au panier', 'success');
                    } else {
                        console.warn('Aucune fonction toast disponible');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast(data.message || 'Erreur lors de l\'ajout au panier', 'error');
                    } else {
                        console.warn('Fonction showToast non disponible');
                        alert(data.message || 'Erreur lors de l\'ajout au panier');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                if (typeof showToast === 'function') {
                    showToast('Une erreur s\'est produite', 'error');
                } else {
                    alert('Une erreur s\'est produite');
                }
            });
        });

        // Ajouter un écouteur pour cacher le message d'erreur quand une taille est sélectionnée
        const tailleInputs = form.querySelectorAll('input[name="taille"]');
        tailleInputs.forEach(input => {
            input.addEventListener('change', () => {
                tailleError.classList.add('hidden');
            });
        });
    }

    // Fonction pour gérer les onglets
    function handleTabs() {
        const tabs = document.querySelectorAll('.tab');
        const indicator = document.querySelector('.indicator');
        const tabContents = document.querySelectorAll('.tab-pane');
        if (!tabs.length || !indicator || !tabContents.length) {
            console.warn("Les éléments nécessaires pour les onglets n'ont pas été trouvés.");
            return;
        }

        function positionIndicator() {
            const activeTab = document.querySelector('.tab:checked');
            if (activeTab) {
                const label = activeTab.nextElementSibling;
                indicator.style.width = `${label.offsetWidth}px`;
                indicator.style.left = `${label.offsetLeft}px`;
            }
        }

        function showTabContent(tabId) {
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            const activeContent = document.getElementById(`${tabId}-content`);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('change', function() {
                positionIndicator();
                showTabContent(this.id);
            });
        });

        // Position initiale de l'indicateur
        positionIndicator();

        // Vérifier si on revient d'une autre page avec un onglet spécifique
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab) {
            const tab = document.getElementById(activeTab);
            if (tab) {
                tab.checked = true;
                positionIndicator();
                showTabContent(activeTab);
            }
        }

        // Repositionner l'indicateur lors du redimensionnement de la fenêtre
        window.addEventListener('resize', positionIndicator);
    }

    // Fonction pour charger les avis
    async function loadAvis(productId) {
        try {
            const response = await fetch(`/shopping-website/ajax/get_avis.php?id_produit=${productId}`);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Erreur lors du chargement des avis:', error);
            return null;
        }
    }

    // Fonction pour gérer l'affichage des avis
    function handleAvis() {
        const productIdElement = document.getElementById('id_produit');
        if (!productIdElement) {
            console.warn("ID du produit non trouvé");
            return;
        }

        const productId = productIdElement.value;
        loadAvis(productId).then(data => {
            if (data && data.success) {
                displayAvis(data.avis);
            } else {
                console.error('Erreur lors du chargement des avis');
            }
        });
    }

    // Appel de toutes les fonctions
    handleCommentForm();
    handleAddToCartSubmit();
    handleTabs();
    handleAvis();
});

function displayAvis(avis) {
    const avisList = document.getElementById('comments-list');
    if (!avisList) {
        console.error("La liste des commentaires n'a pas été trouvée");
        return;
    }

    // Vider la liste actuelle
    avisList.innerHTML = '';

    // Trier les avis par date (du plus récent au plus ancien)
    const sortedAvis = avis.sort((a, b) => new Date(b.date_creation) - new Date(a.date_creation));

    // Ajouter les avis triés
    sortedAvis.forEach(avis => {
        const avisElement = createAvisElement(avis);
        avisList.appendChild(avisElement);
    });
}

function createAvisElement(avis) {
    const div = document.createElement('div');
    div.className = 'mb-6 p-6 border rounded-xl shadow-lg bg-white';
    div.setAttribute('data-avis-id', avis.id_avis);
    
    // Vérification des valeurs avant utilisation
    const nomUtilisateur = avis.nom_utilisateur || 'Anonyme';
    const dateCreation = avis.date_creation ? formatDate(avis.date_creation) : 'Date non disponible';
    const note = parseInt(avis.note) || 0;
    const commentaire = avis.commentaire || 'Aucun commentaire';
    const isAuthor = avis.id_utilisateur === getCurrentUserId();

    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center">
                <span class="font-semibold mr-2">${nomUtilisateur}</span>
                <div class="flex items-center star-rating" data-note="${note}">
                    ${createStarRating(note)}
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">${dateCreation}</span>
                ${isAuthor ? `
                    <div class="flex gap-2">
                        <button onclick="modifierAvis(${avis.id_avis})" class="text-blue-600 hover:text-blue-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                        <button onclick="supprimerAvis(${avis.id_avis})" class="text-red-600 hover:text-red-800">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                ` : ''}
            </div>
        </div>
        <p class="text-gray-700 mt-2">${commentaire}</p>
    `;
    return div;
}

function formatDate(dateString) {
    try {
        const date = new Date(dateString);
        // Vérifier si la date est valide
        if (isNaN(date.getTime())) {
            return 'Date non disponible';
        }
        // Format : DD/MM/YYYY HH:mm
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        console.error('Erreur de formatage de date:', e);
        return 'Date non disponible';
    }
}

function createStarRating(note) {
    const fullStar = '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
    const emptyStar = '<svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
    
    return Array(5).fill('').map((_, index) => index < note ? fullStar : emptyStar).join('');
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.classList.toggle('bg-red-600', count === 0);
        cartCountElement.classList.toggle('bg-green-600', count > 0);
    }
}


// Ajoutez après la fonction handleTabs

function initializeEditMode() {
    const editCategoriesContainer = document.getElementById('edit-categories-container');
    if (!editCategoriesContainer) return;

    // Initialiser le sélecteur de catégories
    const categorySelector = new CategorySelector('edit-categories-container');
    categorySelector.init();

    // Pré-sélectionner les catégories existantes
    const existingCategories = JSON.parse(editCategoriesContainer.dataset.categories || '[]');
    categorySelector.setSelectedCategories(existingCategories);

    // Ajouter la gestion des catégories au formulaire d'édition
    const editForm = document.querySelector('#edit-form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Ajouter les catégories sélectionnées
            const selectedCategories = categorySelector.getSelectedCategories();
            formData.append('categories', JSON.stringify(selectedCategories));

            // Envoyer les données
            fetch('/shopping-website/admin/update_article.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Article mis à jour avec succès', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(data.message || 'Erreur lors de la mise à jour', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showToast('Erreur lors de la mise à jour', 'error');
            });
        });
    }
}

// Ajouter à votre event listener existant
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('edit-categories-container')) {
        initializeEditMode();
    }
});

function getCurrentUserId() {
    const userIdElement = document.getElementById('current_user_id');
    return userIdElement ? parseInt(userIdElement.value) : null;
}

