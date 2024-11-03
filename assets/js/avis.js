document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');

    if (!commentForm) {
        console.error("Le formulaire de commentaire n'a pas été trouvé");
        return;
    }

    commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            // Debug des données envoyées
            console.log('Données envoyées:', Object.fromEntries(formData));

            const response = await fetch('/shopping-website/ajax/add_comment.php', {
                method: 'POST',
                body: formData
            });

            // Debug de la réponse
            console.log('Status:', response.status);
            console.log('Headers:', Object.fromEntries(response.headers));

            if (!response.ok) {
                const text = await response.text();
                console.error('Réponse serveur:', text);
                throw new Error(`Erreur serveur: ${response.status}`);
            }

            const data = await response.json();
            console.log('Données reçues:', data);

            if (data.success) {
                const newComment = createCommentElement(data.comment);
                if (newComment) {
                    commentsList.insertBefore(newComment, commentsList.firstChild);
                    commentForm.reset();
                    // Utilisation du nouveau toast
                    showBottomToast('Votre avis a été ajouté avec succès', 'success');
                }
            } else {
                showBottomToast(data.message || 'Erreur lors de l\'ajout du commentaire', 'error');
            }
        } catch (error) {
            console.error('Erreur détaillée:', error);
            showBottomToast('Une erreur s\'est produite lors de l\'ajout du commentaire', 'error');
        }
    });

    function createCommentElement(comment) {
        // Vérification que comment existe
        if (!comment) {
            console.error('Données de commentaire manquantes');
            return null;
        }

        const div = document.createElement('div');
        div.className = 'mb-6 p-6 border rounded-xl shadow-lg bg-white';
        div.setAttribute('data-avis-id', comment.id_avis); // Ajout de l'attribut data-avis-id
        
        // Récupérer l'ID de l'utilisateur connecté depuis le formulaire
        const currentUserId = document.querySelector('input[name="id_utilisateur"]').value;
        
        // Utilisation de l'opérateur ?? pour fournir des valeurs par défaut
        const nomUtilisateur = comment.nom_utilisateur ?? 'Anonyme';
        const note = comment.note ?? 0;
        const commentaire = comment.commentaire ?? 'Aucun commentaire';
        const dateCreation = comment.date_creation ? formatDate(comment.date_creation) : 'Date non disponible';

        // Création des boutons d'action si l'utilisateur est l'auteur
        const actionButtons = currentUserId == comment.id_utilisateur ? `
            <button onclick="modifierAvis(${comment.id_avis})" class="text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </button>
            <button onclick="supprimerAvis(${comment.id_avis})" class="text-red-600 hover:text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        ` : '';

        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">${nomUtilisateur}</span>
                    <div class="flex items-center">
                        ${createStarRating(note)}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">${dateCreation}</span>
                    ${actionButtons}
                </div>
            </div>
            <p class="text-gray-700 mt-2 commentaire-texte">${commentaire}</p>
        `;

        return div;
    }

    function createStarRating(note, isEditable = false) {
        let html = '<div class="flex items-center star-rating" data-note="' + note + '">';
        for (let i = 1; i <= 5; i++) {
            if (isEditable) {
                html += `
                    <input type="radio" id="star${i}" name="rating" value="${i}" ${i === note ? 'checked' : ''} class="hidden">
                    <label for="star${i}" class="cursor-pointer">
                        <svg class="w-5 h-5 ${i <= note ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </label>
                `;
            } else {
                html += `
                    <svg class="w-5 h-5 ${i <= note ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                `;
            }
        }
        html += '</div>';
        return html;
    }

    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                return 'Date non disponible';
            }
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

    // Fonction pour modifier un avis
    window.modifierAvis = function(idAvis) {
        const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
        const commentaireElement = avisElement.querySelector('p');
        const currentValue = commentaireElement.textContent.trim();
        const noteActuelle = parseInt(avisElement.querySelector('.star-rating').getAttribute('data-note'));
        
        // Création du champ d'édition
        const container = document.createElement('div');
        container.className = 'flex flex-col gap-2';
        
        // Textarea pour le commentaire
        const textarea = document.createElement('textarea');
        textarea.value = currentValue;
        textarea.className = 'w-full px-3 py-2 bg-white border border-transparent rounded-lg';
        
        // Création du système de notation
        const ratingDiv = document.createElement('div');
        ratingDiv.className = 'flex gap-2';

        // Inverser l'ordre des étoiles (de 5 à 1 au lieu de 1 à 5)
        for (let i = 5; i >= 1; i--) {
            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `rating_${idAvis}`;
            input.value = i;
            input.checked = i === noteActuelle;
            input.className = 'hidden peer';
            input.id = `star${i}_${idAvis}`;
            
            const label = document.createElement('label');
            label.htmlFor = `star${i}_${idAvis}`;
            label.innerHTML = `<svg class="w-6 h-6 ${i <= noteActuelle ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>`;
            
            ratingDiv.appendChild(input);
            ratingDiv.appendChild(label);
        }
        
        // Boutons d'action
        const buttonsDiv = document.createElement('div');
        buttonsDiv.className = 'flex justify-end gap-2 mt-2';
        buttonsDiv.innerHTML = `
            <button class="save-btn px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </button>
        `;
        
        container.appendChild(textarea);
        container.appendChild(ratingDiv);
        container.appendChild(buttonsDiv);
        
        // Cacher le commentaire original et afficher le formulaire
        commentaireElement.style.display = 'none';
        commentaireElement.after(container);
        
        // Gestionnaire de sauvegarde
        buttonsDiv.querySelector('.save-btn').addEventListener('click', async () => {
            const newCommentaire = textarea.value.trim();
            const newNote = container.querySelector('input[name^="rating"]:checked').value;
            
            try {
                const response = await fetch('/shopping-website/ajax/update_avis.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_avis: idAvis,
                        commentaire: newCommentaire,
                        note: newNote
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    commentaireElement.textContent = newCommentaire;
                    commentaireElement.style.display = 'block';
                    container.remove();
                    showBottomToast('Avis modifié avec succès', 'success');
                    // Mettre à jour l'affichage des étoiles
                    updateStarDisplay(avisElement, newNote);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showBottomToast(error.message || 'Erreur lors de la modification', 'error');
            }
        });
    };

    function updateStarDisplay(avisElement, newNote) {
        const stars = avisElement.querySelectorAll('.star-rating svg');
        stars.forEach((star, index) => {
            if (index < newNote) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        avisElement.querySelector('.star-rating').setAttribute('data-note', newNote);
    }

    // Fonction pour supprimer un avis
    window.supprimerAvis = function(idAvis) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
            fetch('/shopping-website/ajax/delete_avis.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id_avis: idAvis })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-avis-id="${idAvis}"]`).remove();
                } else {
                    alert('Erreur lors de la suppression : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la suppression');
            });
        }
    };

    window.sauvegarderModification = async function(idAvis) {
        const commentElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
        const newCommentaire = commentElement.querySelector('textarea').value.trim();
        const checkedStar = commentElement.querySelector('input[name="rating"]:checked');
        
        // Sauvegarder les informations existantes
        const nomUtilisateur = commentElement.querySelector('.font-semibold').textContent;
        
        // Validations
        if (newCommentaire.length < 10) {
            alert('Le commentaire doit faire au moins 10 caractères');
            return;
        }
        
        if (!checkedStar) {
            alert('Veuillez sélectionner une note');
            return;
        }
        
        const newNote = parseInt(checkedStar.value);

        try {
            const saveButton = commentElement.querySelector('button[onclick^="sauvegarderModification"]');
            const originalText = saveButton.textContent;
            saveButton.textContent = 'Sauvegarde...';
            saveButton.disabled = true;

            const response = await fetch('/shopping-website/ajax/update_avis.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_avis: idAvis,
                    commentaire: newCommentaire,
                    note: newNote
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Utiliser la date de modification renvoyée par le serveur
                const dateModification = data.date_modification ? formatDate(data.date_modification) : formatDate(new Date());
                
                commentElement.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center">
                            <span class="font-semibold mr-2">${nomUtilisateur}</span>
                            <div class="flex items-center">
                                ${createStarRating(newNote)}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">Modifié le ${dateModification}</span>
                            <button onclick="modifierAvis(${idAvis})" class="text-blue-600 hover:text-blue-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <button onclick="supprimerAvis(${idAvis})" class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-700 mt-2 commentaire-texte">${newCommentaire}</p>
                `;
                
                // Notification de succès
                const notification = document.createElement('div');
                notification.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-2 notification';
                notification.textContent = 'Modification enregistrée avec succès';
                commentElement.appendChild(notification);
                
                setTimeout(() => {
                    const notif = commentElement.querySelector('.notification');
                    if (notif) notif.remove();
                }, 3000);
            } else {
                throw new Error(data.message || 'Erreur lors de la modification');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de la modification : ' + error.message);
            
            if (saveButton) {
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            }
        }
    };

    window.annulerModification = function(idAvis) {
        const commentElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
        if (!commentElement) return;

        const commentaireElement = commentElement.querySelector('.commentaire-texte');
        if (commentaireElement) {
            commentaireElement.style.display = 'block';
        }

        // Supprimer le formulaire de modification
        const form = commentElement.querySelector('div.mt-4.space-y-4').parentNode;
        if (form) {
            form.remove();
        }
    };
});

async function getAvis(productId) {
    try {
        const response = await fetch(`/shopping-website/ajax/get_avis.php?id_produit=${productId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("La réponse n'est pas au format JSON!");
        }
        return await response.json();
    } catch (error) {
        console.error('Erreur lors de la récupération des avis:', error);
        return [];
    }
}

// Modification de la fonction d'affichage
function displayAvis(avis) {
    const avisList = document.getElementById('comments-list');
    if (!avisList) {
        console.error("La liste des commentaires n'a pas été trouvée");
        return;
    }
    
    avisList.innerHTML = '';
    if (Array.isArray(avis)) {
        avis.forEach(avis => {
            try {
                const avisElement = createAvisElement(avis);
                if (avisElement) {
                    avisList.appendChild(avisElement);
                }
            } catch (error) {
                console.error('Erreur lors de la création d\'un élément avis:', error);
            }
        });
    } else {
        console.error('Les avis reçus ne sont pas dans un format valide');
    }
}

function showBottomToast(message, type = "success") {
    // Créer l'élément toast
    const toast = document.createElement("div");
    toast.className = `fixed bottom-5 right-5 p-4 rounded-md text-white ${
        type === "success" ? "bg-green-500" : "bg-red-500"
    } shadow-lg transition-opacity duration-500 ease-in-out opacity-0`;
    toast.style.zIndex = "1000";
    toast.innerHTML = message;
  
    // Ajouter le toast au body
    document.body.appendChild(toast);
  
    // Faire apparaître le toast
    requestAnimationFrame(() => {
        toast.style.opacity = "1";
    });
  
    // Faire disparaître le toast après 3 secondes
    setTimeout(() => {
        toast.style.opacity = "0";
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 500);
    }, 3000);
}
