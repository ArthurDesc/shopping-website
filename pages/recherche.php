<?php
ob_start(); // Démarre la mise en mémoire tampon de sortie
require_once '../includes/_db.php';
require_once '../includes/_header.php';
require_once '../classe/WishlistManager.php'; // Ajoutez cette ligne

// Initialiser le WishlistManager
$wishlistManager = new WishlistManager($conn);

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

if (isset($_POST['ajouter_au_panier']) && isset($_POST['id_produit'])) {
    $id_produit = $_POST['id_produit'];
    $panier->ajouter($id_produit);
    // Redirigez vers la même page pour éviter les soumissions multiples
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$search = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ?");
$searchTerm = '%' . $search . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Définir le chemin de base pour les images des produits
$image_base_path = '../assets/images/produits/';

// Ajouter une variable pour le nombre de résultats
$nombre_de_resultats = $result->num_rows; // Compte le nombre de résultats
?>
<main>

    <div class="container mx-auto px-4">
        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center">
            <div class="flex items-center gap-3">
                <h2 class="text-xl sm:text-2xl montserrat-bold text-blue-600">Résultats de recherche pour "<?php echo htmlspecialchars($search); ?>"</h2>
                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                    <?= $nombre_de_resultats ?> résultat<?= $nombre_de_resultats > 1 ? 's' : '' ?>
                </span>
            </div>
            <form method="get" action="" class="flex items-center mt-4 sm:mt-0">
                <div class="wave-group">
                    <input required type="text" name="q" class="input" value="<?php echo htmlspecialchars($search); ?>">
                    <span class="bar"></span>
                    <label class="label">
                        <span class="label-char" style="--index: 0">R</span>
                        <span class="label-char" style="--index: 1">e</span>
                        <span class="label-char" style="--index: 2">c</span>
                        <span class="label-char" style="--index: 3">h</span>
                        <span class="label-char" style="--index: 4">e</span>
                        <span class="label-char" style="--index: 5">r</span>
                        <span class="label-char" style="--index: 6">c</span>
                        <span class="label-char" style="--index: 7">h</span>
                        <span class="label-char" style="--index: 8">e</span>
                        <span class="label-char" style="--index: 9">r</span>
                    </label>
                    <button type="submit" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
        <!-- Champ de recherche avec autocomplétion -->


        <section class="products_list">
            <?php
            if ($result->num_rows > 0) {
                echo '<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
                while ($row = $result->fetch_assoc()) {
                    $image_url = $image_base_path . ($row['image_url'] ?? 'default_product.jpg');

                    if (!file_exists($image_url) || empty($row['image_url'])) {
                        $image_url = $image_base_path . 'default_product.jpg';
                    }
            ?>
                    <div class="product-card list-item bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full">
                        <div class="relative">
                            <a href="<?= url('pages/detail.php?id=' . $row['id_produit']) ?>" class="product-link block">
                                <div class="relative pb-[125%]">
                                    <img src="<?= BASE_URL ?>assets/images/produits/<?= $row['image_url'] ?>"
                                         alt="<?= htmlspecialchars($row['nom']) ?>"
                                         class="absolute inset-0 w-full h-full object-cover object-top">
                                </div>
                            </a>

                            <!-- Bouton Wishlist -->
                            <label class="wishlist-btn absolute bottom-4 right-4 z-10">
                                <input type="checkbox"
                                       class="wishlist-input"
                                       data-product-id="<?= $row['id_produit'] ?>"
                                       <?= (isset($_SESSION['id_utilisateur']) && $wishlistManager->isInWishlist($_SESSION['id_utilisateur'], $row['id_produit'])) ? 'checked' : '' ?> />
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
                                <?= htmlspecialchars($row['nom']) ?>
                            </h3>
                            <p class="text-xs text-gray-600 mb-1">
                                <?= htmlspecialchars($row['marque']) ?>
                            </p>
                            <div class="product-price-cart-container flex justify-between items-center">
                                <p class="product-price text-sm text-blue-600 font-bold">
                                    <?= number_format($row['prix'], 2) ?> €
                                </p>
                                <button type="button" 
                                        class="product-cart-button open-modal-btn flex items-center justify-center" 
                                        data-product-id="<?= $row['id_produit'] ?>" 
                                        data-product-price="<?= $row['prix'] ?>"
                                        data-product-sizes="<?= htmlspecialchars($row['tailles_disponibles']) ?>">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon text-blue-500 hover:text-blue-600">
                                    <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8ZM10.84 5.69C10.84 4.04 12.2 2.69 13.84 2.69C15.49 2.69 16.84 3.69 16.84 5.69V6.69H10.84V5.69ZM23.82 18.41L22.39 8.41C22.25 7.43 21.41 6.69 20.41 6.69H18.84V5.69C18.84 2.69 16.6 0.69 13.84 0.69C11.08 0.69 8.84 2.93 8.84 5.69V6.69H7.57C6.58 6.69 5.43 7.43 5.29 8.41L3.86 18.41C3.69 19.62 4.62 20.69 5.84 20.69H21.84C23.06 20.69 23.99 19.62 23.82 18.41Z" fill="currentColor" />
                                </svg>
                            </button>
                            </div>
                        </div>
                    </div>
            <?php
                }
                echo '</div>';
            } else {
                echo "<p class='mt-4'>Aucun produit trouvé pour cette recherche.</p>";
            }
            ?>
        </section>
    </div>
</main>


<!-- Remplacer le modal existant par celui-ci -->
<div id="modal-container" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0 hidden">
    <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
            <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
            <select id="productSize" class="w-full px-3 py-2 border border-blue-300 rounded-md mb-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-blue-900 bg-blue-50">
                <!-- Les options seront ajoutées dynamiquement -->
            </select>
            <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                <button id="cancelBtn" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                    Annuler
                </button>
                <!-- Version mobile du bouton -->
                <button id="addToCartBtnMobile" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md sm:hidden"
                        data-product-id=""
                        data-product-price="">
                    <span>Ajouter au panier</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                    </svg>
                </button>
                <!-- Version desktop du bouton -->
                <div id="addToCartBtn" 
                     class="cart-add-button button-shadow hidden sm:block" 
                     data-product-id=""
                     data-product-price=""
                     data-tooltip="">
                    <div class="cart-add-button-wrapper">
                        <div class="cart-add-button-text">Ajouter au panier</div>
                        <span class="cart-add-button-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast pour les favoris -->
<div id="wishlistToast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50 hover:bg-green-600 cursor-pointer">
    <a href="wishlist.php" class="flex items-center text-white">
        <span class="toast-message">Produit ajouté aux favoris</span>
    </a>
</div>

<!-- Toast notification -->
<div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
</div>

        <?php include '../includes/_scripts.php'; ?>
        
        <?php require_once '../includes/_footer.php'; ?>



</body>

</html>