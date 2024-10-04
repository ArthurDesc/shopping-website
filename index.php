<?php include './includes/session.php'; ?>


<?php include './includes/_header.php'; ?>

<div class="swiper-container relative">
  <div class="swiper-wrapper">
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide1.png" alt="Image 1"></div>
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide2.png" alt="Image 2"></div>
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide3.png" alt="Image 3"></div>
  </div>
  <!-- Add Pagination -->
  <div class="swiper-pagination"></div>
  <!-- Add Navigation -->
  <div class="swiper-button-next z-10">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 19.5 8.25 12l7.5-7.5" />
    </svg>
  </div>
  <div class="swiper-button-prev z-10">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
    </svg>
  </div>
</div>

<section class="nouveautes py-8">
  <h2 class="text-center text-2xl font-bold mb-4">Les nouveautés</h2>
  <div class="flex justify-center space-x-4">
    <div><img src="<?php echo BASE_URL; ?>assets/images/new1.png" alt="Nouveauté 1"></div>
    <div><img src="<?php echo BASE_URL; ?>assets/images/new2.png" alt="Nouveauté 2"></div>
  </div>
</section>



<?php include './includes/_footer.php'; ?>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
</body>

</html>