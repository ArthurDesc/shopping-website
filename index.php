<?php
include './includes/session.php';
include './includes/_header.php';
?>

<main class="flex-grow pt-[60px]">
  <div class="swiper-container relative w-full mx-auto overflow-hidden carousel-height -mt-[60px]">
    <div class="swiper-wrapper h-full">
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/pikaso_edit.png'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 60%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-4 sm:bottom-8 left-4 sm:left-8 text-white">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/basketSlide2.png'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 15%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-4 sm:bottom-8 left-4 sm:left-8 text-white">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/rugby4.jpeg'); ?>" alt="Image 2" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-4 sm:bottom-8 left-4 sm:left-8 text-white">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Confort</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/running.jpeg'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 20%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-4 sm:bottom-8 left-4 sm:left-8 text-white">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Les nouveautés</h2>
  <div class="custom-scroll">
    <div class="flex space-x-12 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/octa.png'); ?>" alt="Nocta" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Nocta</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/adidas.png'); ?>" alt="Adidas" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Adidas</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/nike.png'); ?>" alt="Nike" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Nike</button>
          </div>
        </a>
      </div>
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Sports</h2>
  <div class="custom-scroll">
    <div class="flex space-x-12 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/football.jpg'); ?>" alt="Football" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Football</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/basketball.jpg'); ?>" alt="Basketball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Basketball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/running.jpg'); ?>" alt="Running" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Running</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/handball.jpg'); ?>" alt="Handball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Handball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/rugby.jpg'); ?>" alt="Rugby" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Rugby</button>
          </div>
        </a>
      </div>
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Collections</h2>
  <div class="custom-scroll">
    <div class="flex space-x-8 p-4 w-max">
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/homme.jpg'); ?>" alt="Homme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Homme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/femme.jpg'); ?>" alt="Femme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Femme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/enfant.jpg'); ?>" alt="Enfant" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Enfant</button>
          </div>
        </a>
      </div>
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Promotions</h2>
  <div class="custom-scroll">
    <div class="flex space-x-8 p-4 w-max">
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/camp.jpeg'); ?>" alt="Homme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/airmax-sunder.jpg'); ?>" alt="Femme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/kids.jpg'); ?>" alt="Enfant" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
    </div>
  </div>
</main>

<?php include './includes/_footer.php'; ?>




