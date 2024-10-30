document.addEventListener('DOMContentLoaded', function() {
    // Récupération des éléments du formulaire
    const profileForm = document.getElementById('profileForm');
    const inputs = {
        telephone: document.getElementById('telephone'),
        email: document.getElementById('email'),
        nom: document.getElementById('nom'),
        prenom: document.getElementById('prenom')
    };

    // Regex de validation
    const validationRules = {
        telephone: {
            regex: /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/,
            message: 'Format invalide (ex: 06 12 34 56 78)'
        },
        email: {
            regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Format email invalide'
        },
        nom: {
            regex: /^[A-Za-zÀ-ÿ\s-]{2,}$/,
            message: 'Le nom doit contenir au moins 2 caractères (lettres, espaces et tirets uniquement)'
        },
        prenom: {
            regex: /^[A-Za-zÀ-ÿ\s-]{2,}$/,
            message: 'Le prénom doit contenir au moins 2 caractères (lettres, espaces et tirets uniquement)'
        }
    };

    // Fonction de validation en temps réel
    function setupValidation(inputElement, validationRule) {
        inputElement.addEventListener('input', function(e) {
            if (this.value && !validationRule.regex.test(this.value)) {
                this.setCustomValidity(validationRule.message);
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('invalid');
                this.classList.add('valid');
            }
        });
    }

    // Application des validations
    Object.keys(inputs).forEach(key => {
        if (inputs[key] && validationRules[key]) {
            setupValidation(inputs[key], validationRules[key]);
        }
    });

    // Gestion de la soumission du formulaire
    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Vérification de la validité du formulaire
        if (!this.checkValidity()) {
            // Afficher les messages d'erreur natifs du navigateur
            return;
        }

        try {
            // Afficher le loader
            Swal.fire({
                title: 'Mise à jour en cours...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData(this);
            const response = await axios.post('../ajax/update_profile.php', formData);

            if (response.data.success) {
                // Mise à jour du token CSRF
                document.querySelector('input[name="csrf_token"]').value = response.data.newCsrfToken;

                // Mise à jour des informations dans le header si nécessaire
                if (document.getElementById('userNameHeader')) {
                    document.getElementById('userNameHeader').textContent = 
                        `${response.data.user.prenom} ${response.data.user.nom}`;
                }

                // Message de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Succès!',
                    text: response.data.message,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Mise à jour visuelle des champs
                Object.keys(response.data.user).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.value = response.data.user[key];
                        input.classList.add('valid');
                    }
                });

            } else {
                throw new Error(response.data.message);
            }

        } catch (error) {
            // Gestion des erreurs
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: error.message || 'Une erreur est survenue lors de la mise à jour',
                confirmButtonText: 'OK'
            });

            // Log de l'erreur pour le debugging
            console.error('Erreur lors de la mise à jour du profil:', error);
        }
    });

    // Réinitialisation des styles de validation lors du focus
    Object.values(inputs).forEach(input => {
        if (input) {
            input.addEventListener('focus', function() {
                this.classList.remove('invalid', 'valid');
            });
        }
    });

    // Ajout de classes CSS pour le feedback visuel
    const style = document.createElement('style');
    style.textContent = `
        input.valid {
            border-color: #22c55e !important;
        }
        input.invalid {
            border-color: #ef4444 !important;
        }
    `;
    document.head.appendChild(style);
});
