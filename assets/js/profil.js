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
        let isEditing = false; // Variable pour suivre l'état d'édition

        // Wrapper l'élément éditable dans un conteneur
        const wrapper = document.createElement('div');
        wrapper.className = 'editable-container';
        el.parentNode.insertBefore(wrapper, el);
        wrapper.appendChild(el);

        // Ajout de l'icône d'édition avec SVG
        const icon = document.createElement('div');
        icon.className = 'edit-icon';
        icon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
        `;
        wrapper.appendChild(icon);

        // Gestion du clic pour l'édition
        el.addEventListener('click', function(e) {
            e.preventDefault();
            if (isEditing) return; // Si déjà en édition, ne rien faire
            
            isEditing = true; // Marquer comme en édition
            this.classList.add('active');
            const currentValue = this.textContent.trim();
            
            // Création du champ d'édition
            const input = this.dataset.type === 'textarea' 
                ? document.createElement('textarea')
                : document.createElement('input');
            
            input.value = currentValue;
            input.type = this.dataset.type || 'text';
            input.className = `w-full px-3 py-2 bg-white border border-transparent rounded-lg`;

            // Ajout de l'écouteur pour la touche Entrée
            input.addEventListener('keypress', async (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault(); // Empêcher le saut de ligne par défaut
                    await handleSave(); // Utiliser la même fonction de sauvegarde
                }
            });

            // Gestionnaire de clic extérieur modifié
            const handleClickOutside = (event) => {
                if (!isEditing) return; // Ne rien faire si pas en mode édition
                if (!el.contains(event.target)) {
                    el.innerHTML = currentValue;
                    el.classList.remove('active');
                    isEditing = false; // Marquer comme non éditable
                    document.removeEventListener('click', handleClickOutside);
                }
            };

            // Gestion de la sauvegarde
            const handleSave = async () => {
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
                        isEditing = false; // Marquer comme non éditable
                        document.removeEventListener('click', handleClickOutside);
                        el.innerHTML = newValue || (fieldName === 'telephone' || fieldName === 'adresse' ? 'Non renseigné' : '');
                        el.classList.remove('active');
                        showToast('Modification enregistrée', 'success');
                        
                        if (fieldName === 'nom' || fieldName === 'prenom') {
                            updateHeaderName();
                        }
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    el.innerHTML = currentValue;
                    showToast(error.message || 'Erreur lors de la sauvegarde', 'error');
                }
            };

            // Création des boutons
            const buttonsDiv = document.createElement('div');
            buttonsDiv.className = 'flex justify-end gap-2 mt-2';
            buttonsDiv.innerHTML = `
                <button class="save-btn px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            `;

            // Ajout des gestionnaires d'événements
            buttonsDiv.querySelector('.save-btn').addEventListener('click', handleSave);
            setTimeout(() => {
                document.addEventListener('click', handleClickOutside);
            }, 0);

            // Mise en place de l'interface d'édition
            const container = document.createElement('div');
            container.className = 'flex items-center gap-2';
            container.appendChild(input);
            container.appendChild(buttonsDiv);
            this.innerHTML = '';
            this.appendChild(container);
            input.focus();
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
