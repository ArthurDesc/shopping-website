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
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('BASE_URL')) {
  define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['panier'])) {
  $_SESSION['panier'] = array();
}

$total = array_sum($_SESSION['panier'] ?? []);

// Déterminer si nous sommes sur la page profil
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
  <style>
    .iti { width: 100%; }
    .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags.png"); }
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
      .iti__flag { background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags@2x.png"); }
    }
  </style>

  <!-- Bibliothèques externes -->
 
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Ressources locales -->
  <link rel="icon" type="image/png" href="<?php echo url('assets/images/favicon.png'); ?>">
  <?php include __DIR__ . '/../includes/_fonts.php'; ?>
  
  <!-- CSS principaux -->
  <link rel="stylesheet" href="<?php echo url('assets/css/main.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo url('assets/css/responsive.css?v=' . filemtime(__DIR__ . '/../assets/css/responsive.css')); ?>">
  
  <!-- CSS spécifiques aux pages -->
  <link rel="stylesheet" href="<?php echo url('assets/css/panier.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <?php if ($isProfilPage): ?>
    <link rel="stylesheet" href="<?php echo url('assets/css/profil.css?v=' . filemtime(__DIR__ . '/../assets/css/profil.css')); ?>">
  <?php endif; ?>
  <?php if (basename($_SERVER['PHP_SELF']) == 'produit.php'): ?>
    <link rel="stylesheet" href="<?php echo url('assets/css/produit.css?v=' . filemtime(__DIR__ . '/../assets/css/produit.css')); ?>">
  <?php endif; ?>
  <?php if (basename($_SERVER['PHP_SELF']) == 'detail.php'): ?>
    <link rel="stylesheet" href="<?php echo url('assets/css/detail.css?v=' . filemtime(__DIR__ . '/../assets/css/detail.css')); ?>">
  <?php endif; ?>
  <?php if (basename($_SERVER['PHP_SELF']) == 'wishlist.php'): ?>
    <link rel="stylesheet" href="<?php echo url('assets/css/wishlist.css?v=' . filemtime(__DIR__ . '/../assets/css/wishlist.css')); ?>">
  <?php endif; ?>
  
  

  <!-- Scripts -->
  <script src="<?php echo url('assets/js/cart.js'); ?>" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/vanilla-editable@1.0.3/dist/vanilla-editable.min.js"></script>

</head>

