<?php include './includes/session.php'; ?>


<?php include './includes/_header.php'; ?>
<main class="flex-grow">

  
  <div class="swiper-container relative max-w-screen-xl mx-auto">
  <div class="swiper-wrapper">
    <div class="swiper-slide relative">
      <img src="<?php echo BASE_URL; ?>assets/images/slide1.png" alt="Image 1" class="w-full h-full object-cover object-top">
      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
      <div class="absolute bottom-8 left-8 text-white">
        <h2 class="text-3xl font-bold mb-2">T-shirts homme</h2>
      </div>
    </div>
    <div class="swiper-slide relative">
      <img src="<?php echo BASE_URL; ?>assets/images/slide2.jpg" alt="Image 2" class="w-full h-full object-cover object-top">
      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
      <div class="absolute bottom-8 left-8 text-white">
        <h2 class="text-3xl font-bold mb-2">Gilets femme</h2>
      </div>
    </div>
    <div class="swiper-slide relative">
      <img src="<?php echo BASE_URL; ?>assets/images/slide3.jpg" alt="Image 3" class="w-full h-full object-cover object-top">
      <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
      <div class="absolute bottom-8 left-8 text-white">
        <h2 class="text-3xl font-bold mb-2">Oui</h2>
      </div>
    </div>
  </div>
  <div class="swiper-pagination"></div>
</div>

<h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Les nouveautés</h2>

  <div class="overflow-x-auto">
    <div class="flex space-x-12 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/octa.png" alt="Nocta" class="w-full h-64 object-cover rounded-lg">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Nocta</button>
          </div>
        </a>
      </div>
      <!-- Répétez ce bloc pour chaque nouveauté -->
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/adidas.png" alt="Adidas" class="w-full h-64 object-cover rounded-lg">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Adidas</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/nike.png" alt="Nike" class="w-full h-64 object-cover rounded-lg">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Nike</button>
          </div>
        </a>
      </div>
      <!-- Ajoutez d'autres nouveautés ici -->
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Sports</h2>
  <div class="overflow-x-auto">
    <div class="flex space-x-8 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/football.jpg" alt="Football" class="w-full h-64 object-cover rounded-lg">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Football</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/basketball.jpg" alt="Basketball" class="w-full h-64 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Basketball</button>
        </div>
      </div>
      <div class="flex-shrink-0 w-64 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/running.jpg" alt="Running" class="w-full h-64 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Running</button>
        </div>
      </div>
      <div class="flex-shrink-0 w-64 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/handball.jpg" alt="Handball" class="w-full h-64 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Handball</button>
        </div>
      </div>
      <div class="flex-shrink-0 w-64 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/rugby.jpg" alt="Rugby" class="w-full h-64 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Rugby</button>
        </div>
      </div>
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Collection</h2>
  <div class="overflow-x-auto">
    <div class="flex space-x-8 p-4 w-max">
      <div class="flex-shrink-0 w-80 relative">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/homme.jpg" alt="Homme" class="w-full h-44 object-cover rounded-lg">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Homme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/femme.jpg" alt="Femme" class="w-full h-44 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Femme</button>
        </div>
      </div>
      <div class="flex-shrink-0 w-80 relative">
        <img src="<?php echo BASE_URL; ?>assets/images/enfant.jpg" alt="Enfant" class="w-full h-44 object-cover rounded-lg">
        <div class="absolute bottom-2 right-2">
          <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full hover:bg-gray-100 transition duration-300 shadow-md">Enfant</button>
        </div>
      </div>
    </div>
  </div>






</main>
<?php include './includes/_footer.php'; ?>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
</body>

</html>