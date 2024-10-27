document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');

    if (!commentForm || !commentsList) {
        console.error("Le formulaire de commentaire ou la liste des commentaires n'a pas été trouvé.");
        return; // Arrêter l'exécution si les éléments nécessaires n'existent pas
    }

    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`${BASE_URL}ajax/add_comment.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Ajouter le nouveau commentaire à la liste
                const newComment = createCommentElement(data.comment);
                commentsList.insertBefore(newComment, commentsList.firstChild);
                commentForm.reset();
            } else {
                alert('Erreur lors de l\'ajout du commentaire : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur s\'est produite lors de l\'ajout du commentaire');
        });
    });

    function createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment mb-6 p-6 border rounded-xl shadow-lg bg-white';
        div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">${comment.nom_utilisateur}</span>
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

    function createStarRating(note) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += `<svg class="w-4 h-4 ${i <= note ? 'text-yellow-300' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>`;
        }
        return stars;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString();
    }
});
