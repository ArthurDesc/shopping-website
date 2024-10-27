document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('comment-form');
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
});

document.addEventListener('DOMContentLoaded', function() {
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('Bouton cliqué');

            const form = document.getElementById('product-form');
            if (form) {
                const formData = new FormData(form);

                fetch('ajouter_panier.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Réponse du serveur:', data);
                    if (data.success) {
                        alert('Produit ajouté au panier avec succès !');
                    } else {
                        alert('Erreur lors de l\'ajout du produit au panier: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur s\'est produite lors de l\'ajout au panier.');
                });
            } else {
                console.error('Le formulaire product-form n\'existe pas');
            }
        });
    } else {
        console.error("Le bouton 'Ajouter au panier' n'a pas été trouvé.");
    }
});

document.querySelectorAll('.image-container').forEach(container => {
    const card = container.querySelector('.card');

    container.addEventListener('mousemove', (e) => {
        const rect = container.getBoundingClientRect();
        const xPos = (rect.width / 2 - (e.clientX - rect.left)) / 30; // Réduit l'effet
        const yPos = (rect.height / 2 - (e.clientY - rect.top)) / 30; // Réduit l'effet

        card.style.transform = `rotateX(${yPos}deg) rotateY(${xPos}deg)`;
    });

    container.addEventListener('mouseenter', () => {
        card.style.transition = "none";
    });

    container.addEventListener('mouseleave', () => {
        card.style.transition = "transform 0.3s";
        card.style.transform = "none";
    });
});



document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('add-to-cart-form');
    const addToCartBtn = document.getElementById('add-to-cart-btn');
    let isSubmitting = false;

    if (form && addToCartBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (isSubmitting) return;

            isSubmitting = true;
            addToCartBtn.disabled = true;
            addToCartBtn.querySelector('.add-to-cart-text').textContent = 'Ajout en cours...';

            const formData = new FormData(form);

            fetch(BASE_URL + 'ajax/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cartCount);
                    showToast('Article ajouté au panier');
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur s\'est produite lors de l\'ajout au panier.');
            })
            .finally(() => {
                isSubmitting = false;
                addToCartBtn.disabled = false;
                addToCartBtn.querySelector('.add-to-cart-text').textContent = 'Ajouter au panier';
            });
        });
    } else {
        console.error('Le formulaire ou le bouton d\'ajout au panier n\'a pas été trouvé.');
    }

    function updateCartCount(count) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
            
            if (count > 0) {
                cartCountElement.classList.remove('bg-red-600');
                cartCountElement.classList.add('bg-green-600');
            } else {
                cartCountElement.classList.remove('bg-green-600');
                cartCountElement.classList.add('bg-red-600');
            }
        }
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab');
    const indicator = document.querySelector('.indicator');
    const tabContents = document.querySelectorAll('.tab-pane');

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
            // Ajout du défilement automatique
            activeContent.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
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

    // Repositionner l'indicateur lors du redimensionnement de la fenêtre
    window.addEventListener('resize', positionIndicator);
});

document.addEventListener('DOMContentLoaded', function() {
    const avisForm = document.getElementById('avis-form');
    const avisList = document.getElementById('avis-list');
    const idProduitElement = document.getElementById('id_produit');

    if (idProduitElement) {
        const ID_PRODUIT = idProduitElement.value;
        
        // Le reste de votre code ici
        loadAvis();
        
        // ...
    } else {
        console.error("L'élément avec l'ID 'id_produit' n'a pas été trouvé.");
    }

    // ...
});
