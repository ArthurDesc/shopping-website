<script>
  const BASE_URL = '<?php echo BASE_URL; ?>';
</script>

<!-- Ajouter dans le _header.php -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
<script src="<?php echo url('assets/js/toast.js'); ?>"></script>
<script src="<?php echo url('assets/js/scripts.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/navbar.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/header.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/autocomplete.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/wishlist.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/cart.js'); ?>" defer></script>
<script src="https://kit.fontawesome.com/5ea815c1d0.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (basename($_SERVER['PHP_SELF']) === 'recherche.php'): ?>
  <script src="<?php echo url('assets/js/search-modal.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/wishlist-modal.js'); ?>" defer></script>
<?php endif; ?>
  
  <?php if (basename($_SERVER['PHP_SELF']) === 'wishlist.php'): ?>
  <script src="<?php echo url('assets/js/wishlist-modal.js'); ?>" defer></script>
<?php endif; ?>

<?php if (basename($_SERVER['PHP_SELF']) === 'detail.php' || basename($_SERVER['PHP_SELF']) === 'avis.php'): ?>
  <script src="<?php echo url('assets/js/detail.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/avis.js'); ?>" defer></script>
  <script type="module" src="<?php echo url('assets/js/comments.js'); ?>"></script>
  <script src="<?php echo url('assets/js/editMode.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/backoffice/uiManager.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/backoffice/categorySelector.js'); ?>" defer></script>
<?php endif; ?>

<?php if (basename($_SERVER['PHP_SELF']) === 'produit.php'): ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/2.3.1/list.min.js"></script>
  <script src="<?php echo BASE_URL; ?>assets/js/dropdown.js"></script>
  <script src="<?php echo BASE_URL; ?>assets/js/productFilter.js"></script>
  <script src="<?php echo url('assets/js/detail.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/product-modal.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/productFilterMobile.js'); ?>" defer></script>
  <script src="<?php echo url('assets/js/add-to-cart.js'); ?>" defer></script>
<?php endif; ?>

<?php if (basename($_SERVER['PHP_SELF']) === 'profil.php'): ?>
  <script src="<?php echo BASE_URL; ?>assets/js/profil.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<?php endif; ?>