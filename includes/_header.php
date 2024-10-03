<header class="bg-white shadow-sm !important">
  <nav class="container mx-auto px-4 py-3 flex items-center justify-between bg-white !important">
    <!-- Menu hamburger à l'extrême gauche -->
    <button id="menuToggle" class="text-gray-800" type="button" aria-label="Toggle navigation">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
      </svg>
    </button>

    <!-- Liens de navigation (visibles sur desktop) -->
    <div class="hidden md:flex space-x-4">
      <a href="<?php echo BASE_URL; ?>pages/homme.php" class="text-gray-800 hover:text-black no-underline">Homme</a>
      <a href="<?php echo BASE_URL; ?>pages/femme.php" class="text-gray-800 hover:text-black no-underline">Femme</a>
      <a href="<?php echo BASE_URL; ?>pages/enfants.php" class="text-gray-800 hover:text-black no-underline">Enfants</a>
    </div>

    <!-- Logo au centre -->
    <a href="<?php echo BASE_URL; ?>" class="flex-shrink-0">
      <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Fitmode Logo" class="h-11 w-auto">
    </a>

    <!-- Icônes à droite -->
    <div class="flex items-center space-x-4">
      <a href="<?php echo BASE_URL; ?>pages/panier.php" class="text-gray-800 hover:text-black">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
      </a>
      <?php if (is_logged_in()): ?>
        <a href="<?php echo BASE_URL; ?>pages/profil.php" class="text-gray-800 hover:text-black">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </a>
      <?php else: ?>
        <a href="<?php echo BASE_URL; ?>pages/auth.php" class="text-gray-800 hover:text-black">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
          </svg>
        </a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Menu burger (caché par défaut) -->
  <div id="burgerMenu" class="fixed inset-0 bg-gray-800 bg-opacity-75 z-50 hidden">
    <div class="bg-white w-64 h-full overflow-y-auto">
      <div class="p-4">
        <button id="closeMenu" class="text-gray-800 hover:text-black">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
        <ul class="mt-4">
          <li><a href="<?php echo BASE_URL; ?>pages/homme.php" class="block py-2 text-gray-800 hover:text-black">Homme</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/femme.php" class="block py-2 text-gray-800 hover:text-black">Femme</a></li>
          <li><a href="<?php echo BASE_URL; ?>pages/enfants.php" class="block py-2 text-gray-800 hover:text-black">Enfants</a></li>
          <!-- Ajoutez d'autres liens de menu ici -->
        </ul>
      </div>
    </div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menuToggle');
  const closeMenu = document.getElementById('closeMenu');
  const burgerMenu = document.getElementById('burgerMenu');

  menuToggle.addEventListener('click', function() {
    burgerMenu.classList.remove('hidden');
  });

  closeMenu.addEventListener('click', function() {
    burgerMenu.classList.add('hidden');
  });

  // Fermer le menu si on clique en dehors
  burgerMenu.addEventListener('click', function(e) {
    if (e.target === burgerMenu) {
      burgerMenu.classList.add('hidden');
    }
  });
});
</script>