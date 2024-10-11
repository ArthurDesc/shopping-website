<?php include './includes/session.php'; ?>
<?php include './includes/_header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  
<style>
  .swiper-button-next,
  .swiper-button-prev {
    width: 40px !important;
    height: 40px !important;
    background-color: rgba(255, 255, 255, 0.7);
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
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232563EB'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M8.25 4.5l7.5 7.5-7.5 7.5' /%3E%3C/svg%3E");
  }
  .swiper-button-prev::after {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%232563EB'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M15.75 19.5L8.25 12l7.5-7.5' /%3E%3C/svg%3E");
  }

  .custom-scroll {
    scrollbar-width: none; /* Pour Firefox */
    -ms-overflow-style: none; /* Pour Internet Explorer et Edge */
    overflow-x: auto;
  }

  .custom-scroll::-webkit-scrollbar {
    display: none; /* Pour Chrome, Safari et Opera */
  }

  .scroll-button {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 10;
  }

  .scroll-left {
    left: 10px;
  }

  .scroll-right {
    right: 10px;
  }

  /* Styles pour les écrans larges (ordinateurs) */
  @media (min-width: 1024px) {
    .custom-scroll {
      scrollbar-width: none; /* Pour Firefox */
      -ms-overflow-style: none; /* Pour Internet Explorer et Edge */
    }

    .custom-scroll::-webkit-scrollbar {
      display: none; /* Pour Chrome, Safari et Opera */
    }
  }

  /* Styles pour les écrans mobiles */
  @media (max-width: 1023px) {
    .custom-scroll {
      overflow-x: auto;
    }
  }
</style>
  

<main class="flex-grow">

  
  <div class="swiper-container relative w-full mx-auto h-[calc(80vh-4rem)] sm:h-64 md:h-80 lg:h-[28rem] xl:h-[32rem] overflow-hidden">
    <div class="swiper-wrapper h-full">
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit (2).jpeg" alt="Image 1" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">Élasticité</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit(3).jpeg" alt="Image 2" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">Confort</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo BASE_URL; ?>assets/images/pikaso_edit.png" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 60%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 left-8 text-white">
          <h2 class="text-3xl font-bold mb-2">Performance</h2>
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
  <div class="custom-scroll">
    <div class="flex space-x-12 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/octa.png" alt="Nocta" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Nocta</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/adidas.png" alt="Adidas" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Adidas</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/nike.png" alt="Nike" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
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
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/football.jpg" alt="Football" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Football</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/basketball.jpg" alt="Basketball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Basketball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/running.jpg" alt="Running" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Running</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/handball.jpg" alt="Handball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Handball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/rugby.jpg" alt="Rugby" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
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
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/homme.jpg" alt="Homme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Homme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/femme.jpg" alt="Femme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Femme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/enfant.jpg" alt="Enfant" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
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
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/camp.jpeg" alt="Homme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/airmax-sunder.jpg" alt="Femme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="#" class="block relative">
          <img src="<?php echo BASE_URL; ?>assets/images/kids.jpg" alt="Enfant" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
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