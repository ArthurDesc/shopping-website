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
        <div class="absolute bottom-24 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Détermination</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/basketSlide2.png'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 15%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-24 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Compétition</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/rugby4.jpeg'); ?>" alt="Image 2" class="w-full h-full object-cover sm:object-top">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-24 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Aventure</h2>
        </div>
      </div>
      <div class="swiper-slide relative">
        <img src="<?php echo url('assets/images/running.jpeg'); ?>" alt="Image 3" class="w-full h-full object-cover" style="object-position: center 20%;">
        <div class="absolute inset-x-0 bottom-0 h-1/4 bg-gradient-to-t from-black to-transparent opacity-50"></div>
        <div class="absolute bottom-24 sm:bottom-12 left-6 sm:left-10 text-white z-10 max-w-[80%]">
          <h2 class="text-4xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-7xl font-bold mb-2">Bien être</h2>
        </div>
      </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev !hidden sm:!flex w-10 h-10 items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </div>
    <div class="swiper-button-next !hidden sm:!flex w-10 h-10 items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </div>
    
    <div class="absolute bottom-8 sm:bottom-12 md:bottom-8 left-1/2 transform -translate-x-1/2 z-10">
      <a href="#nouveautes" class="bg-blue-600 bg-opacity-80 text-white px-6 py-3 sm:px-4 sm:py-2 text-base sm:text-sm md:text-base rounded-full shadow-md hover:bg-blue-600 hover:text-white transition duration-300 flex items-center space-x-3 sm:space-x-2">
        <span>Découvrir plus</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-4 sm:w-4 md:h-5 md:w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
      </a>
    </div>
  </div>

  <div id="nouveautes" class="section-container relative px-4 mt-12">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-2xl font-normal">Les nouveautés du moment</h2>
      <div class="scroll-buttons hidden sm:hidden md:flex gap-2">
        <button class="scroll-left-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button class="scroll-right-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
    <div class="scroll-container overflow-x-auto scrollbar-hide">
      <div class="flex space-x-8 p-4 w-max">
        <?php
        // Récupérer toutes les marques de la base de données
        $query = "SELECT DISTINCT marque FROM produits ORDER BY marque";
        $result = mysqli_query($conn, $query);
        $all_marques = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $all_marques[] = $row['marque'];
        }

        // Liste des marques que vous voulez afficher dans l'index
        $marques_to_display = ['Puma', 'Adidas', 'Nike', 'NorthFace', 'UnderArmour'];

        // Ne garder que les marques qui existent dans la base de données
        $marques = array_intersect($marques_to_display, $all_marques);

        foreach ($marques as $marque) :
        ?>
          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
            <a href="<?php echo url('pages/produit.php?marques=' . urlencode($marque)); ?>" class="block relative">
              <img src="<?php echo url('assets/images/' . strtolower($marque) . '.png'); ?>" alt="<?php echo $marque; ?>" class="w-full h-80 object-cover rounded-lg transition duration-300 group-hover:scale-105">
              <div class="absolute inset-0 bg-gradient-to-t from-blue-600 to-transparent opacity-0 group-hover:opacity-50 transition duration-300"></div>
              <div class="absolute bottom-2 right-2 z-10">
                <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600"><?php echo $marque; ?></button>
                </div>
              </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="section-container relative px-4">
    <div class="flex items-center justify-between mb-4 mt-12">
      <h2 class="text-2xl font-normal">Découvrez nos univers sportifs</h2>
      <div class="scroll-buttons hidden sm:hidden md:flex gap-2">
        <button class="scroll-left-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button class="scroll-right-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
    <div class="scroll-container overflow-x-auto scrollbar-hide">
      <div class="flex space-x-12 p-4 w-max">
          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
              <a href="<?php echo url('pages/produit.php?categories=19'); ?>" class="block relative">
                  <img src="<?php echo url('assets/images/football.jpg'); ?>" alt="Football" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
                  <div class="absolute bottom-2 right-2 z-10">
                      <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Football</button>
                  </div>
              </a>
          </div>

          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
              <a href="<?php echo url('pages/produit.php?categories=20'); ?>" class="block relative">
                  <img src="<?php echo url('assets/images/rugby.jpg'); ?>" alt="Rugby" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
                  <div class="absolute bottom-2 right-2 z-10">
                      <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Rugby</button>
                  </div>
              </a>
          </div>

          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
              <a href="<?php echo url('pages/produit.php?categories=21'); ?>" class="block relative">
                  <img src="<?php echo url('assets/images/running.jpg'); ?>" alt="Running" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
                  <div class="absolute bottom-2 right-2 z-10">
                      <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Running</button>
                  </div>
              </a>
          </div>

          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
              <a href="<?php echo url('pages/produit.php?categories=22'); ?>" class="block relative">
                  <img src="<?php echo url('assets/images/handball.jpg'); ?>" alt="Handball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
                  <div class="absolute bottom-2 right-2 z-10">
                      <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Handball</button>
                  </div>
              </a>
          </div>

          <div class="flex-shrink-0 w-64 relative shadow-lg rounded-lg overflow-hidden group">
              <a href="<?php echo url('pages/produit.php?categories=23'); ?>" class="block relative">
                  <img src="<?php echo url('assets/images/basketball.jpg'); ?>" alt="Basketball" class="w-full h-64 object-cover rounded-lg transition duration-300 group-hover:scale-110">
                  <div class="absolute bottom-2 right-2 z-10">
                      <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Basketball</button>
                  </div>
              </a>
          </div>
      </div>
    </div>
  </div>

  <div class="section-container relative px-4">
    <div class="flex items-center justify-between mb-4 mt-12">
      <h2 class="text-2xl font-normal">Trouvez votre style parmi nos collections</h2>
      <div class="scroll-buttons hidden sm:hidden md:flex gap-2">
        <button class="scroll-left-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button class="scroll-right-btn w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors duration-200">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>
    </div>
    <div class="scroll-container overflow-x-auto scrollbar-hide">
      <div class="flex space-x-8 p-4 w-max">
        <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
          <a href="<?php echo url('pages/produit.php?collections=Homme'); ?>" class="block relative">
            <img src="<?php echo url('assets/images/homme.jpg'); ?>" alt="Homme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
            <div class="absolute bottom-2 right-2">
              <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Homme</button>
            </div>
          </a>
        </div>
        <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
          <a href="<?php echo url('pages/produit.php?collections=Femme'); ?>" class="block relative">
            <img src="<?php echo url('assets/images/femme.jpg'); ?>" alt="Femme" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
            <div class="absolute bottom-2 right-2">
              <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Femme</button>
            </div>
          </a>
        </div>
        <div class="flex-shrink-0 w-80 relative shadow-lg rounded-lg overflow-hidden group">
          <a href="<?php echo url('pages/produit.php?collections=Enfant'); ?>" class="block relative">
            <img src="<?php echo url('assets/images/enfant.jpg'); ?>" alt="Enfant" class="w-full h-44 object-cover rounded-lg transition duration-300 group-hover:scale-110">
            <div class="absolute bottom-2 right-2">
              <button class="bg-blue-600 text-white text-sm px-4 py-1 rounded-full transition duration-300 shadow-md group-hover:bg-white group-hover:text-blue-600">Enfant</button>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>

 
