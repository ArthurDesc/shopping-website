<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/@heroicons/react/outline" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>

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
        <a href="<?php echo BASE_URL; ?>profile.php">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
          </svg>
        </a>
      </div>
    </div>
  </header>

<!-- Barre de navigation latérale -->
<div id="sidebar" class="fixed left-0 top-0 w-64 h-full bg-gray-800 text-white transform -translate-x-full transition-transform">
  <nav class="p-4">
    <ul>
      <li><a href="<?php echo BASE_URL; ?>index.php" class="block py-2">Accueil</a></li>
      <li><a href="<?php echo BASE_URL; ?>pages/produits.php" class="block py-2">Produits</a></li>
      <li><a href="<?php echo BASE_URL; ?>pages/contact.php" class="block py-2">Contact</a></li>
    </ul>
  </nav>
</div>