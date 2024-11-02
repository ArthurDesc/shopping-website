<footer class="bg-gradient-to-b from-blue-400 to-blue-600 text-white py-6 px-4 mt-12 font-sans loading">  <div class="container mx-auto max-w-6xl md:px-4 lg:px-8">
    <div class="flex flex-col items-center md:flex-row md:justify-between md:items-start mb-6">
      <div class="mb-4 md:mb-0 text-center md:text-left">
        <img src="<?php echo url('assets/images/LogoBlanc.png'); ?>" alt="Fitmode" class="h-7 w-auto mb-4 mx-auto md:mx-0">
        <nav class="text-sm">
          <a href="<?php echo url('pages/profil.php'); ?>" class="hover:underline block mb-2">Mon profil</a>
          <a href="<?php echo url('pages/conditions.php'); ?>" class="hover:underline block">Conditions d'utilisations</a>
        </nav>
      </div>
      <div class="flex flex-col items-center md:items-end">
        <div class="flex justify-center space-x-2 mb-4">
          <img src="<?php echo url('assets/images/LogoCB.jpg'); ?>" alt="CB" class="h-6 w-auto">
          <img src="<?php echo url('assets/images/LogoMastercard.png'); ?>" alt="Mastercard" class="h-6 w-auto">
          <img src="<?php echo url('assets/images/LogoVisa.png'); ?>" alt="Visa" class="h-6 w-auto">
          <img src="<?php echo url('assets/images/LogoPaypal.jpg'); ?>" alt="PayPal" class="h-6 w-auto">
        </div>
        <div class="text-xs text-center">
          <p>&copy; <?php echo date('Y'); ?> Fitmode™. Tous droits réservés</p>
        </div>
      </div>
    </div>
  </div>
</footer>
