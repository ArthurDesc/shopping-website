<?php
// Déplacer session_start() et la vérification tout en haut, avant toute sortie
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo "<script>
            window.location.href = 'connexion.php';
          </script>";
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['success_message']; ?></span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg onclick="this.parentElement.parentElement.style.display='none'" class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Fermer</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Reste du contenu de la page profil -->
</div>

<?php require_once '../includes/_footer.php'; ?>
