<?php
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';
require_once '../classe/WishlistManager.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

$wishlistManager = new WishlistManager($conn);
$wishlistItems = $wishlistManager->getWishlist($_SESSION['id_utilisateur']);
?>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Ma Liste de Souhaits</h1>

        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php while ($item = $wishlistItems->fetch_assoc()): ?>
                <div class="product-card list-item bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                    <!-- Conteneur d'image avec position relative pour le bouton wishlist -->
                    <div class="relative">
                        <a href="<?= url('pages/detail.php?id=' . $item['id_produit']) ?>" class="product-link block">
                            <div class="relative pb-[125%]">
                                <img src="<?= BASE_URL ?>assets/images/produits/<?= $item['image_url'] ?>"
                                    alt="<?= htmlspecialchars($item['nom']) ?>"
                                    class="absolute inset-0 w-full h-full object-cover object-top">
                            </div>
                        </a>
                        
                        <!-- Bouton Wishlist -->
                        <label class="wishlist-btn absolute bottom-4 right-4 z-10">
                            <input type="checkbox" 
                                   class="wishlist-input"
                                   checked="checked"
                                   data-product-id="<?php echo $item['id_produit']; ?>"
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
                            <?= htmlspecialchars($item['nom']) ?>
                        </h3>
                        <p class="text-xs text-gray-600 mb-1">
                            <?= htmlspecialchars($item['marque']) ?>
                        </p>
                        <div class="flex justify-between items-center"></div>
                            <p class="product-price text-sm text-blue-600 font-bold">
                                <?= number_format($item['prix'], 2) ?> €
                            </p>
                            <button type="button" 
                                class="product-cart-button open-modal-btn flex items-center justify-center" 
                                data-product-id="<?= $item['id_produit'] ?>" 
                                data-product-price="<?= $item['prix'] ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon">
                                    <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($wishlistItems->num_rows === 0): ?>
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