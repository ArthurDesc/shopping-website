<?php
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';
require_once '../classe/WishlistManager.php';
require_once '../classe/Produit.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$wishlistManager = new WishlistManager($conn);
$wishlistItems = $wishlistManager->getWishlist($_SESSION['id_utilisateur']);

$produits = [];
while ($item = $wishlistItems->fetch_assoc()) {
    $produit = new Produit(
        $item['id_produit'],
        $item['nom'],
        $item['prix'],
        $item['image_url'],
        $item['marque'],
        $item['description'] ?? '',
        $item['stock'] ?? 0,
        $item['tailles_disponibles'] ?? '',
        $item['categories'] ?? [],
        $item['collection'] ?? ''
    );
    $produits[] = $produit;
}
?>

<main>
    <!-- Modal avec le même style que produit.php -->
    <div id="modal-container">
        <div class="modal-background bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
                <!-- Message d'erreur -->
                <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
                <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
                    <!-- Les options seront ajoutées dynamiquement -->
                </select>
                <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                    <button id="cancelBtn" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                        Annuler
                    </button>
                    <!-- Version mobile du bouton -->
                    <button id="addToCartBtnMobile" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md sm:hidden">
                        <span>Ajouter au panier</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                            <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                        </svg>
                    </button>
                    <!-- Version desktop du bouton -->
                    <div id="addToCartBtn" class="cart-add-button button-shadow hidden sm:block" data-tooltip="<?= $produit->formatPrix() ?>">
                        <div class="cart-add-button-wrapper">
                            <div class="cart-add-button-text">Ajouter au panier</div>
                            <span class="cart-add-button-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                    <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Le reste de votre contenu -->
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Ma Liste de Souhaits</h1>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($produits as $produit): ?>
                <div class="product-card list-item bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                    <div class="relative">
                        <a href="<?= url('pages/detail.php?id=' . $produit->getId()) ?>" class="product-link block">
                            <div class="relative pb-[125%]">
                                <img src="<?= BASE_URL ?>assets/images/produits/<?= $produit->getImageUrl() ?>"
                                    alt="<?= htmlspecialchars($produit->getNom()) ?>"
                                    class="absolute inset-0 w-full h-full object-cover object-top">
                            </div>
                        </a>
                        
                        <!-- Bouton Wishlist -->
                        <label class="wishlist-btn absolute bottom-4 right-4 z-10">
                            <input type="checkbox" 
                                   class="wishlist-input"
                                   checked="checked"
                                   data-product-id="<?= $produit->getId() ?>"
                            />
                            <div class="wishlist-heart">
                                <svg viewBox="0 0 256 256">
                                    <rect fill="none" height="256" width="256"></rect>
                                    <path d="M224.6,51.9a59.5,59.5,0,0,0-43-19.9,60.5,60.5,0,0,0-44,17.6L128,59.1l-7.5-7.4C97.2,28.3,59.2,26.3,35.9,47.4a59.9,59.9,0,0,0-2.3,87l83.1,83.1a15.9,15.9,0,0,0,22.6,0l81-81C243.7,113.2,245.6,75.2,224.6,51.9Z"
                                        stroke-width="20px" stroke="#000" fill="none"></path>
                                </svg>
                            </div>
                        </label>
                    </div>

                    <!-- Informations produit -->
                    <div class="p-3">
                        <h3 class="product-name text-sm font-semibold mb-1 truncate">
                            <?= htmlspecialchars($produit->getNom()) ?>
                        </h3>
                        <p class="text-xs text-gray-600 mb-1">
                            <?= htmlspecialchars($produit->getMarque()) ?>
                        </p>
                        <div class="product-price-cart-container flex justify-between items-center">
                            <p class="product-price text-sm text-blue-600 font-bold">
                                <?= $produit->formatPrix() ?>
                            </p>
                            <button type="button" 
                                    class="product-cart-button open-modal-btn flex items-center justify-center" 
                                    data-product-id="<?= $produit->getId() ?>" 
                                    data-product-price="<?= $produit->getPrix() ?>"
                                    data-product-sizes="<?= htmlspecialchars($produit->getTaillesDisponibles()) ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon">
                                    <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8ZM10.84 5.69C10.84 4.04 12.2 2.69 13.84 2.69C15.49 2.69 16.84 3.69 16.84 5.69V6.69H10.84V5.69ZM23.82 18.41L22.39 8.41C22.25 7.43 21.41 6.69 20.41 6.69H18.84V5.69C18.84 2.69 16.6 0.69 13.84 0.69C11.08 0.69 8.84 2.93 8.84 5.69V6.69H7.57C6.58 6.69 5.43 7.43 5.29 8.41L3.86 18.41C3.69 19.62 4.62 20.69 5.84 20.69H21.84C23.06 20.69 23.99 19.62 23.82 18.41Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($produits)): ?>
            <div class="text-center py-12">
                <p class="text-gray-600 mb-4">Votre liste de souhaits est vide</p>
                <a href="<?= BASE_URL ?>pages/produit.php" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Découvrir nos produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</main>
    <script>
        function removeFromWishlist(id_produit) {
            fetch('/shopping-website/ajax/wishlist_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'remove',
                        id_produit: id_produit
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }
    </script>

    <?php require_once '../includes/_footer.php'; ?>