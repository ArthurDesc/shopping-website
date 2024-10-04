<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/@heroicons/react/outline" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
</head>

<body>
  <header class="flex justify-center items-center p-4 bg-white shadow-md">
    <div class="flex justify-between items-center w-full max-w-screen-lg">
      <div class="menu-icon cursor-pointer" id="menu-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
      </div>
      <div class="logo">
        <a href="<?php echo BASE_URL; ?>index.php">
          <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Fitmode" class="h-8 w-auto">
        </a>
      </div>
      <div class="flex space-x-4">
        <a href="<?php echo BASE_URL; ?>pages/panier.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
          </svg>
        </a>
        <a href="<?php echo BASE_URL; ?>pages/profil.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
        </a>
      </div>
    </div>
  </header>

<!-- Barre de navigation latérale -->
<div id="sidebar" class="fixed left-0 top-0 w-64 h-full bg-white text-black shadow-lg transform -translate-x-full transition-transform">
  <nav class="p-4">
    <ul class="space-y-2">
      <li>
        <a href="<?php echo BASE_URL; ?>pages/produit.php" class="flex items-center justify-between py-2 border-b">
          Tout les articles
        </a>
      </li>
      <li>
        <a href="#" class="flex items-center justify-between py-2 border-b" id="menu-homme-toggle">
          Homme
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </a>
        <ul class="hidden pl-4" id="menu-homme">
          <li><a href="#" class="block py-2">T-shirts</a></li>
          <li><a href="#" class="block py-2">Shorts</a></li>
          <li><a href="#" class="block py-2">Joggings</a></li>
          <li><a href="#" class="block py-2">Chaussures</a></li>
          <li><a href="#" class="block py-2">Accessoires</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="flex items-center justify-between py-2 border-b" id="menu-femme-toggle">
          Femme
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </a>
        <ul class="hidden pl-4" id="menu-femme">
          <li><a href="#" class="block py-2">T-shirts</a></li>
          <li><a href="#" class="block py-2">Joggings</a></li>
          <li><a href="#" class="block py-2">Leggings</a></li>
          <li><a href="#" class="block py-2">Chaussures</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="flex items-center justify-between py-2 border-b" id="menu-enfants-toggle">
          Enfants
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </a>
        <ul class="hidden pl-4" id="menu-enfants">
          <li><a href="#" class="block py-2">T-shirts</a></li>
          <li><a href="#" class="block py-2">Shorts</a></li>
          <li><a href="#" class="block py-2">Chaussures</a></li>
        </ul>
      </li>
      <li>
        <a href="#" class="flex items-center justify-between py-2 border-b" id="menu-sports-toggle">
          Sports
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </a>
        <ul class="hidden pl-4" id="menu-sports">
          <li><a href="#" class="block py-2">Football</a></li>
          <li><a href="#" class="block py-2">Basketball</a></li>
          <li><a href="#" class="block py-2">Running</a></li>
          <li><a href="#" class="block py-2">Rugby</a></li>
          <li><a href="#" class="block py-2">Handball</a></li>
        </ul>
      </li>
    </ul>
  </nav>
</div>

<script>
  const toggles = ['menu-homme', 'menu-femme', 'menu-enfants', 'menu-sports'];

  toggles.forEach(toggle => {
    document.getElementById(`${toggle}-toggle`).addEventListener('click', function(event) {
      event.preventDefault();
      document.getElementById(toggle).classList.toggle('hidden');
      this.querySelector('svg').classList.toggle('rotate-180');
    });
  });
</script>