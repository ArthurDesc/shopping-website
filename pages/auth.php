<?php
require_once '../includes/session.php';
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
}
// Si l'utilisateur est déjà connecté, redirigez-le vers la page de profil
if (is_logged_in()) {
    header("Location: " . BASE_URL . "pages/profil.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitmode - Authentification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body class="flex flex-col min-h-screen">
    <header class="pt-4"> <!-- Changé de pt-8 à pt-4 -->
        <div class="container mx-auto px-4 flex justify-center">
            <a href="<?php echo BASE_URL; ?>" class="hover:opacity-80 transition-opacity duration-300">
                <img src="<?php echo BASE_URL; ?>assets/images/logoBlanc.png" alt="Fitmode" class="w-32"> <!-- Changé de w-48 à w-32 -->
            </a>
        </div>
    </header>
    
    <main class="flex-grow flex items-center justify-center">
        <div class="container mx-auto px-4 flex flex-col items-center">
            <div class="flex flex-col space-y-4 w-64">
                <a href="<?php echo BASE_URL; ?>pages/connexion.php" class="btn-custom font-semibold py-2 px-6 rounded-full text-center transition duration-300">
                    Se connecter
                </a>
                <a href="<?php echo BASE_URL; ?>pages/inscription.php" class="btn-custom font-semibold py-2 px-6 rounded-full text-center transition duration-300">
                    S'inscrire
                </a>
            </div>
        </div>
    </main>
</body>
</html>
