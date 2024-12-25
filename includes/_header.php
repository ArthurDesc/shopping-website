<?php
require_once __DIR__ . '/../functions/url.php';
require_once __DIR__ . '/../classe/AdminManager.php';
require_once __DIR__ . '/../classe/Panier.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/_db.php';
require_once __DIR__ . '/../classe/CategoryManager.php';

$adminManager = new AdminManager($conn);
$panier = new Panier();
$total = $panier->getNombreArticles();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['panier'])) {
  $_SESSION['panier'] = array();
}

$total = array_sum($_SESSION['panier'] ?? []);
$isProfilPage = basename($_SERVER['PHP_SELF']) === 'profil.php';
?>

<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>

  <!-- Styles pour intl-tel-input -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">

  <!-- Bibliothèques externes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Ressources locales -->
  <link rel="icon" type="image/png" href="<?php echo url('assets/images/favicon.png'); ?>">
  <?php include __DIR__ . '/../includes/_fonts.php'; ?>

    <!-- CSS principaux avec version pour cache busting -->
    <link rel="stylesheet" href="<?php echo url('assets/css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/responsive.css'); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/panier.css'); ?>">
    
  <?php if ($isProfilPage): ?>
        <link rel="stylesheet" href="<?php echo url('assets/css/profil.css'); ?>">
  <?php endif; ?>
    
  <?php if (basename($_SERVER['PHP_SELF']) == 'produit.php'): ?>
        <link rel="stylesheet" href="<?php echo url('assets/css/produit.css'); ?>">
  <?php endif; ?>
    
  <?php if (basename($_SERVER['PHP_SELF']) == 'detail.php' || basename($_SERVER['PHP_SELF']) == 'avis.php'): ?>
        <link rel="stylesheet" href="<?php echo url('assets/css/detail.css'); ?>">
  <?php endif; ?>
    
  <?php if (basename($_SERVER['PHP_SELF']) == 'wishlist.php'): ?>
        <link rel="stylesheet" href="<?php echo url('assets/css/wishlist.css'); ?>">
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>

<body class="flex flex-col min-h-full <?php echo $isProfilPage ? '' : 'pt-[55px]'; ?>">
  <?php if (!$isProfilPage): ?>
    <div id="header-container" class="fixed top-0 left-0 right-0 z-50 transition-transform duration-300 ease-in-out">
      <header class="bg-white shadow-md">
        <div class="flex justify-between items-center py-3 px-2 bg-white shadow-md">
          <div class="flex items-center justify-between w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
                        <!-- Logo -->
                        <div class="flex items-center">
                            <a href="<?php echo url(''); ?>" class="flex items-center">
                                <img src="<?php echo url('assets/images/logo.png'); ?>" alt="Logo" class="h-8 w-auto">
                            </a>
                        </div>

                        <!-- Navigation -->
                        <nav class="hidden md:flex space-x-8">
                            <a href="<?php echo url('pages/produit.php?collections=Homme'); ?>" class="text-gray-600 hover:text-blue-600">Homme</a>
                            <a href="<?php echo url('pages/produit.php?collections=Femme'); ?>" class="text-gray-600 hover:text-blue-600">Femme</a>
                            <?php if (isset($_SESSION['user'])): ?>
                                <a href="<?php echo url('pages/profil.php'); ?>" class="text-gray-600 hover:text-blue-600">Mon Compte</a>
                                <a href="<?php echo url('pages/wishlist.php'); ?>" class="text-gray-600 hover:text-blue-600">Wishlist</a>
                    <?php else: ?>
                                <a href="<?php echo url('pages/connexion.php'); ?>" class="text-gray-600 hover:text-blue-600">Connexion</a>
                    <?php endif; ?>
                            <a href="<?php echo url('pages/panier.php'); ?>" class="text-gray-600 hover:text-blue-600">
                                Panier (<?php echo $total; ?>)
                            </a>
                        </nav>
          </div>
        </div>
      </header>
    </div>
  <?php endif; ?>
</body>

</class=>