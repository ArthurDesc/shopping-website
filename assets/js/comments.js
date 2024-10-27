import { createCommentElement, addComment } from './commentModule.js';

document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('comment-form');
    const commentsList = document.getElementById('comments-list');

    if (!commentForm || !commentsList) {
        console.error("Le formulaire de commentaire ou la liste des commentaires n'a pas été trouvé.");
        return;
    }

    commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        try {
            const newComment = await addComment(formData);
            const newCommentElement = createCommentElement(newComment);
            commentsList.insertBefore(newCommentElement, commentsList.firstChild);
            commentForm.reset();
        } catch (error) {
        }
    });
});
