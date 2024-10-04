<?php include './includes/session.php'; ?>


<?php include './includes/_header.php'; ?>

<div class="swiper-container relative max-w-screen-xl mx-auto">
  <div class="swiper-wrapper">
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide1.png" alt="Image 1" class="w-full h-auto"></div>
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide2.png" alt="Image 2" class="w-full h-auto"></div>
    <div class="swiper-slide"><img src="<?php echo BASE_URL; ?>assets/images/slide3.png" alt="Image 3" class="w-full h-auto"></div>
  </div>
  <div class="swiper-pagination"></div>
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
      });
    }, 100);
  });
</script>
</body>

</html>