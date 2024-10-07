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

<h2 class="text-2xl font-bold mb-8 mt-12">Les nouveautés</h2>

  <!-- Nouveau carousel avec des blocs cliquables -->
  <div class="swiper-container-blocks relative max-w-screen-xl mx-auto mt-8">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <div class="relative w-52 h-auto"> <!-- Réduit la hauteur de 64 à 48 -->
          <img src="<?php echo BASE_URL; ?>assets/images/nocta.png" alt="Nocta" class="w-full h-full object-cover">
          <div class="absolute bottom-2 right-2"> <!-- Réduit l'espacement de 4 à 2 -->
            <button class="bg-white text-black text-sm px-3 py-1 rounded-full hover:bg-gray-200 transition duration-300">Découvrir Nocta</button> <!-- Réduit la taille du texte et le padding -->
          </div>
        </div>
      </div>
      <!-- Ajoutez d'autres slides ici avec la même structure -->
      <div class="swiper-slide">
        <div class="relative w-full h-48"> <!-- Réduit la hauteur de 64 à 48 -->
          <img src="<?php echo BASE_URL; ?>assets/images/autre_collection.jpg" alt="Autre Collection" class="w-full h-full object-cover">
          <div class="absolute bottom-2 right-2"> <!-- Réduit l'espacement de 4 à 2 -->
            <button class="bg-white text-black text-sm px-3 py-1 rounded-full hover:bg-gray-200 transition duration-300">Voir la collection</button> <!-- Réduit la taille du texte et le padding -->
          </div>
        </div>
      </div>
      <!-- Répétez pour d'autres collections -->
    </div>
    <div class="swiper-pagination"></div>
  </div>

<section class="nouveautes py-8 max-w-screen-xl mx-auto">
  <div class="swiper-container-nouveautes">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <div class="relative group">
          <img src="<?php echo BASE_URL; ?>assets/images/nocta_nouveaute.jpg" alt="Nocta" class="w-full h-auto rounded-lg shadow-md">
          <div class="absolute bottom-4 left-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <button class="bg-white text-black px-6 py-2 rounded-full shadow-lg hover:bg-gray-100 transition duration-300">
              Nocta
            </button>
          </div>
        </div>
      </div>
      <!-- Ajoutez d'autres slides ici avec la même structure -->
    </div>
  </div>
</section>



</main>
<?php include './includes/_footer.php'; ?>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
</body>

</html>