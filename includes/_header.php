<?php
require_once __DIR__ . '/../functions/url.php';
require_once __DIR__ . '/../classe/AdminManager.php';
require_once __DIR__ . '/../classe/Panier.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/_db.php';
require_once __DIR__ . '/../classe/CategoryManager.php';

$adminManager = new AdminManager($conn); // Assurez-vous que $conn est disponible
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

$categoryManager = new CategoryManager($conn);
$headerCategories = $categoryManager->getHeaderCategories();
?>



<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="icon" type="image/png" href="<?php echo url('assets/images/favicon.png'); ?>">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?php echo url('assets/css/panier.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo url('assets/css/produit.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo url('assets/css/detail.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo url('assets/css/main.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
  <link rel="stylesheet" href="<?php echo url('assets/css/responsive.css?v=' . filemtime(__DIR__ . '/../assets/css/responsive.css')); ?>">

</head>


<body class="flex flex-col min-h-full pt-[55px]">
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
  <?php foreach ($headerCategories as $category => $subcategories) : ?>
    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
      <a href="<?php echo url("pages/produit.php?collection=" . urlencode($category)); ?>" 
         class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
        <?php echo ucfirst($category); ?>
      </a>
      <div x-show="open" 
           x-transition:enter="transition ease-out duration-200" 
           x-transition:enter-start="opacity-0 transform scale-95" 
           x-transition:enter-end="opacity-100 transform scale-100" 
           x-transition:leave="transition ease-in duration-150" 
           x-transition:leave-start="opacity-100 transform scale-100" 
           x-transition:leave-end="opacity-0 transform scale-95" 
           class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-48 bg-white rounded-md shadow-lg z-[1100]">
        <?php foreach ($subcategories as $subcategory) : ?>
          <a href="<?php echo url("pages/produit.php?collection=" . urlencode($category) . "&category=" . urlencode($subcategory)); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            <?php echo $subcategory; ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
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
            <a href="<?php echo url('pages/profil.php'); ?>" class="relative inline-block">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 hover:text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
              <?php if (isset($_SESSION['id_utilisateur'])): ?>
                <?php if ($adminManager->isAdmin($_SESSION['id_utilisateur'])): ?>
                  <span class="absolute -bottom-2.5 left-1/2 transform -translate-x-1/2 text-[10px] font-bold text-blue-600 px-1 rounded">Admin</span>
                <?php else: ?>
                  <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></span>
                <?php endif; ?>
              <?php else: ?>
                <span class="absolute -bottom-1 -right-1 w-3 h-3 bg-red-500 rounded-full"></span>
              <?php endif; ?>
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- Barre de navigation latérale -->
    <div id="sidebar" class="fixed left-0 top-0 w-full md:w-64 h-full bg-white text-black shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-50">
      <div class="flex justify-between items-center p-4 border-b">
        <button id="close-sidebar" class="text-gray-500 hover:text-gray-700 focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <form action="<?php echo url('pages/recherche.php'); ?>" method="GET" class="flex-grow flex items-center ml-4">
          <input type="text" name="q" placeholder="Rechercher..." class="w-full px-3 py-2 border rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
            </svg>
          </button>
        </form>
      </div>

      <nav class="p-4">
        <ul class="space-y-2">
          <li>
            <a href="<?php echo url('pages/produit.php'); ?>" class="flex items-center justify-between py-2 border-b">
              Tous les articles
            </a>
          </li>
          <li>
            <a href="<?php echo url('pages/produit.php?collection=homme'); ?>" class="flex items-center justify-between py-2 border-b" id="menu-homme-toggle">
              Homme
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4" id="menu-homme">
              <li><a href="<?php echo url('pages/produit.php?collection=homme&category=t-shirts'); ?>" class="block py-2">T-shirts</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=homme&category=shorts'); ?>" class="block py-2">Shorts</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=homme&category=joggings'); ?>" class="block py-2">Joggings</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=homme&category=chaussures'); ?>" class="block py-2">Chaussures</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=homme&category=accessoires'); ?>" class="block py-2">Accessoires</a></li>
            </ul>
          </li>
          <li>
            <a href="<?php echo url('pages/produit.php?collection=femme'); ?>" class="flex items-center justify-between py-2 border-b" id="menu-femme-toggle">
              Femme
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4" id="menu-femme">
              <li><a href="<?php echo url('pages/produit.php?collection=femme&category=t-shirts'); ?>" class="block py-2">T-shirts</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=femme&category=joggings'); ?>" class="block py-2">Joggings</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=femme&category=leggings'); ?>" class="block py-2">Leggings</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=femme&category=chaussures'); ?>" class="block py-2">Chaussures</a></li>
            </ul>
          </li>
          <li>
            <a href="<?php echo url('pages/produit.php?collection=enfants'); ?>" class="flex items-center justify-between py-2 border-b" id="menu-enfants-toggle">
              Enfants
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4" id="menu-enfants">
              <li><a href="<?php echo url('pages/produit.php?collection=enfants&category=t-shirts'); ?>" class="block py-2">T-shirts</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=enfants&category=shorts'); ?>" class="block py-2">Shorts</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=enfants&category=chaussures'); ?>" class="block py-2">Chaussures</a></li>
            </ul>
          </li>
          <li>
            <a href="<?php echo url('pages/produit.php?collection=sports'); ?>" class="flex items-center justify-between py-2 border-b" id="menu-sports-toggle">
              Sports
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </a>
            <ul class="hidden pl-4" id="menu-sports">
              <li><a href="<?php echo url('pages/produit.php?collection=sports&category=football'); ?>" class="block py-2">Football</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=sports&category=basketball'); ?>" class="block py-2">Basketball</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=sports&category=running'); ?>" class="block py-2">Running</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=sports&category=rugby'); ?>" class="block py-2">Rugby</a></li>
              <li><a href="<?php echo url('pages/produit.php?collection=sports&category=handball'); ?>" class="block py-2">Handball</a></li>
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

  







