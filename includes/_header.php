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
    .iti {
      width: 100%;
    }

    .iti__flag {
      background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags.png");
    }

    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
      .iti__flag {
        background-image: url("https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/img/flags@2x.png");
      }
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
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>

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
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-36 bg-white rounded-md shadow-lg z-[1100] py-1">
                    <a href="<?php echo url("pages/produit.php?categories=1&collections=Homme"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Vêtements</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 296 296">
                        <path d="M147.763,44.074c12.801,0,23.858-8.162,27.83-20.169c-7.578,2.086-17.237,3.345-27.83,3.345
                          c-10.592,0-20.251-1.259-27.828-3.345C123.905,35.911,134.961,44.074,147.763,44.074z
                          M295.158,58.839c-0.608-1.706-1.873-3.109-3.521-3.873l-56.343-26.01c-11.985-4.06-24.195-7.267-36.524-9.611
                          c-0.434-0.085-0.866-0.126-1.292-0.126c-3.052,0-5.785,2.107-6.465,5.197c-4.502,19.82-22.047,34.659-43.251,34.659
                          c-21.203,0-38.749-14.838-43.25-34.659c-0.688-3.09-3.416-5.197-6.466-5.197c-0.426,0-0.858,0.041-1.292,0.126
                          c-12.328,2.344-24.538,5.551-36.542,9.611L3.889,54.965c-1.658,0.764-2.932,2.167-3.511,3.873
                          c-0.599,1.726-0.491,3.589,0.353,5.217l24.46,48.272c1.145,2.291,3.474,3.666,5.938,3.666c0.636,0,1.281-0.092,1.917-0.283
                          l27.167-8.052v161.97c0,3.678,3.001,6.678,6.689,6.678h161.723c3.678,0,6.67-3.001,6.67-6.678V107.66l27.186,8.052
                          c0.636,0.191,1.28,0.283,1.915,0.283c2.459,0,4.779-1.375,5.94-3.666l24.469-48.272C295.629,62.428,295.747,60.565,295.158,58.839z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=2&collections=Homme"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Chaussures</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 512.099 512.099">
                        <path d="M504.594,261.784l-0.017-2.534c-0.589-8.294-10.138-27.58-86.366-27.58h-0.128c-17.288,1.22-54.118-1.442-58.231-17.306
        c-0.247-0.973-0.776-1.801-1.331-2.611l-89.25,100.216c-6.067-1.417-12.271-2.97-18.654-4.727l88.218-99.046
        c-0.102,0.051-0.205,0.068-0.299,0.128c-0.137,0.068-0.282,0.154-0.418,0.23l-6.886-5.171c-0.546-1.408-1.408-3.115-2.441-4.873
        l-34.057,38.647c-1.681,1.911-4.036,2.893-6.4,2.893c-2.005,0-4.011-0.7-5.641-2.133c-3.533-3.115-3.874-8.508-0.759-12.041
        l33.007-37.461c-2.935-0.375-6.571-0.034-10.684,2.057l-2.816-1.869l-38.144,37.359c-1.673,1.63-3.814,2.441-5.973,2.441
        c-2.219,0-4.429-0.853-6.101-2.56c-3.294-3.371-3.243-8.772,0.119-12.066l38.289-37.504c-2.944-1.306-7.151-2.193-12.629-0.614
        c-2.654-1.425-5.914-3.575-7.936-5.871l-38.298,37.495c-1.655,1.621-3.814,2.432-5.965,2.432c-2.219,0-4.429-0.853-6.101-2.56
        c-3.294-3.371-3.234-8.772,0.137-12.066l37.436-36.651c-2.62-0.375-5.931-0.051-9.651,2.048
        c-2.441-0.58-7.893-2.876-14.345-12.041c-9.702-13.798-32.717-24.491-47.753-17.613c-6.306,2.876-15.821,10.846-11.819,32.282
        c-4.787,5.103-19.413,16.452-52.813,13.363c-5.154-0.717-19.874-4.352-25.873-12.424c-8.943-12.032-34.552-18.159-49.749-11.955
        c-9.464,3.857-14.285,12.143-12.919,22.144c1.382,10.146,0.239,14.251-0.239,15.445c-0.742,0.495-1.408,1.109-1.971,1.818
        c-1.212,1.553-29.107,37.956-17.818,84.241c-3.806,3.482-8.977,9.617-10.368,18.304c-1.143,7.108,0.461,14.225,4.779,21.231
        c-3.849,6.579-8.482,18.039-3.695,29.969c6.647,16.589,27.699,26.453,64.375,30.157c2.287,0.222,4.634-0.486,6.4-2.014
        c2.022-1.724,10.735-7.834,17.178-6.195c3.499,0.905,6.451,4.471,8.755,10.607c1.408,3.738,5.239,5.965,9.131,5.47
        c2.27-0.316,56.124-7.723,92.74-24.567c11.418-4.198,26.778-6.059,30.208-2.27c7.228,7.936,38.135,26.982,64.239,26.974
        c3.106,0,6.153-0.273,9.071-0.862c10.086-2.039,17.647-7.671,22.135-16.41c3.106-0.145,7.433,0.956,9.549,7.134
        c0.768,2.227,2.415,4.036,4.565,5.009c13.807,6.246,42.624,11.128,56.61-12.681l4.514-1.51c6.238,4.002,18.679,10.308,31.309,7.04
        c6.05-1.57,14.242-6.025,19.934-18.142l7.868-5.239l5.973-0.384c0.973-0.06,1.929-0.29,2.825-0.674
        c1.254-0.538,30.711-13.431,39.953-40.252C514.229,292.334,512.573,277.324,504.594,261.784z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=3&collections=Homme"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Accessoires</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 294.964 294.964">
                        <path d="M266.576,193.82c-1.699,0-3.076-1.377-3.076-3.076c0-13.029,10.562-23.591,23.591-23.591h0.038
        c1.175,0,2.281-0.552,2.987-1.491c0.706-0.939,0.93-2.155,0.604-3.284c-11.514-39.915-45.327-70.375-87.081-77.016
        c-2.552-7.025-9.265-12.051-17.169-12.051c-7.91,0-14.63,5.026-17.184,12.052c-51.757,8.234-91.319,53.064-91.319,107.139v4.152
        H12.5c-6.903,0-12.5,5.597-12.5,12.5c0,6.903,5.597,12.5,12.5,12.5h132.664c0.007,0,0.013-0.001,0.019-0.001l146.014-0.002
        c2.08,0,3.767-1.687,3.767-3.767v-20.297c0-2.08-1.687-3.767-3.767-3.767H266.576z" />
                      </svg>
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
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Vêtements</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 296 296">
                        <path d="M147.763,44.074c12.801,0,23.858-8.162,27.83-20.169c-7.578,2.086-17.237,3.345-27.83,3.345
                          c-10.592,0-20.251-1.259-27.828-3.345C123.905,35.911,134.961,44.074,147.763,44.074z
                          M295.158,58.839c-0.608-1.706-1.873-3.109-3.521-3.873l-56.343-26.01c-11.985-4.06-24.195-7.267-36.524-9.611
                          c-0.434-0.085-0.866-0.126-1.292-0.126c-3.052,0-5.785,2.107-6.465,5.197c-4.502,19.82-22.047,34.659-43.251,34.659
                          c-21.203,0-38.749-14.838-43.25-34.659c-0.688-3.09-3.416-5.197-6.466-5.197c-0.426,0-0.858,0.041-1.292,0.126
                          c-12.328,2.344-24.538,5.551-36.542,9.611L3.889,54.965c-1.658,0.764-2.932,2.167-3.511,3.873
                          c-0.599,1.726-0.491,3.589,0.353,5.217l24.46,48.272c1.145,2.291,3.474,3.666,5.938,3.666c0.636,0,1.281-0.092,1.917-0.283
                          l27.167-8.052v161.97c0,3.678,3.001,6.678,6.689,6.678h161.723c3.678,0,6.67-3.001,6.67-6.678V107.66l27.186,8.052
                          c0.636,0.191,1.28,0.283,1.915,0.283c2.459,0,4.779-1.375,5.94-3.666l24.469-48.272C295.629,62.428,295.747,60.565,295.158,58.839z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=2&collections=Femme"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Chaussures</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 512.099 512.099">
                        <path d="M504.594,261.784l-0.017-2.534c-0.589-8.294-10.138-27.58-86.366-27.58h-0.128c-17.288,1.22-54.118-1.442-58.231-17.306
        c-0.247-0.973-0.776-1.801-1.331-2.611l-89.25,100.216c-6.067-1.417-12.271-2.97-18.654-4.727l88.218-99.046
        c-0.102,0.051-0.205,0.068-0.299,0.128c-0.137,0.068-0.282,0.154-0.418,0.23l-6.886-5.171c-0.546-1.408-1.408-3.115-2.441-4.873
        l-34.057,38.647c-1.681,1.911-4.036,2.893-6.4,2.893c-2.005,0-4.011-0.7-5.641-2.133c-3.533-3.115-3.874-8.508-0.759-12.041
        l33.007-37.461c-2.935-0.375-6.571-0.034-10.684,2.057l-2.816-1.869l-38.144,37.359c-1.673,1.63-3.814,2.441-5.973,2.441
        c-2.219,0-4.429-0.853-6.101-2.56c-3.294-3.371-3.243-8.772,0.119-12.066l38.289-37.504c-2.944-1.306-7.151-2.193-12.629-0.614
        c-2.654-1.425-5.914-3.575-7.936-5.871l-38.298,37.495c-1.655,1.621-3.814,2.432-5.965,2.432c-2.219,0-4.429-0.853-6.101-2.56
        c-3.294-3.371-3.234-8.772,0.137-12.066l37.436-36.651c-2.62-0.375-5.931-0.051-9.651,2.048
        c-2.441-0.58-7.893-2.876-14.345-12.041c-9.702-13.798-32.717-24.491-47.753-17.613c-6.306,2.876-15.821,10.846-11.819,32.282
        c-4.787,5.103-19.413,16.452-52.813,13.363c-5.154-0.717-19.874-4.352-25.873-12.424c-8.943-12.032-34.552-18.159-49.749-11.955
        c-9.464,3.857-14.285,12.143-12.919,22.144c1.382,10.146,0.239,14.251-0.239,15.445c-0.742,0.495-1.408,1.109-1.971,1.818
        c-1.212,1.553-29.107,37.956-17.818,84.241c-3.806,3.482-8.977,9.617-10.368,18.304c-1.143,7.108,0.461,14.225,4.779,21.231
        c-3.849,6.579-8.482,18.039-3.695,29.969c6.647,16.589,27.699,26.453,64.375,30.157c2.287,0.222,4.634-0.486,6.4-2.014
        c2.022-1.724,10.735-7.834,17.178-6.195c3.499,0.905,6.451,4.471,8.755,10.607c1.408,3.738,5.239,5.965,9.131,5.47
        c2.27-0.316,56.124-7.723,92.74-24.567c11.418-4.198,26.778-6.059,30.208-2.27c7.228,7.936,38.135,26.982,64.239,26.974
        c3.106,0,6.153-0.273,9.071-0.862c10.086-2.039,17.647-7.671,22.135-16.41c3.106-0.145,7.433,0.956,9.549,7.134
        c0.768,2.227,2.415,4.036,4.565,5.009c13.807,6.246,42.624,11.128,56.61-12.681l4.514-1.51c6.238,4.002,18.679,10.308,31.309,7.04
        c6.05-1.57,14.242-6.025,19.934-18.142l7.868-5.239l5.973-0.384c0.973-0.06,1.929-0.29,2.825-0.674
        c1.254-0.538,30.711-13.431,39.953-40.252C514.229,292.334,512.573,277.324,504.594,261.784z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=3&collections=Femme"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Accessoires</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 294.964 294.964">
                        <path d="M266.576,193.82c-1.699,0-3.076-1.377-3.076-3.076c0-13.029,10.562-23.591,23.591-23.591h0.038
        c1.175,0,2.281-0.552,2.987-1.491c0.706-0.939,0.93-2.155,0.604-3.284c-11.514-39.915-45.327-70.375-87.081-77.016
        c-2.552-7.025-9.265-12.051-17.169-12.051c-7.91,0-14.63,5.026-17.184,12.052c-51.757,8.234-91.319,53.064-91.319,107.139v4.152
        H12.5c-6.903,0-12.5,5.597-12.5,12.5c0,6.903,5.597,12.5,12.5,12.5h132.664c0.007,0,0.013-0.001,0.019-0.001l146.014-0.002
        c2.08,0,3.767-1.687,3.767-3.767v-20.297c0-2.08-1.687-3.767-3.767-3.767H266.576z" />
                      </svg>
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
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Vêtements</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 296 296">
                        <path d="M147.763,44.074c12.801,0,23.858-8.162,27.83-20.169c-7.578,2.086-17.237,3.345-27.83,3.345
                          c-10.592,0-20.251-1.259-27.828-3.345C123.905,35.911,134.961,44.074,147.763,44.074z
                          M295.158,58.839c-0.608-1.706-1.873-3.109-3.521-3.873l-56.343-26.01c-11.985-4.06-24.195-7.267-36.524-9.611
                          c-0.434-0.085-0.866-0.126-1.292-0.126c-3.052,0-5.785,2.107-6.465,5.197c-4.502,19.82-22.047,34.659-43.251,34.659
                          c-21.203,0-38.749-14.838-43.25-34.659c-0.688-3.09-3.416-5.197-6.466-5.197c-0.426,0-0.858,0.041-1.292,0.126
                          c-12.328,2.344-24.538,5.551-36.542,9.611L3.889,54.965c-1.658,0.764-2.932,2.167-3.511,3.873
                          c-0.599,1.726-0.491,3.589,0.353,5.217l24.46,48.272c1.145,2.291,3.474,3.666,5.938,3.666c0.636,0,1.281-0.092,1.917-0.283
                          l27.167-8.052v161.97c0,3.678,3.001,6.678,6.689,6.678h161.723c3.678,0,6.67-3.001,6.67-6.678V107.66l27.186,8.052
                          c0.636,0.191,1.28,0.283,1.915,0.283c2.459,0,4.779-1.375,5.94-3.666l24.469-48.272C295.629,62.428,295.747,60.565,295.158,58.839z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=2&collections=Enfant"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Chaussures</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 512.099 512.099">
                        <path d="M504.594,261.784l-0.017-2.534c-0.589-8.294-10.138-27.58-86.366-27.58h-0.128c-17.288,1.22-54.118-1.442-58.231-17.306
        c-0.247-0.973-0.776-1.801-1.331-2.611l-89.25,100.216c-6.067-1.417-12.271-2.97-18.654-4.727l88.218-99.046
        c-0.102,0.051-0.205,0.068-0.299,0.128c-0.137,0.068-0.282,0.154-0.418,0.23l-6.886-5.171c-0.546-1.408-1.408-3.115-2.441-4.873
        l-34.057,38.647c-1.681,1.911-4.036,2.893-6.4,2.893c-2.005,0-4.011-0.7-5.641-2.133c-3.533-3.115-3.874-8.508-0.759-12.041
        l33.007-37.461c-2.935-0.375-6.571-0.034-10.684,2.057l-2.816-1.869l-38.144,37.359c-1.673,1.63-3.814,2.441-5.973,2.441
        c-2.219,0-4.429-0.853-6.101-2.56c-3.294-3.371-3.243-8.772,0.119-12.066l38.289-37.504c-2.944-1.306-7.151-2.193-12.629-0.614
        c-2.654-1.425-5.914-3.575-7.936-5.871l-38.298,37.495c-1.655,1.621-3.814,2.432-5.965,2.432c-2.219,0-4.429-0.853-6.101-2.56
        c-3.294-3.371-3.234-8.772,0.137-12.066l37.436-36.651c-2.62-0.375-5.931-0.051-9.651,2.048
        c-2.441-0.58-7.893-2.876-14.345-12.041c-9.702-13.798-32.717-24.491-47.753-17.613c-6.306,2.876-15.821,10.846-11.819,32.282
        c-4.787,5.103-19.413,16.452-52.813,13.363c-5.154-0.717-19.874-4.352-25.873-12.424c-8.943-12.032-34.552-18.159-49.749-11.955
        c-9.464,3.857-14.285,12.143-12.919,22.144c1.382,10.146,0.239,14.251-0.239,15.445c-0.742,0.495-1.408,1.109-1.971,1.818
        c-1.212,1.553-29.107,37.956-17.818,84.241c-3.806,3.482-8.977,9.617-10.368,18.304c-1.143,7.108,0.461,14.225,4.779,21.231
        c-3.849,6.579-8.482,18.039-3.695,29.969c6.647,16.589,27.699,26.453,64.375,30.157c2.287,0.222,4.634-0.486,6.4-2.014
        c2.022-1.724,10.735-7.834,17.178-6.195c3.499,0.905,6.451,4.471,8.755,10.607c1.408,3.738,5.239,5.965,9.131,5.47
        c2.27-0.316,56.124-7.723,92.74-24.567c11.418-4.198,26.778-6.059,30.208-2.27c7.228,7.936,38.135,26.982,64.239,26.974
        c3.106,0,6.153-0.273,9.071-0.862c10.086-2.039,17.647-7.671,22.135-16.41c3.106-0.145,7.433,0.956,9.549,7.134
        c0.768,2.227,2.415,4.036,4.565,5.009c13.807,6.246,42.624,11.128,56.61-12.681l4.514-1.51c6.238,4.002,18.679,10.308,31.309,7.04
        c6.05-1.57,14.242-6.025,19.934-18.142l7.868-5.239l5.973-0.384c0.973-0.06,1.929-0.29,2.825-0.674
        c1.254-0.538,30.711-13.431,39.953-40.252C514.229,292.334,512.573,277.324,504.594,261.784z" />
                      </svg>
                    </a>
                    <a href="<?php echo url("pages/produit.php?categories=3&collections=Enfant"); ?>"
                      x-data="{ hover: false }"
                      @mouseenter="hover = true"
                      @mouseleave="hover = false"
                      class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:text-blue-600">
                      <span>Accessoires</span>
                      <svg x-show="hover" x-cloak
                        class="w-5 h-5 ml-2 transition-transform duration-300"
                        fill="currentColor" viewBox="0 0 294.964 294.964">
                        <path d="M266.576,193.82c-1.699,0-3.076-1.377-3.076-3.076c0-13.029,10.562-23.591,23.591-23.591h0.038
        c1.175,0,2.281-0.552,2.987-1.491c0.706-0.939,0.93-2.155,0.604-3.284c-11.514-39.915-45.327-70.375-87.081-77.016
        c-2.552-7.025-9.265-12.051-17.169-12.051c-7.91,0-14.63,5.026-17.184,12.052c-51.757,8.234-91.319,53.064-91.319,107.139v4.152
        H12.5c-6.903,0-12.5,5.597-12.5,12.5c0,6.903,5.597,12.5,12.5,12.5h132.664c0.007,0,0.013-0.001,0.019-0.001l146.014-0.002
        c2.08,0,3.767-1.687,3.767-3.767v-20.297c0-2.08-1.687-3.767-3.767-3.767H266.576z" />
                      </svg>
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