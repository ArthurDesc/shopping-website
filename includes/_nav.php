<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}
?>

<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <!-- Logo et Navigation principale -->
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?php echo url(''); ?>">
                        <img class="h-8 w-auto" src="<?php echo url('assets/images/logo.png'); ?>" alt="Logo">
                    </a>
                </div>
                
                <!-- Liens de navigation -->
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="<?php echo url('pages/produit.php?collections=Homme'); ?>" 
                           class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Homme
                        </a>
                        <a href="<?php echo url('pages/produit.php?collections=Femme'); ?>" 
                           class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Femme
                        </a>
                        <?php if (isset($_SESSION['user'])): ?>
                            <a href="<?php echo url('pages/profil.php'); ?>" 
                               class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Mon Compte
                            </a>
                            <a href="<?php echo url('pages/wishlist.php'); ?>" 
                               class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Wishlist
                            </a>
                        <?php else: ?>
                            <a href="<?php echo url('pages/connexion.php'); ?>" 
                               class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Connexion
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo url('pages/panier.php'); ?>" 
                           class="text-gray-600 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Panier (<span id="cart-count"><?php echo isset($_SESSION['panier']) ? array_sum($_SESSION['panier']) : 0; ?></span>)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav> 