</main class="mb-8">

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

  .scrollbar-hide::-webkit-scrollbar {
    display: none;
  }

  .scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const scrollAmount = 300;

  // Fonction pour vérifier si le défilement est nécessaire
  function checkScrollable(container, buttonsContainer) {
    // Ajouter une vérification de la largeur d'écran (768px est la breakpoint md de Tailwind par défaut)
    const isMobileView = window.innerWidth < 768;
    const isScrollable = container.scrollWidth > container.clientWidth;
    
    // Cacher les boutons sur mobile ou si le contenu ne déborde pas
    buttonsContainer.style.display = (isMobileView || !isScrollable) ? 'none' : 'flex';
  }

  // Pour chaque section
  document.querySelectorAll('.section-container').forEach(section => {
    const container = section.querySelector('.scroll-container');
    const buttonsContainer = section.querySelector('.scroll-buttons');
    
    if (container && buttonsContainer) {
      // Vérification initiale
      checkScrollable(container, buttonsContainer);

      // Vérification lors du redimensionnement
      window.addEventListener('resize', () => {
        checkScrollable(container, buttonsContainer);
      });

      // Gestionnaires de clics existants
      const leftBtn = buttonsContainer.querySelector('.scroll-left-btn');
      const rightBtn = buttonsContainer.querySelector('.scroll-right-btn');

      if (leftBtn) {
        leftBtn.addEventListener('click', function() {
          container.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
          });
        });
      }

      if (rightBtn) {
        rightBtn.addEventListener('click', function() {
          container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
          });
        });
      }
    }
  });
});
</script>