<body class="flex flex-col min-h-full <?php echo $isProfilPage ? '' : 'pt-[55px]'; ?>">
<?php if (!$isProfilPage): ?>
  <!-- Le reste du header ici (tout le contenu existant) -->
  <div id="header-container" class="fixed top-0 left-0 right-0 z-50 transition-transform duration-300 ease-in-out">
    <header class="bg-white shadow-md">
      <div class="flex justify-between items-center py-3 px-2 bg-white shadow-md">
        <div class="flex items-center justify-between w-full max-w-7xl mx-auto px-2 sm:px-4 md:px-6 lg:px-8">
          <div class="flex items-center w-1/3">
            <button class="menu-icon cursor-pointer md:hidden" id="menu-toggle">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
              </svg>
            </button>

            <nav class="hidden md:flex space-x-8 ml-4">
              <!-- Homme -->
              <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="<?php echo url("pages/produit.php?collections=Homme"); ?>"
                  class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Homme
                </a>
                <div x-show="open"
                  x-cloak
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="opacity-100 transform scale-100"
                  x-transition:leave-end="opacity-0 transform scale-95"
                  class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-36 bg-white rounded-md shadow-lg z-[1100] py-1">
                  <a href="<?php echo url("pages/produit.php?categories=1&collections=Homme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Vêtements</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=2&collections=Homme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Chaussures</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=3&collections=Homme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Accessoires</span>
                  </a>
                </div>
              </div>

              <!-- Femme -->
              <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="<?php echo url("pages/produit.php?collections=Femme"); ?>"
                  class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Femme
                </a>
                <div x-show="open"
                  x-cloak
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 transform scale-100"
                  x-transition:leave-end="opacity-0 transform scale-95"
                  class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-36 bg-white rounded-md shadow-lg z-[1100] py-1">
                  <a href="<?php echo url("pages/produit.php?categories=1&collections=Femme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Vêtements</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=2&collections=Femme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Chaussures</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=3&collections=Femme"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Accessoires</span>
                  </a>
                </div>
              </div>
              <!-- Enfant -->
              <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="<?php echo url("pages/produit.php?collections=Enfant"); ?>"
                  class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Enfant
                </a>
                <div x-show="open"
                  x-cloak
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 transform scale-100"
                  x-transition:leave-end="opacity-0 transform scale-95"
                  class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-36 bg-white rounded-md shadow-lg z-[1100] py-1">
                  <a href="<?php echo url("pages/produit.php?categories=1&collections=Enfant"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Vêtements</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=2&collections=Enfant"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Chaussures</span>
                  </a>
                  <a href="<?php echo url("pages/produit.php?categories=3&collections=Enfant"); ?>"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                    <span>Accessoires</span>
                  </a>
                </div>
              </div>




            </nav>

          </div>

          <div class="flex justify-center w-1/3">
            <a href="<?php echo url('index.php'); ?>" class="flex items-center">
              <img src="<?php echo url('assets/images/logo.png'); ?>" alt="Fitmode" class="h-8 w-auto">
            </a>
          </div>

          <div class="flex justify-end items-center space-x-4 w-1/3">
            <button id="search-toggle" class="hidden md:block text-black hover:text-blue-600 focus:outline-none">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
              </svg>
            </button>

            <a href="<?php echo url('pages/panier.php'); ?>" aria-label="Voir le panier" class="relative">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 hover:text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
              </svg>
              <span id="cart-count" class="absolute -top-2 -right-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold leading-none text-red-100 <?php echo $total > 0 ? 'bg-green-600' : 'bg-red-600'; ?> rounded-full">
                <?php echo $total; ?>
              </span>
            </a>
            <div class="relative" x-data="{ open: false }">
              <?php if (isset($_SESSION['id_utilisateur'])): ?>
                <!-- Si connecté : bouton avec menu déroulant -->
                <button @click="open = !open" class="relative inline-block focus:outline-none" @click.away="open = false">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 hover:text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                  </svg>
                  <?php if ($adminManager->isAdmin($_SESSION['id_utilisateur'])): ?>
                    <span class="absolute -bottom-2.5 left-1/2 transform -translate-x-1/2 text-[10px] font-bold text-blue-600 px-1 rounded">Admin</span>
                  <?php else: ?>
                    <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></span>
                  <?php endif; ?>
                </button>

                <!-- Menu déroulant pour utilisateurs connectés -->
                <div x-show="open"
                  x-cloak
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 transform scale-95"
                  x-transition:enter-end="opacity-100 transform scale-100"
                  x-transition:leave="transition ease-in duration-150"
                  x-transition:leave-start="opacity-100 transform scale-100"
                  x-transition:leave-end="opacity-0 transform scale-95"
                  class="absolute right-0 transform translate-x-1/3 mt-2 w-48 bg-white rounded-md shadow-lg z-[1200]">
                  <?php if ($adminManager->isAdmin($_SESSION['id_utilisateur'])): ?>
                    <a href="<?php echo url('admin/backofficeV2.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group relative" id="admin-link">
                      <div class="absolute inset-0 bg-white flex items-center justify-center hidden" id="admin-loader">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                      </div>
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                      </svg>
                      <span>Administration</span>
                    </a>
                    <a href="<?php echo url('pages/profil.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                      </svg>
                      <span>Mon compte</span>
                    </a>
                    <a href="<?php echo url('pages/wishlist.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                      </svg>
                      <span>Mes favoris</span>
                    </a>
                  <?php else: ?>
                    <!-- Même modification pour les liens non-admin -->
                    <a href="<?php echo url('pages/profil.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                      </svg>
                      <span>Mon compte</span>
                    </a>
                    <a href="<?php echo url('pages/commandes.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                      </svg>
                      <span>Mes commandes</span>
                    </a>
                    <a href="<?php echo url('pages/wishlist.php'); ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:text-blue-600 group">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2 group-hover:text-blue-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                      </svg>
                      <span>Mes favoris</span>
                    </a>
                  <?php endif; ?>
                  <a href="#" class="flex items-center px-4 py-2 text-sm text-red-600 " id="btn-deconnexion-profil">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                    </svg>
                    <span>Déconnexion</span>
                  </a>
                </div>
              <?php else: ?>
                <!-- Si non connecté : lien simple vers auth.php -->
                <a href="<?php echo url('pages/auth.php'); ?>" class="relative inline-block">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 hover:text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                  </svg>
                  <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div id="sidebar" class="fixed left-0 top-0 w-full md:w-64 h-full bg-gradient-to-b from-blue-400 to-blue-600 text-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50 overflow-y-auto">
      <!-- En-tête de la sidebar -->
      <div class="flex justify-between items-center p-4 border-b border-white/20">
        <button id="close-sidebar" class="text-white hover:text-white/80 focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <form action="<?php echo url('pages/recherche.php'); ?>" method="GET" class="flex-grow flex items-center ml-4">
          <input type="text" name="q" placeholder="Rechercher..."
            class="w-full h-10 px-3 bg-white/90 border border-white/30 rounded-l-md text-blue-900 focus:outline-none focus:ring-2 focus:ring-white/50 focus:bg-white placeholder-blue-400/70">
          <button type="submit" class="h-10 bg-white/20 text-white px-4 rounded-r-md hover:bg-white/30 focus:outline-none focus:ring-2 focus:ring-white/50 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>
        </form>
      </div>


      <nav class="p-4">
        <ul class="space-y-3">
          <!-- Bouton Tous les articles -->
          <li>
            <a href="<?php echo url('pages/produit.php'); ?>"
              class="flex items-center py-2 px-3 text-white hover:bg-white/10 rounded-md transition-colors duration-200">
              Tous les articles
            </a>
          </li>

          <!-- Dropdown Homme -->
          <li class="border-b border-white/20 pb-2">
            <a href="<?php echo url('pages/produit.php?collections=homme'); ?>"
              class="flex items-center justify-between py-2 px-3 text-white hover:bg-white/10 rounded-md transition-colors duration-200"
              id="menu-homme-toggle">
              Homme
              <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 transition-transform duration-200">
                <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4 space-y-1 mt-2" id="menu-homme">
              <li>
                <a href="<?php echo url('pages/produit.php?categories=1&collections=Homme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Vêtements
                </a>
              </li>
              <li>
                <a href="<?php echo url('/pages/produit.php?categories=2&collections=Homme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Chaussures
                </a>
              </li>
              <li>
                <a href="<?php echo url('pages/produit.php?categories=3&collections=Homme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Accessoires
                </a>
              </li>
            </ul>
          </li>

          <!-- Dropdown Femme -->
          <li class="border-b border-white/20 pb-2">
            <a href="<?php echo url('pages/produit.php?collections=femme'); ?>"
              class="flex items-center justify-between py-2 px-3 text-white hover:bg-white/10 rounded-md transition-colors duration-200"
              id="menu-femme-toggle">
              Femme
              <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 transition-transform duration-200">
                <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4 space-y-1 mt-2" id="menu-femme">
              <li>
                <a href="<?php echo url('pages/produit.php?categories=1&collections=Femme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Vêtements
                </a>
              </li>
              <li>
                <a href="<?php echo url('/pages/produit.php?categories=2&collections=Femme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Chaussures
                </a>
              </li>
              <li>
                <a href="<?php echo url('pages/produit.php?categories=3&collections=Femme'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Accessoires
                </a>
              </li>
            </ul>
          </li>

          <!-- Dropdown Enfant -->
          <li class="border-b border-white/20 pb-2">
            <a href="<?php echo url('pages/produit.php?collections=enfant'); ?>"
              class="flex items-center justify-between py-2 px-3 text-white hover:bg-white/10 rounded-md transition-colors duration-200"
              id="menu-enfant-toggle">
              Enfant
              <svg xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-5 h-5 transition-transform duration-200">
                <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4 space-y-1 mt-2" id="menu-enfant">
              <li>
                <a href="<?php echo url('pages/produit.php?categories=1&collections=Enfant'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Vêtements
                </a>
              </li>
              <li>
                <a href="<?php echo url('/pages/produit.php?categories=2&collections=Enfant'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Chaussures
                </a>
              </li>
              <li>
                <a href="<?php echo url('pages/produit.php?categories=3&collections=Enfant'); ?>"
                  class="block py-2 px-3 text-white/90 hover:bg-white/10 rounded-md transition-colors duration-200">
                  Accessoires
                </a>
              </li>
            </ul>
          </li>

     
        </ul>
      </nav>
    </div>

    <!-- Barre de recherche déroulante sticky avec autocomplétion -->
    <div id="search-bar" class="w-full bg-white transition-all duration-300 ease-in-out overflow-visible flex items-center h-0 shadow-md border-t border-gray-200">
      <div class="container mx-auto px-4">
        <form action="<?php echo url('pages/recherche.php'); ?>" method="GET" class="flex items-center relative">
          <input type="text" name="q" id="search-input" placeholder="Rechercher..." class="w-full px-4 py-2 focus:outline-none focus:border-blue-500 transition-colors duration-300">
          <button type="submit" class="ml-2 text-gray-500 hover:text-blue-500 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>
          <div id="autocomplete-results" class="absolute left-0 right-0 top-full bg-white border border-gray-300 rounded-b-lg shadow-lg z-10 hidden"></div>
        </form>
      </div>
    </div>
  </div>



  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const adminLink = document.getElementById('admin-link');
      const fullPageLoader = document.getElementById('full-page-loader');

      adminLink.addEventListener('click', function(e) {
        e.preventDefault();
        fullPageLoader.classList.remove('hidden');
        fullPageLoader.classList.add('flex');

        setTimeout(() => {
          window.location.href = this.href;
        }, 3000);
      });
    });
  </script>

  <!-- Ajoutez ceci juste après l'ouverture du <body> -->
  <div id="full-page-loader" class="fixed inset-0 bg-white bg-opacity-90 z-[9999] justify-center items-center hidden">
    <div class="spinnerContainer">
      <div class="spinner"></div>
      <div class="loader">
        <div class="words">
          <span class="word">Ajouter</span>
          <span class="word">Ajouter</span>
          <span class="word">Modifier</span>
          <span class="word">Supprimer</span>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <?php include __DIR__ . '/_modal_deconnexion.php'; ?>
</body>

</class=>