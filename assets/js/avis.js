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
                }
            } else {
                alert(data.message || 'Erreur lors de l\'ajout du commentaire');
            }
        } catch (error) {
            console.error('Erreur détaillée:', error);
            alert('Une erreur s\'est produite lors de l\'ajout du commentaire');
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
        
        // Utilisation de l'opérateur ?? pour fournir des valeurs par défaut
        const nomUtilisateur = comment.nom_utilisateur ?? 'Anonyme';
        const note = comment.note ?? 0;
        const commentaire = comment.commentaire ?? 'Aucun commentaire';
        const dateCreation = comment.date_creation ? formatDate(comment.date_creation) : 'Date non disponible';

        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">${nomUtilisateur}</span>
                    <div class="flex items-center">
                        ${createStarRating(note)}
                    </div>
                </div>
                <span class="text-sm text-gray-500">${dateCreation}</span>
            </div>
            <p class="text-gray-700 mt-2">${commentaire}</p>
        `;
        return div;
    }

    function createStarRating(note) {
        const fullStar = '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
        const emptyStar = '<svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
        
        return Array(5).fill('').map((_, index) => index < note ? fullStar : emptyStar).join('');
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
