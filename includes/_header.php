<?php
require_once __DIR__ . '/../functions/url.php';
require_once __DIR__ . '/../classe/AdminManager.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/_db.php';
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>
  <link rel="icon" type="image/png" href="<?php echo url('assets/images/favicon.png'); ?>">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
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
            <nav class="hidden md:flex space-x-4 ml-4">
              <div class="relative group">
                <a href="<?php echo BASE_URL; ?>pages/homme.php" class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Homme
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="20" height="20">
                      <path d="M34.89 7.52c-0.01 0-0.02 0-0.04 0.01-0.02 0-0.05 0-0.08 0.01-0.05 0.01-0.09 0.02-0.13 0.04-0.02 0-0.03 0.01-0.04 0.01l-19.06 8.95c-0.17 0.08-0.3 0.21-0.38 0.38l-15.09 32.09c-0.18 0.38-0.03 0.82 0.34 1.02l17.83 9.38v0c0 0 0.01 0 0.01 0c0.09 0.04 0.18 0.07 0.27 0.08h0.02c0.09 0.01 0.19 0 0.28-0.03c0.01 0 0.02 0 0.02-0.01c0.09-0.03 0.18-0.07 0.25-0.13c0 0 0.01-0.01 0.01-0.01c0.06-0.05 0.12-0.11 0.16-0.18l5.79-9.13v41.69c0 0.09 0.02 0.18 0.05 0.27c0.01 0.03 0.03 0.06 0.04 0.09c0.03 0.06 0.06 0.11 0.11 0.16c0.02 0.02 0.04 0.05 0.07 0.07c0.05 0.04 0.11 0.08 0.16 0.11c0.02 0.02 0.05 0.03 0.07 0.04c0.09 0.04 0.18 0.05 0.27 0.05h50.33c0.07 0 0.14-0.01 0.2-0.03c0 0 0.01 0 0.01 0c0.03-0.01 0.06-0.02 0.09-0.03c0 0 0 0 0.01 0c0.02-0.01 0.05-0.02 0.07-0.04c0.02-0.01 0.04-0.03 0.07-0.04c0 0 0.01 0 0.01-0.01c0.01-0.01 0.02-0.02 0.04-0.03c0.01-0.01 0.03-0.02 0.04-0.04l0.04-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.02 0.02-0.03 0.03-0.05c0.01-0.01 0.02-0.02 0.02-0.03c0.02-0.03 0.03-0.05 0.04-0.08l0-0c0.01-0.02 0.01-0.04 0.02-0.05c0-0.02 0.01-0.04 0.02-0.05v0c0.01-0.03 0.01-0.06 0.02-0.09s0.01-0.06 0.01-0.09v-41.56l5.25 8.77c0.01 0.02 0.02 0.03 0.03 0.04c0 0.01 0.01 0.03 0.02 0.04c0.01 0.01 0.02 0.01 0.02 0.02c0.05 0.06 0.11 0.11 0.18 0.16c0.02 0.01 0.03 0.02 0.05 0.03c0.08 0.04 0.16 0.07 0.25 0.08c0.01 0 0.02 0 0.04 0.01c0.09 0.01 0.18 0 0.27-0.02c0 0 0.01 0 0.02 0c0.06-0.02 0.13-0.04 0.18-0.08c0.03-0.02 0.05-0.04 0.08-0.05h0v0c0.02-0.01 0.03-0.02 0.04-0.03l16.31-12.82c0.3-0.23 0.39-0.65 0.21-0.98l-15.09-28.41c-0.08-0.15-0.21-0.27-0.36-0.34l-19.06-8.95c-0.02 0-0.04-0.01-0.05-0.01c-0.06-0.02-0.13-0.04-0.2-0.05c-0.03 0-0.05-0.01-0.08-0.01c-0.09 0-0.17 0.01-0.25 0.04c-0.02 0.01-0.04 0.02-0.06 0.03c-0.04 0.01-0.07 0.03-0.11 0.05-6.33 3.93-11.65 5.21-16.38 4.91-4.73-0.31-8.91-2.23-12.91-4.9l0 0c-0.05-0.04-0.11-0.06-0.16-0.08c0 0 0 0 0 0h0c-0.09-0.03-0.18-0.04-0.27-0.04c-0.01 0-0.02 0-0.03 0zm30.22 1.67l18.45 8.65l14.66 27.6l-15.14 11.9-6.14-10.24v-10.65c0-0.21-0.08-0.41-0.23-0.55c-0.14-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v54.46h-48.76v-43.56-0.08-10.83c0-0.21-0.08-0.41-0.23-0.55c-0.15-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v10.65l-6.73 10.6-16.53-8.7l14.65-31.16l18.41-8.64c4.03 2.63 8.37 4.58 13.31 4.91c4.99 0.33 10.55-1.04 16.94-4.92z" />
                      <text x="30" y="95" font-size="10" font-weight="bold" font-family="Arial, sans-serif" fill="#000000">T-shirt</text>
                    </svg>

                    Vetement
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90.31 71.1875" width="25" height="25.59">
                      <path d="M84.69 14.58a33.07 33.07 0 00-24.6-13.31 1.41 1.41 0 00-.29 0 27.42 27.42 0 00-6.64-1.2c-8.91-.54-21.23 1.38-27.07 8.92-4.6 5.93-7.59 14.46-8.45 21.87a1.13 1.13 0 000 .26 28.62 28.62 0 01-8.4 4.84 21.47 21.47 0 00-5.85 2.7 8.29 8.29 0 00-3.25 8.34 1.18 1.18 0 00.94 1l.11 0c6.94 2.15 11.14-1.72 16.78-5.29a1.65 1.65 0 00.39-.35 29.08 29.08 0 017.46 2.76c5.13 2.75 9.15 7.18 14.35 9.84 10.87 5.56 20.36-2.1 29.57-7.53a1.34 1.34 0 00.32-.24c5.35 1.68 10.89 2.86 16.34 4.28a1.42 1.42 0 001.07-.12 1.6 1.6 0 001.57-1.42c1.35-11.85 3.32-25.11-4.35-35.35zm-1.15 3.58c5.53 8.64 3.85 20.38 2.69 30.23-5.27-1.36-10.57-2.65-15.8-4.14a33.35 33.35 0 01-4.41-1.61c2-9.06 6-19.63 4.6-28.95a14.91 14.91 0 00-3.84-8.23 30.34 30.34 0 0116.76 12.7zm-56.59-5c4.78-8.04 15.61-10.34 24.41-10.14 4.64.08 9.49.68 12.89 4.09 4 4 4 9.81 3.2 15.08-1 6.41-2.75 12.79-4.19 19.11-3.07-1.6-6-3.44-9.14-5.18-9.41-5.16-22.39-7.8-33-6.51l-.25.05a47.25 47.25 0 016.08-16.5zm-22.6 32.44a13 13 0 019.49-3.69c-3.03 2.09-5.86 3.96-9.49 3.69zm41.32 8.31c-6.63-1.5-11.37-7.13-17-10.55-7.27-4.43-17.25-6.4-24.23-1.7 2-2 5.69-2.76 8-3.78a30.53 30.53 0 007.62-4.93 1.53 1.53 0 00.33-.41 1.42 1.42 0 00.63 0c12.32-1.49 25.68 2.33 36 8.88a49.53 49.53 0 009.33 4.52c-5.94 3.86-13.52 9.57-20.62 7.97z" />
                    </svg>
                    Accessoires
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="-5 -10 110 135" width="30" height="37.5">
                      <path d="m38.855 48.301c0.73047-0.4375 0.97656-1.375 0.55469-2.1133-0.41797-0.74219-1.3555-1.0078-2.1016-0.60156-2.2812 1.4844-5.582 1.25-8.1758 1.2383-1.4023 0.003906-2.793-0.30859-4.0586-0.91406-0.77734-0.37109-1.707-0.039062-2.0781 0.73828-0.37109 0.77344-0.046875 1.707 0.73047 2.0781 2.5859 1.4023 6.0625 1.25 8.9336 1.2227 2.1719-0.003907 4.3086-0.57031 6.1953-1.6484z" />
                      <path d="m93.73 56.93c0.40234-3.8438-5.7656-8.4141-10.414-8.0391-5.3516 0.082031-10.652-1.0156-15.527-3.2188l-22.148-14.414c-1.0703-0.69141-2.3789-0.91797-3.625-0.62891-1.2422 0.28906-2.3164 1.0742-2.9727 2.168l-3.6875 6.2266v0.003906c-0.56641 0.94922-1.5898 1.5312-2.6953 1.5352-1.6602-0.10938-4.8477 0.52734-5.9648-1.168l-4.5977-5.707c-1.0352-1.2812-2.8828-1.5469-4.2305-0.60156-5.2344 3.8008-8.5859 9.6562-9.2148 16.09v9.8164c-1.543 1.6094-2.4023 3.75-2.4023 5.9766v1.3945c0 1.7266 1.4023 3.125 3.1289 3.125h62.098c8.4414 0.24609 21.668-4.0117 22.254-12.559zm-38.152-11.734 0.97266-3.1094 2.7578 1.793-0.98047 3.1055zm-1.75-4.8828-0.97266 3.1094-2.7578-1.793 0.98047-3.1055zm-42.051 8.957c0.58594-5.4531 3.4492-10.398 7.8867-13.621l4.5977 5.707c1.1836 1.4766 2.9766 2.3359 4.8711 2.332h3.5273c2.207-0.003906 4.2539-1.1719 5.3828-3.0703l3.6875-6.2266c0.21875-0.36719 0.57812-0.62891 0.99609-0.72656 0.41406-0.097656 0.85156-0.019531 1.2109 0.21094l4.4219 2.8789-0.98047 3.1055-2.4141-1.5703c-0.35938-0.23047-0.79297-0.30859-1.207-0.21094-0.41406 0.097656-0.76953 0.35938-0.98828 0.72266l-2.0234 3.4141c-0.42188 0.74219-0.17188 1.6836 0.5625 2.1172 0.73047 0.43359 1.6758 0.19922 2.125-0.52344l1.1914-2.0156 2.4609 1.6016 0.003906 0.003907c0.17969 0.19531 0.40625 0.33984 0.65625 0.42578l15.262 9.9336c0.72266 0.47266 1.6914 0.26563 2.1641-0.45703 0.46875-0.72266 0.26562-1.6914-0.45703-2.1602l-3.6719-2.3906 0.98047-3.1055 4.1289 2.6875c6.0117 3.1719 13.395 3.6719 17.152 3.6797l0.003906 0.003906c2.1914-0.03125 4.3086 0.80078 5.8945 2.3203 0.46875 0.44531 0.86328 0.96484 1.168 1.5352l-2.7344 1.1406c-8.0625 3.8594-19.551 3.043-28.281 3.0781-1.2461 0-2.4922-0.046875-3.7344-0.11719l0.71875-0.97656h-0.003906c0.51172-0.69531 0.36328-1.6719-0.33203-2.1836-0.69531-0.51172-1.6758-0.35938-2.1836 0.33594l-1.8516 2.5117c-1.5547-0.17578-3.1055-0.39062-4.6445-0.67578l1.8242-2.4766c0.49219-0.69531 0.33594-1.6523-0.35156-2.1562-0.68359-0.50391-1.6445-0.36719-2.1641 0.30469l-2.7773 3.7734c-1.5391-0.21484-3.0898-0.33594-4.6445-0.36328l2.832-3.8438c0.48828-0.69531 0.33594-1.6523-0.35156-2.1602-0.68359-0.50391-1.6484-0.36719-2.168 0.30859l-4.1836 5.6758h-23.562zm71.086 15.012c-3.6289 1.4141-7.4922 2.1211-11.387 2.0859l-62.102-0.003907c-0.26562-1.8672 0.28516-3.7617 1.5117-5.1914h27.906c4.6914-0.074219 9.2344 1.3633 13.891 1.7188 4.4805 0.53125 14.926 0.26953 19.555 0.32812v-0.003906c5.6992 0.007812 11.344-1.1211 16.602-3.3203l0.67969-0.28516v0.003906c-1.7383 2.1484-4.043 3.7656-6.6562 4.668z" />
                      <text x="0.0" y="117.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000"></text><text x="0.0" y="122.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000">from Noun Project</text>
                    </svg>

                    Chaussures
                  </a>
                </div>
              </div>
              <div class="relative group">
                <a href="<?php echo BASE_URL; ?>pages/femme.php" class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Femme
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="20" height="20">
                      <path d="M34.89 7.52c-0.01 0-0.02 0-0.04 0.01-0.02 0-0.05 0-0.08 0.01-0.05 0.01-0.09 0.02-0.13 0.04-0.02 0-0.03 0.01-0.04 0.01l-19.06 8.95c-0.17 0.08-0.3 0.21-0.38 0.38l-15.09 32.09c-0.18 0.38-0.03 0.82 0.34 1.02l17.83 9.38v0c0 0 0.01 0 0.01 0c0.09 0.04 0.18 0.07 0.27 0.08h0.02c0.09 0.01 0.19 0 0.28-0.03c0.01 0 0.02 0 0.02-0.01c0.09-0.03 0.18-0.07 0.25-0.13c0 0 0.01-0.01 0.01-0.01c0.06-0.05 0.12-0.11 0.16-0.18l5.79-9.13v41.69c0 0.09 0.02 0.18 0.05 0.27c0.01 0.03 0.03 0.06 0.04 0.09c0.03 0.06 0.06 0.11 0.11 0.16c0.02 0.02 0.04 0.05 0.07 0.07c0.05 0.04 0.11 0.08 0.16 0.11c0.02 0.02 0.05 0.03 0.07 0.04c0.09 0.04 0.18 0.05 0.27 0.05h50.33c0.07 0 0.14-0.01 0.2-0.03c0 0 0.01 0 0.01 0c0.03-0.01 0.06-0.02 0.09-0.03c0 0 0 0 0.01 0c0.02-0.01 0.05-0.02 0.07-0.04c0.02-0.01 0.04-0.03 0.07-0.04c0 0 0.01 0 0.01-0.01c0.01-0.01 0.02-0.02 0.04-0.03c0.01-0.01 0.03-0.02 0.04-0.04l0.04-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.02 0.02-0.03 0.03-0.05c0.01-0.01 0.02-0.02 0.02-0.03c0.02-0.03 0.03-0.05 0.04-0.08l0-0c0.01-0.02 0.01-0.04 0.02-0.05c0-0.02 0.01-0.04 0.02-0.05v0c0.01-0.03 0.01-0.06 0.02-0.09s0.01-0.06 0.01-0.09v-41.56l5.25 8.77c0.01 0.02 0.02 0.03 0.03 0.04c0 0.01 0.01 0.03 0.02 0.04c0.01 0.01 0.02 0.01 0.02 0.02c0.05 0.06 0.11 0.11 0.18 0.16c0.02 0.01 0.03 0.02 0.05 0.03c0.08 0.04 0.16 0.07 0.25 0.08c0.01 0 0.02 0 0.04 0.01c0.09 0.01 0.18 0 0.27-0.02c0 0 0.01 0 0.02 0c0.06-0.02 0.13-0.04 0.18-0.08c0.03-0.02 0.05-0.04 0.08-0.05h0v0c0.02-0.01 0.03-0.02 0.04-0.03l16.31-12.82c0.3-0.23 0.39-0.65 0.21-0.98l-15.09-28.41c-0.08-0.15-0.21-0.27-0.36-0.34l-19.06-8.95c-0.02 0-0.04-0.01-0.05-0.01c-0.06-0.02-0.13-0.04-0.2-0.05c-0.03 0-0.05-0.01-0.08-0.01c-0.09 0-0.17 0.01-0.25 0.04c-0.02 0.01-0.04 0.02-0.06 0.03c-0.04 0.01-0.07 0.03-0.11 0.05-6.33 3.93-11.65 5.21-16.38 4.91-4.73-0.31-8.91-2.23-12.91-4.9l0 0c-0.05-0.04-0.11-0.06-0.16-0.08c0 0 0 0 0 0h0c-0.09-0.03-0.18-0.04-0.27-0.04c-0.01 0-0.02 0-0.03 0zm30.22 1.67l18.45 8.65l14.66 27.6l-15.14 11.9-6.14-10.24v-10.65c0-0.21-0.08-0.41-0.23-0.55c-0.14-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v54.46h-48.76v-43.56-0.08-10.83c0-0.21-0.08-0.41-0.23-0.55c-0.15-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v10.65l-6.73 10.6-16.53-8.7l14.65-31.16l18.41-8.64c4.03 2.63 8.37 4.58 13.31 4.91c4.99 0.33 10.55-1.04 16.94-4.92z" />
                      <text x="30" y="95" font-size="10" font-weight="bold" font-family="Arial, sans-serif" fill="#000000">T-shirt</text>
                    </svg>

                    Vetement
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90.31 71.1875" width="25" height="25.59">
                      <path d="M84.69 14.58a33.07 33.07 0 00-24.6-13.31 1.41 1.41 0 00-.29 0 27.42 27.42 0 00-6.64-1.2c-8.91-.54-21.23 1.38-27.07 8.92-4.6 5.93-7.59 14.46-8.45 21.87a1.13 1.13 0 000 .26 28.62 28.62 0 01-8.4 4.84 21.47 21.47 0 00-5.85 2.7 8.29 8.29 0 00-3.25 8.34 1.18 1.18 0 00.94 1l.11 0c6.94 2.15 11.14-1.72 16.78-5.29a1.65 1.65 0 00.39-.35 29.08 29.08 0 017.46 2.76c5.13 2.75 9.15 7.18 14.35 9.84 10.87 5.56 20.36-2.1 29.57-7.53a1.34 1.34 0 00.32-.24c5.35 1.68 10.89 2.86 16.34 4.28a1.42 1.42 0 001.07-.12 1.6 1.6 0 001.57-1.42c1.35-11.85 3.32-25.11-4.35-35.35zm-1.15 3.58c5.53 8.64 3.85 20.38 2.69 30.23-5.27-1.36-10.57-2.65-15.8-4.14a33.35 33.35 0 01-4.41-1.61c2-9.06 6-19.63 4.6-28.95a14.91 14.91 0 00-3.84-8.23 30.34 30.34 0 0116.76 12.7zm-56.59-5c4.78-8.04 15.61-10.34 24.41-10.14 4.64.08 9.49.68 12.89 4.09 4 4 4 9.81 3.2 15.08-1 6.41-2.75 12.79-4.19 19.11-3.07-1.6-6-3.44-9.14-5.18-9.41-5.16-22.39-7.8-33-6.51l-.25.05a47.25 47.25 0 016.08-16.5zm-22.6 32.44a13 13 0 019.49-3.69c-3.03 2.09-5.86 3.96-9.49 3.69zm41.32 8.31c-6.63-1.5-11.37-7.13-17-10.55-7.27-4.43-17.25-6.4-24.23-1.7 2-2 5.69-2.76 8-3.78a30.53 30.53 0 007.62-4.93 1.53 1.53 0 00.33-.41 1.42 1.42 0 00.63 0c12.32-1.49 25.68 2.33 36 8.88a49.53 49.53 0 009.33 4.52c-5.94 3.86-13.52 9.57-20.62 7.97z" />
                    </svg>
                    Accessoires
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="-5 -10 110 135" width="30" height="37.5">
                      <path d="m38.855 48.301c0.73047-0.4375 0.97656-1.375 0.55469-2.1133-0.41797-0.74219-1.3555-1.0078-2.1016-0.60156-2.2812 1.4844-5.582 1.25-8.1758 1.2383-1.4023 0.003906-2.793-0.30859-4.0586-0.91406-0.77734-0.37109-1.707-0.039062-2.0781 0.73828-0.37109 0.77344-0.046875 1.707 0.73047 2.0781 2.5859 1.4023 6.0625 1.25 8.9336 1.2227 2.1719-0.003907 4.3086-0.57031 6.1953-1.6484z" />
                      <path d="m93.73 56.93c0.40234-3.8438-5.7656-8.4141-10.414-8.0391-5.3516 0.082031-10.652-1.0156-15.527-3.2188l-22.148-14.414c-1.0703-0.69141-2.3789-0.91797-3.625-0.62891-1.2422 0.28906-2.3164 1.0742-2.9727 2.168l-3.6875 6.2266v0.003906c-0.56641 0.94922-1.5898 1.5312-2.6953 1.5352-1.6602-0.10938-4.8477 0.52734-5.9648-1.168l-4.5977-5.707c-1.0352-1.2812-2.8828-1.5469-4.2305-0.60156-5.2344 3.8008-8.5859 9.6562-9.2148 16.09v9.8164c-1.543 1.6094-2.4023 3.75-2.4023 5.9766v1.3945c0 1.7266 1.4023 3.125 3.1289 3.125h62.098c8.4414 0.24609 21.668-4.0117 22.254-12.559zm-38.152-11.734 0.97266-3.1094 2.7578 1.793-0.98047 3.1055zm-1.75-4.8828-0.97266 3.1094-2.7578-1.793 0.98047-3.1055zm-42.051 8.957c0.58594-5.4531 3.4492-10.398 7.8867-13.621l4.5977 5.707c1.1836 1.4766 2.9766 2.3359 4.8711 2.332h3.5273c2.207-0.003906 4.2539-1.1719 5.3828-3.0703l3.6875-6.2266c0.21875-0.36719 0.57812-0.62891 0.99609-0.72656 0.41406-0.097656 0.85156-0.019531 1.2109 0.21094l4.4219 2.8789-0.98047 3.1055-2.4141-1.5703c-0.35938-0.23047-0.79297-0.30859-1.207-0.21094-0.41406 0.097656-0.76953 0.35938-0.98828 0.72266l-2.0234 3.4141c-0.42188 0.74219-0.17188 1.6836 0.5625 2.1172 0.73047 0.43359 1.6758 0.19922 2.125-0.52344l1.1914-2.0156 2.4609 1.6016 0.003906 0.003907c0.17969 0.19531 0.40625 0.33984 0.65625 0.42578l15.262 9.9336c0.72266 0.47266 1.6914 0.26563 2.1641-0.45703 0.46875-0.72266 0.26562-1.6914-0.45703-2.1602l-3.6719-2.3906 0.98047-3.1055 4.1289 2.6875c6.0117 3.1719 13.395 3.6719 17.152 3.6797l0.003906 0.003906c2.1914-0.03125 4.3086 0.80078 5.8945 2.3203 0.46875 0.44531 0.86328 0.96484 1.168 1.5352l-2.7344 1.1406c-8.0625 3.8594-19.551 3.043-28.281 3.0781-1.2461 0-2.4922-0.046875-3.7344-0.11719l0.71875-0.97656h-0.003906c0.51172-0.69531 0.36328-1.6719-0.33203-2.1836-0.69531-0.51172-1.6758-0.35938-2.1836 0.33594l-1.8516 2.5117c-1.5547-0.17578-3.1055-0.39062-4.6445-0.67578l1.8242-2.4766c0.49219-0.69531 0.33594-1.6523-0.35156-2.1562-0.68359-0.50391-1.6445-0.36719-2.1641 0.30469l-2.7773 3.7734c-1.5391-0.21484-3.0898-0.33594-4.6445-0.36328l2.832-3.8438c0.48828-0.69531 0.33594-1.6523-0.35156-2.1602-0.68359-0.50391-1.6484-0.36719-2.168 0.30859l-4.1836 5.6758h-23.562zm71.086 15.012c-3.6289 1.4141-7.4922 2.1211-11.387 2.0859l-62.102-0.003907c-0.26562-1.8672 0.28516-3.7617 1.5117-5.1914h27.906c4.6914-0.074219 9.2344 1.3633 13.891 1.7188 4.4805 0.53125 14.926 0.26953 19.555 0.32812v-0.003906c5.6992 0.007812 11.344-1.1211 16.602-3.3203l0.67969-0.28516v0.003906c-1.7383 2.1484-4.043 3.7656-6.6562 4.668z" />
                      <text x="0.0" y="117.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000"></text><text x="0.0" y="122.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000">from Noun Project</text>
                    </svg>

                    Chaussures
                  </a>
                </div>
              </div>
              <div class="relative group">
                <a href="<?php echo BASE_URL; ?>pages/enfant.php" class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Enfant
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="20" height="20">
                      <path d="M34.89 7.52c-0.01 0-0.02 0-0.04 0.01-0.02 0-0.05 0-0.08 0.01-0.05 0.01-0.09 0.02-0.13 0.04-0.02 0-0.03 0.01-0.04 0.01l-19.06 8.95c-0.17 0.08-0.3 0.21-0.38 0.38l-15.09 32.09c-0.18 0.38-0.03 0.82 0.34 1.02l17.83 9.38v0c0 0 0.01 0 0.01 0c0.09 0.04 0.18 0.07 0.27 0.08h0.02c0.09 0.01 0.19 0 0.28-0.03c0.01 0 0.02 0 0.02-0.01c0.09-0.03 0.18-0.07 0.25-0.13c0 0 0.01-0.01 0.01-0.01c0.06-0.05 0.12-0.11 0.16-0.18l5.79-9.13v41.69c0 0.09 0.02 0.18 0.05 0.27c0.01 0.03 0.03 0.06 0.04 0.09c0.03 0.06 0.06 0.11 0.11 0.16c0.02 0.02 0.04 0.05 0.07 0.07c0.05 0.04 0.11 0.08 0.16 0.11c0.02 0.02 0.05 0.03 0.07 0.04c0.09 0.04 0.18 0.05 0.27 0.05h50.33c0.07 0 0.14-0.01 0.2-0.03c0 0 0.01 0 0.01 0c0.03-0.01 0.06-0.02 0.09-0.03c0 0 0 0 0.01 0c0.02-0.01 0.05-0.02 0.07-0.04c0.02-0.01 0.04-0.03 0.07-0.04c0 0 0.01 0 0.01-0.01c0.01-0.01 0.02-0.02 0.04-0.03c0.01-0.01 0.03-0.02 0.04-0.04l0.04-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.01 0.02-0.02 0.03-0.04c0.01-0.02 0.02-0.03 0.03-0.05c0.01-0.01 0.02-0.02 0.02-0.03c0.02-0.03 0.03-0.05 0.04-0.08l0-0c0.01-0.02 0.01-0.04 0.02-0.05c0-0.02 0.01-0.04 0.02-0.05v0c0.01-0.03 0.01-0.06 0.02-0.09s0.01-0.06 0.01-0.09v-41.56l5.25 8.77c0.01 0.02 0.02 0.03 0.03 0.04c0 0.01 0.01 0.03 0.02 0.04c0.01 0.01 0.02 0.01 0.02 0.02c0.05 0.06 0.11 0.11 0.18 0.16c0.02 0.01 0.03 0.02 0.05 0.03c0.08 0.04 0.16 0.07 0.25 0.08c0.01 0 0.02 0 0.04 0.01c0.09 0.01 0.18 0 0.27-0.02c0 0 0.01 0 0.02 0c0.06-0.02 0.13-0.04 0.18-0.08c0.03-0.02 0.05-0.04 0.08-0.05h0v0c0.02-0.01 0.03-0.02 0.04-0.03l16.31-12.82c0.3-0.23 0.39-0.65 0.21-0.98l-15.09-28.41c-0.08-0.15-0.21-0.27-0.36-0.34l-19.06-8.95c-0.02 0-0.04-0.01-0.05-0.01c-0.06-0.02-0.13-0.04-0.2-0.05c-0.03 0-0.05-0.01-0.08-0.01c-0.09 0-0.17 0.01-0.25 0.04c-0.02 0.01-0.04 0.02-0.06 0.03c-0.04 0.01-0.07 0.03-0.11 0.05-6.33 3.93-11.65 5.21-16.38 4.91-4.73-0.31-8.91-2.23-12.91-4.9l0 0c-0.05-0.04-0.11-0.06-0.16-0.08c0 0 0 0 0 0h0c-0.09-0.03-0.18-0.04-0.27-0.04c-0.01 0-0.02 0-0.03 0zm30.22 1.67l18.45 8.65l14.66 27.6l-15.14 11.9-6.14-10.24v-10.65c0-0.21-0.08-0.41-0.23-0.55c-0.14-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v54.46h-48.76v-43.56-0.08-10.83c0-0.21-0.08-0.41-0.23-0.55c-0.15-0.15-0.34-0.23-0.55-0.23c-0.21 0-0.41 0.08-0.55 0.23c-0.15 0.15-0.23 0.34-0.23 0.55v10.65l-6.73 10.6-16.53-8.7l14.65-31.16l18.41-8.64c4.03 2.63 8.37 4.58 13.31 4.91c4.99 0.33 10.55-1.04 16.94-4.92z" />
                      <text x="30" y="95" font-size="10" font-weight="bold" font-family="Arial, sans-serif" fill="#000000">T-shirt</text>
                    </svg>

                    Vetement
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 90.31 71.1875" width="25" height="25.59">
                      <path d="M84.69 14.58a33.07 33.07 0 00-24.6-13.31 1.41 1.41 0 00-.29 0 27.42 27.42 0 00-6.64-1.2c-8.91-.54-21.23 1.38-27.07 8.92-4.6 5.93-7.59 14.46-8.45 21.87a1.13 1.13 0 000 .26 28.62 28.62 0 01-8.4 4.84 21.47 21.47 0 00-5.85 2.7 8.29 8.29 0 00-3.25 8.34 1.18 1.18 0 00.94 1l.11 0c6.94 2.15 11.14-1.72 16.78-5.29a1.65 1.65 0 00.39-.35 29.08 29.08 0 017.46 2.76c5.13 2.75 9.15 7.18 14.35 9.84 10.87 5.56 20.36-2.1 29.57-7.53a1.34 1.34 0 00.32-.24c5.35 1.68 10.89 2.86 16.34 4.28a1.42 1.42 0 001.07-.12 1.6 1.6 0 001.57-1.42c1.35-11.85 3.32-25.11-4.35-35.35zm-1.15 3.58c5.53 8.64 3.85 20.38 2.69 30.23-5.27-1.36-10.57-2.65-15.8-4.14a33.35 33.35 0 01-4.41-1.61c2-9.06 6-19.63 4.6-28.95a14.91 14.91 0 00-3.84-8.23 30.34 30.34 0 0116.76 12.7zm-56.59-5c4.78-8.04 15.61-10.34 24.41-10.14 4.64.08 9.49.68 12.89 4.09 4 4 4 9.81 3.2 15.08-1 6.41-2.75 12.79-4.19 19.11-3.07-1.6-6-3.44-9.14-5.18-9.41-5.16-22.39-7.8-33-6.51l-.25.05a47.25 47.25 0 016.08-16.5zm-22.6 32.44a13 13 0 019.49-3.69c-3.03 2.09-5.86 3.96-9.49 3.69zm41.32 8.31c-6.63-1.5-11.37-7.13-17-10.55-7.27-4.43-17.25-6.4-24.23-1.7 2-2 5.69-2.76 8-3.78a30.53 30.53 0 007.62-4.93 1.53 1.53 0 00.33-.41 1.42 1.42 0 00.63 0c12.32-1.49 25.68 2.33 36 8.88a49.53 49.53 0 009.33 4.52c-5.94 3.86-13.52 9.57-20.62 7.97z" />
                    </svg>
                    Accessoires
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="-5 -10 110 135" width="30" height="37.5">
                      <path d="m38.855 48.301c0.73047-0.4375 0.97656-1.375 0.55469-2.1133-0.41797-0.74219-1.3555-1.0078-2.1016-0.60156-2.2812 1.4844-5.582 1.25-8.1758 1.2383-1.4023 0.003906-2.793-0.30859-4.0586-0.91406-0.77734-0.37109-1.707-0.039062-2.0781 0.73828-0.37109 0.77344-0.046875 1.707 0.73047 2.0781 2.5859 1.4023 6.0625 1.25 8.9336 1.2227 2.1719-0.003907 4.3086-0.57031 6.1953-1.6484z" />
                      <path d="m93.73 56.93c0.40234-3.8438-5.7656-8.4141-10.414-8.0391-5.3516 0.082031-10.652-1.0156-15.527-3.2188l-22.148-14.414c-1.0703-0.69141-2.3789-0.91797-3.625-0.62891-1.2422 0.28906-2.3164 1.0742-2.9727 2.168l-3.6875 6.2266v0.003906c-0.56641 0.94922-1.5898 1.5312-2.6953 1.5352-1.6602-0.10938-4.8477 0.52734-5.9648-1.168l-4.5977-5.707c-1.0352-1.2812-2.8828-1.5469-4.2305-0.60156-5.2344 3.8008-8.5859 9.6562-9.2148 16.09v9.8164c-1.543 1.6094-2.4023 3.75-2.4023 5.9766v1.3945c0 1.7266 1.4023 3.125 3.1289 3.125h62.098c8.4414 0.24609 21.668-4.0117 22.254-12.559zm-38.152-11.734 0.97266-3.1094 2.7578 1.793-0.98047 3.1055zm-1.75-4.8828-0.97266 3.1094-2.7578-1.793 0.98047-3.1055zm-42.051 8.957c0.58594-5.4531 3.4492-10.398 7.8867-13.621l4.5977 5.707c1.1836 1.4766 2.9766 2.3359 4.8711 2.332h3.5273c2.207-0.003906 4.2539-1.1719 5.3828-3.0703l3.6875-6.2266c0.21875-0.36719 0.57812-0.62891 0.99609-0.72656 0.41406-0.097656 0.85156-0.019531 1.2109 0.21094l4.4219 2.8789-0.98047 3.1055-2.4141-1.5703c-0.35938-0.23047-0.79297-0.30859-1.207-0.21094-0.41406 0.097656-0.76953 0.35938-0.98828 0.72266l-2.0234 3.4141c-0.42188 0.74219-0.17188 1.6836 0.5625 2.1172 0.73047 0.43359 1.6758 0.19922 2.125-0.52344l1.1914-2.0156 2.4609 1.6016 0.003906 0.003907c0.17969 0.19531 0.40625 0.33984 0.65625 0.42578l15.262 9.9336c0.72266 0.47266 1.6914 0.26563 2.1641-0.45703 0.46875-0.72266 0.26562-1.6914-0.45703-2.1602l-3.6719-2.3906 0.98047-3.1055 4.1289 2.6875c6.0117 3.1719 13.395 3.6719 17.152 3.6797l0.003906 0.003906c2.1914-0.03125 4.3086 0.80078 5.8945 2.3203 0.46875 0.44531 0.86328 0.96484 1.168 1.5352l-2.7344 1.1406c-8.0625 3.8594-19.551 3.043-28.281 3.0781-1.2461 0-2.4922-0.046875-3.7344-0.11719l0.71875-0.97656h-0.003906c0.51172-0.69531 0.36328-1.6719-0.33203-2.1836-0.69531-0.51172-1.6758-0.35938-2.1836 0.33594l-1.8516 2.5117c-1.5547-0.17578-3.1055-0.39062-4.6445-0.67578l1.8242-2.4766c0.49219-0.69531 0.33594-1.6523-0.35156-2.1562-0.68359-0.50391-1.6445-0.36719-2.1641 0.30469l-2.7773 3.7734c-1.5391-0.21484-3.0898-0.33594-4.6445-0.36328l2.832-3.8438c0.48828-0.69531 0.33594-1.6523-0.35156-2.1602-0.68359-0.50391-1.6484-0.36719-2.168 0.30859l-4.1836 5.6758h-23.562zm71.086 15.012c-3.6289 1.4141-7.4922 2.1211-11.387 2.0859l-62.102-0.003907c-0.26562-1.8672 0.28516-3.7617 1.5117-5.1914h27.906c4.6914-0.074219 9.2344 1.3633 13.891 1.7188 4.4805 0.53125 14.926 0.26953 19.555 0.32812v-0.003906c5.6992 0.007812 11.344-1.1211 16.602-3.3203l0.67969-0.28516v0.003906c-1.7383 2.1484-4.043 3.7656-6.6562 4.668z" />
                      <text x="0.0" y="117.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000"></text><text x="0.0" y="122.5" font-size="2.5" font-weight="bold" font-family="Arbeit Regular, Helvetica, Arial-Unicode, Arial, Sans-serif" fill="#000000">from Noun Project</text>
                    </svg>

                    Chaussures
                  </a>
                </div>
              </div>
              <div class="relative group">
                <a href="<?php echo BASE_URL; ?>pages/sport.php" class="flex items-center text-gray-600 hover:text-blue-600 font-medium transition duration-300">
                  Sport
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 ml-1" :class="{'rotate-180': open}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                  </svg>
                </a>
                <div class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-300">
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Football
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                    Basketball
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Handball
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Running
                  </a>
                  <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Rugby
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
          <!-- Répétez ce bloc pour chaque catégorie (Homme, Femme, Enfants, Sports) -->
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

    <!-- Barre de recherche déroulante sticky avec autocomplétion -->
    <div id="search-bar" class="w-full bg-white transition-all duration-300 ease-in-out overflow-hidden flex items-center h-0 shadow-md border-t border-gray-200">
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

  <!-- Le reste de votre contenu ici -->

  <script>
const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>assets/js/autocomplete.js"></script>
<script src="/shopping-website/assets/js/filtreToggle.js"></script>



</body>
</html>
