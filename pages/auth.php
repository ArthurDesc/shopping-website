<?php
require_once '../functions/url.php';
require_once '../includes/session.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

// Si l'utilisateur est déjà connecté, redirigez-le vers la page de profil
if (is_logged_in()) {
    header("Location: " . url('pages/profil.php'));
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
    <link rel="stylesheet" href="<?php echo url('assets/css/auth.css'); ?>">
</head>
<body class="flex flex-col min-h-screen">
    <header class="pt-4">
        <div class="container mx-auto px-4 flex justify-center">
            <a href="<?php echo url(''); ?>" class="hover:opacity-80 transition-opacity duration-300">
                <img src="<?php echo url('assets/images/LogoBlanc.png'); ?>" alt="Fitmode" class="w-32">
            </a>
        </div>
    </header>
    
    <main class="flex-grow flex items-center justify-center">
        <div class="container mx-auto px-4 flex flex-col items-center">
            <div class="flex flex-col space-y-4 w-64">
                <a href="<?php echo url('pages/connexion.php'); ?>" class="btn-custom font-semibold py-2 px-6 rounded-full text-center transition duration-300">
                    Se connecter
                </a>
                <a href="<?php echo url('pages/inscription.php'); ?>" class="btn-custom font-semibold py-2 px-6 rounded-full text-center transition duration-300">
                    S'inscrire
                </a>
            </div>
        </div>
    </main>
</body>
</html>
