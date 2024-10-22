<?php
session_start();
require_once '../includes/_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_avis'])) {
    $id_avis = intval($_POST['id_avis']);
    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Vérifier que l'utilisateur est bien l'auteur de l'avis
    $sql = "DELETE FROM avis WHERE id_avis = ? AND id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_avis, $id_utilisateur);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Votre avis a été supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de la suppression de votre avis.";
    }

    $stmt->close();
}

// Rediriger vers la page des avis
header("Location: avis.php?id_produit=" . $_GET['id_produit']);
exit();
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteForms = document.querySelectorAll('form[action="supprimer_avis.php"]');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
                e.preventDefault();
            }
        });
    });
});
</script>