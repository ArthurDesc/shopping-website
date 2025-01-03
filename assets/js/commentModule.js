// Fonction pour créer l'élément HTML d'un commentaire
function createCommentElement(comment) {
    const div = document.createElement('div');
    div.className = 'comment mb-6 p-6 border rounded-xl shadow-lg bg-white';
    div.innerHTML = `
        <div class="flex justify-between items-center mb-2">
            <div class="flex items-center">
                <span class="font-semibold mr-2">${comment.nom_utilisateur || 'Anonyme'}</span>
                <div class="flex items-center">
                    ${createStarRating(comment.note)}
                </div>
            </div>
            <span class="text-sm text-gray-500">${formatDate(comment.date_creation)}</span>
        </div>
        <p class="text-gray-700 mt-2">${comment.commentaire}</p>
    `;
    return div;
}

// Fonction pour créer le rating en étoiles
function createStarRating(note) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += `<svg class="w-4 h-4 ${i <= note ? 'text-yellow-300' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
    }
    return stars;
}

// Fonction pour formater la date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString();
}

// Fonction pour ajouter un commentaire
async function addComment(formData) {
    try {
        const response = await fetch(`${BASE_URL}ajax/add_comment.php`, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if (data.success) {
            return data.comment;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Erreur:', error);
        throw error;
    }
}

// Exporter les fonctions que nous voulons rendre disponibles
export { createCommentElement, addComment };

