document.addEventListener('DOMContentLoaded', () => {
    // Configuration des validations
    const validations = {
        telephone: {
            pattern: /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/,
            message: 'Format invalide (ex: 06 12 34 56 78)'
        },
        email: {
            pattern: /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
            message: 'Format email invalide. Exemple : utilisateur@domaine.com',
            validate: (value) => {
                // Vérification supplémentaire de la longueur
                if (value.length > 254) {
                    return "L'adresse email est trop longue";
                }
                // Vérification basique de la structure
                if (!value.includes('@') || !value.includes('.')) {
                    return "L'adresse email doit contenir @ et un domaine valide";
                }
                // Vérification des espaces
                if (/\s/.test(value)) {
                    return "L'adresse email ne doit pas contenir d'espaces";
                }
                return true;
            }
        },
        nom: {
            pattern: /^[A-Za-zÀ-ÿ\s-]{2,}$/,
            message: 'Le nom doit contenir au moins 2 caractères (lettres, espaces et tirets uniquement)'
        },
        prenom: {
            pattern: /^[A-Za-zÀ-ÿ\s-]{2,}$/,
            message: 'Le prénom doit contenir au moins 2 caractères (lettres, espaces et tirets uniquement)'
        }
    };

    // Initialisation des champs éditables
    document.querySelectorAll('.editable').forEach(el => {
        // Ajout de l'icône d'édition
        const icon = document.createElement('i');
        icon.className = 'fas fa-pencil-alt text-gray-400 absolute right-3 opacity-0 group-hover:opacity-100 transition-opacity';
        el.parentNode.appendChild(icon);

        // Gestion du survol
        el.addEventListener('mouseover', () => {
            icon.style.opacity = '1';
        });
        el.addEventListener('mouseout', () => {
            icon.style.opacity = '0';
        });

        // Gestion du clic pour l'édition
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const currentValue = this.textContent.trim();
            const isTextarea = this.dataset.type === 'textarea';
            
            // Création du champ d'édition
            const input = isTextarea 
                ? document.createElement('textarea')
                : document.createElement('input');
            
            input.value = currentValue;
            input.type = this.dataset.type || 'text';
            input.className = `w-full px-3 py-2 bg-white border border-transparent rounded-lg`;
            
            if (isTextarea) {
                input.rows = 3;
            }

            // Création des boutons de validation/annulation
            const buttonsDiv = document.createElement('div');
            buttonsDiv.className = 'flex gap-2 mt-2';
            buttonsDiv.innerHTML = `
                <button class="save-btn px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                    <i class="fas fa-check"></i>
                </button>
                <button class="cancel-btn px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Remplacement temporaire du contenu
            const originalContent = this.innerHTML;
            this.innerHTML = '';
            this.appendChild(input);
            this.appendChild(buttonsDiv);
            input.focus();

            // Gestion de la sauvegarde
            buttonsDiv.querySelector('.save-btn').addEventListener('click', async () => {
                const newValue = input.value.trim();
                const fieldName = el.dataset.name;
                
                // Validation
                const validation = validations[fieldName];
                if (validation && !validation.pattern.test(newValue)) {
                    showToast(validation.message, 'error');
                    return;
                }

                try {
                    const response = await fetch('../ajax/update_profile.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `name=${fieldName}&value=${encodeURIComponent(newValue)}`
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        el.innerHTML = newValue || (fieldName === 'telephone' || fieldName === 'adresse' ? 'Non renseigné' : '');
                        showToast('Modification enregistrée', 'success');
                        
                        if (fieldName === 'nom' || fieldName === 'prenom') {
                            updateHeaderName();
                        }
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    el.innerHTML = originalContent;
                    showToast(error.message || 'Erreur lors de la sauvegarde', 'error');
                }
            });

            // Gestion de l'annulation
            buttonsDiv.querySelector('.cancel-btn').addEventListener('click', () => {
                el.innerHTML = originalContent;
            });
        });
    });

    // Fonction pour mettre à jour le nom dans le header
    function updateHeaderName() {
        const nom = document.getElementById('nom').textContent.trim();
        const prenom = document.getElementById('prenom').textContent.trim();
        const headerName = document.getElementById('userNameHeader');
        if (headerName) {
            headerName.textContent = `${prenom} ${nom}`;
        }
    }

    // Fonction pour afficher les toasts (à adapter selon votre système de notification)
    function showToast(message, type = 'success') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                text: message,
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        } else {
            alert(message);
        }
    }

    // Gestion du modal de changement de mot de passe
    const modal = document.getElementById('password_modal');
    const toggleBtn = document.getElementById('toggle_password_modal');
    const closeBtn = document.getElementById('cancel_password');
    const changePasswordBtn = document.getElementById('change_password');

    // Fonction pour réinitialiser le formulaire
    function resetPasswordForm() {
        document.getElementById('current_password').value = '';
        document.getElementById('new_password').value = '';
        document.getElementById('confirm_password').value = '';
        modal.classList.add('hidden');
    }
    
    // Ajouter ces gestionnaires d'événements
    closeBtn.addEventListener('click', resetPasswordForm);
    
    // Fermer en cliquant en dehors du modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            resetPasswordForm();
        }
    });

    // Ouvrir le modal
    toggleBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });

    // Gestion du changement de mot de passe
    changePasswordBtn.addEventListener('click', async () => {
        // Vérification de l'existence des éléments
        const currentPasswordInput = document.getElementById('current_password');
        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const errorDiv = document.getElementById('password-error');

        if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput || !errorDiv) {
            return;
        }

        // Récupération des valeurs
        const currentPassword = currentPasswordInput.value.trim();
        const newPassword = newPasswordInput.value.trim();
        const confirmPassword = confirmPasswordInput.value.trim();

        // Réinitialiser le message d'erreur
        errorDiv.classList.add('hidden');
        errorDiv.textContent = '';

        // Validation des champs
        if (!currentPassword || !newPassword || !confirmPassword) {
            errorDiv.textContent = 'Tous les champs sont obligatoires';
            errorDiv.classList.remove('hidden');
            return;
        }

        // Validation de la longueur minimale
        if (newPassword.length < 8) {
            errorDiv.textContent = 'Le nouveau mot de passe doit contenir au moins 8 caractères';
            errorDiv.classList.remove('hidden');
            return;
        }

        // Validation de la correspondance
        if (newPassword !== confirmPassword) {
            errorDiv.textContent = 'Les mots de passe ne correspondent pas';
            errorDiv.classList.remove('hidden');
            return;
        }

        try {
            const response = await fetch('../ajax/update_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `current_password=${encodeURIComponent(currentPassword)}&new_password=${encodeURIComponent(newPassword)}&confirm_password=${encodeURIComponent(confirmPassword)}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                showToast('Mot de passe modifié avec succès', 'success');
                resetPasswordForm();
            } else {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            }
        } catch (error) {
            errorDiv.textContent = error.message || 'Erreur lors du changement de mot de passe';
            errorDiv.classList.remove('hidden');
        }
        
    });
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Retirer la classe active de tous les boutons
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
                btn.classList.add('text-gray-500', 'border-transparent');
            });

            // Ajouter la classe active au bouton cliqué
            button.classList.add('active', 'text-blue-600', 'border-blue-600');
            button.classList.remove('text-gray-500', 'border-transparent');

            // Cacher tous les contenus
            tabPanes.forEach(pane => {
                pane.classList.add('hidden');
            });

            // Afficher le contenu correspondant
            const tabId = button.getAttribute('data-tab');
            document.getElementById(tabId).classList.remove('hidden');
        });
    });
});
