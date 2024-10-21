document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('star-rating');
    const stars = starRating.querySelectorAll('.star-icon');
    const noteInput = document.getElementById('note-input');
    const form = document.getElementById('avis-form');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            noteInput.value = rating;
            highlightStars(rating);
        });

        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            highlightStars(rating);
        });

        star.addEventListener('mouseout', function() {
            const currentRating = noteInput.value || 0;
            highlightStars(currentRating);
        });
    });

    function highlightStars(rating) {
        stars.forEach(star => {
            const starRating = star.getAttribute('data-rating');
            if (starRating <= rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    form.addEventListener('submit', function(e) {
        if (!noteInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une note avant de soumettre votre avis.');
        }
    });
});

    document.addEventListener('DOMContentLoaded', function() {
        const addToCartButton = document.querySelector('button[name="ajouter_au_panier"]');

        addToCartButton.addEventListener('click', function(event) {
            event.preventDefault();
            console.log('Bouton cliqué');

            const form = document.getElementById('product-form');
            const formData = new FormData(form);

            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('ajouter_panier.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Réponse du serveur:', data);
                if (data.success) {
                    alert('Produit ajouté au panier avec succès!');
                } else {
                    alert('Erreur lors de l\'ajout du produit au panier: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur s\'est produite lors de l\'ajout au panier.');
            });
        });
    });