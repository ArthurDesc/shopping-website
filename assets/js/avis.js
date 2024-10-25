document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');

    commentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('<?php echo BASE_URL; ?>ajax/add_comment.php', {
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
        // Créer et retourner un élément DOM pour le nouveau commentaire
        const div = document.createElement('div');
        div.className = 'comment';
        div.innerHTML = `
            <p><strong>${comment.nom_utilisateur}</strong> - Note: ${comment.note}/5</p>
            <p>${comment.commentaire}</p>
            <p class="text-sm text-gray-500">${new Date(comment.date_creation).toLocaleString()}</p>
        `;
        return div;
    }
});
