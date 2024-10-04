<?php
require_once '../includes/session.php';

// Si l'utilisateur est déjà connecté, redirigez-le vers la page de profil
if (is_logged_in()) {
    header("Location: " . BASE_URL . "pages/profil.php");
    exit();
}

include '../includes/_header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-2xl font-bold mb-6">Authentification</h2>
        
        <div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <a href="<?php echo BASE_URL; ?>pages/connexion.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                Connexion
            </a>
            <a href="<?php echo BASE_URL; ?>pages/inscription.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                Inscription
            </a>
        </div>
    </div>
</body>
</html>
