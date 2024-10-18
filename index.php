<?php
include './includes/session.php';
include './includes/_header.php';
?>

<main class="flex-grow">
  <div class="swiper-container relative w-full mx-auto overflow-hidden h-[calc(100vh-55px)]">
    <div class="swiper-wrapper h-full">
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/pikaso_edit.png'); ?>" alt="Image 3" class="w-full h-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/basketSlide2.png'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 15%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/rugby4.jpeg'); ?>" alt="Image 2" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/running.jpeg'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 20%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-8 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Performance</h2>
        </div>
      </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
    
    <!-- Nouveau bouton d'ancrage -->
    <div class="absolute bottom-16 sm:bottom-12 md:bottom-8 left-1/2 transform -translate-x-1/2 z-10">
      <a href="#nouveautes" class="bg-white bg-opacity-80 text-blue-600 px-3 py-2 sm:px-4 sm:py-2 text-xs sm:text-sm md:text-base rounded-full shadow-md hover:bg-blue-600 hover:text-white transition duration-300 flex items-center space-x-2">
        <span>Découvrir plus</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 sm:h-4 sm:w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>
  </div>

  <h2 id="nouveautes" class="text-2xl font-normal mb-4 mt-12 ml-4">Les nouveautés</h2>
  <div class="custom-scroll">
    <div class="flex space-x-12 p-4 w-max">
      <?php
      $marques = ['Puma', 'Adidas', 'Nike', 'NorthFace', 'underarmour'];
      foreach ($marques as $marque) :
      ?>
        <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
          <a href="<?php echo url('pages/produit.php?marque=' . $marque); ?>" class="block relative">
            <img src="<?php echo url('assets/images/' . strtolower($marque) . '.png'); ?>" alt="<?php echo $marque; ?>" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-blue-600 to-transparent opacity-0 group-hover:opacity-50 transition duration-300"></div>
            <div class="absolute bottom-2 right-2 z-10">
              <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-blue-600 group-hover:text-white"><?php echo $marque; ?></button>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <h2 class="text-2xl font-normal mb-4 mt-12 ml-4">Sports</h2>
  <div class="custom-scroll">
    <div class="flex space-x-12 p-4 w-max">
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?categorie=homme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/football.jpg'); ?>" alt="Football" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Football</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?categorie=homme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/basketball.jpg'); ?>" alt="Basketball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Basketball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?categorie=homme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/running.jpg'); ?>" alt="Running" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Running</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?categorie=homme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/handball.jpg'); ?>" alt="Handball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Handball</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?categorie=homme'); ?>" class="block relative">
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
        <a href="<?php echo url('pages/produit.php?collection=Homme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/homme.jpg'); ?>" alt="Homme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Homme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?collection=Femme'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/femme.jpg'); ?>" alt="Femme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Femme</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?collection=Enfant'); ?>" class="block relative">
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
        <a href="<?php echo url('pages/produit.php?promotion=camp'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/camp.jpeg'); ?>" alt="Homme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?promotion=airmax-sunder'); ?>" class="block relative">
          <img src="<?php echo url('assets/images/airmax-sunder.jpg'); ?>" alt="Femme" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-110">
          <div class="absolute bottom-2 right-2">
            <button class="bg-white text-blue-600 text-sm px-4 py-1 rounded-full transition duration-300 shadow-md hover:bg-blue-600 hover:text-white">Promotion</button>
          </div>
        </a>
      </div>
      <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
        <a href="<?php echo url('pages/produit.php?promotion=kids'); ?>" class="block relative">
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

<script>
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();

        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);

        if (targetElement) {
            const headerOffset = 10; // Hauteur de votre header fixe
            const carouselHeight = document.querySelector('.swiper-container').offsetHeight;
            const windowHeight = window.innerHeight;
            
            // Calculer la position de défilement pour que le bord inférieur du carrousel soit au bord supérieur de l'écran
            const scrollPosition = targetElement.offsetTop - windowHeight + carouselHeight + headerOffset;

            gsap.to(window, {
                duration: 1, 
                scrollTo: {
                    y: scrollPosition,
                    autoKill: false
                },
                ease: "power2.inOut"
            });
        }
    });
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/ScrollToPlugin.min.js"></script>

<style>
  .nouveautes-container {
    position: relative;
    overflow: visible !important;
  }
  
  .nouveautes-container::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    background-color: #3B82F6;
    transform: translateY(-50%);
    z-index: -1;
  }
  
  .nouveautes-container .flex > div {
    position: relative;
  }
  
  .nouveautes-container .flex > div::before {
    content: '';
    position: absolute;
    top: 50%;
    left: -6px;
    width: 12px;
    height: 12px;
    background-color: #3B82F6;
    border-radius: 50%;
    transform: translateY(-50%);
    z-index: 1;
  }
  
  .nouveautes-container .flex > div:first-child::before {
    display: none;
  }
</style>
