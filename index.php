<?php include './includes/session.php'; ?>


<?php include './includes/_header.php'; ?>
<style>
  .swiper-button-next,
  .swiper-button-prev {
    width: 40px !important;
    height: 40px !important;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .swiper-button-next::after,
  .swiper-button-prev::after {
    content: '';
    width: 24px;
    height: 24px;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
  }
  .swiper-button-next::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M8.25 4.5l7.5 7.5-7.5 7.5' /%3E%3C/svg%3E");
  }
  .swiper-button-prev::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M15.75 19.5L8.25 12l7.5-7.5' /%3E%3C/svg%3E");
  }
</style>

<main class="flex-grow">

  
  <div class="swiper-container relative w-full mx-auto h-[calc(80vh-4rem)] sm:h-64 md:h-80 lg:h-[28rem] xl:h-[32rem] overflow-hidden">
    <div class="swiper-wrapper h-full">
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit (2).jpeg" alt="Image 1" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">T-shirts homme</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit(3).jpeg" alt="Image 2" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">Gilets femme</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit.png" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 60%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">Oui</h2>
        </div>
      </div>
    </div>
    <div class="swiper-pagination"></div>
    <!-- Flèches de navigation -->
    <div class="hidden sm:block">
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
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