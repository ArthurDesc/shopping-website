<?php
include '../includes/session.php';
include '../includes/_db.php';
require_once '../classe/produit.php';
require_once '../classe/ArticleManager.php';
require_once '../classe/AdminManager.php';
require_once '../includes/product_functions.php';
require_once "../classe/Panier.php";
require_once "../classe/Avis.php";
require_once "../classe/AvisManager.php";
require_once '../classe/CategoryManager.php';


// Créez une instance de AdminManager
$adminManager = new AdminManager($conn);

$mode = isset($_GET['mode']) && $_GET['mode'] === 'edit' ? 'edit' : 'view';
$isEditMode = $mode === 'edit';

// Vérifiez si l'utilisateur est admin pour le mode édition
if ($isEditMode) {
    $id_utilisateur = get_id_utilisateur();
    if (!$id_utilisateur || !$adminManager->isAdmin($id_utilisateur)) {
        header("Location: /shopping-website/index.php");
        exit();
    }
}

// Vérifier si un ID de produit est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: /shopping-website/index.php");
    exit();
}

$id_produit = $_GET['id'];



// Récupérer les détails du produit
$sql = "SELECT p.*, pc.id_categorie 
        FROM produits p 
        LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
        WHERE p.id_produit = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_produit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produit non trouvé";
    exit();
}

$produit = $result->fetch_assoc();

// Récupérer les catégories du produit
$query_categories = "SELECT c.* 
                     FROM categories c
                     JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie
                     WHERE pc.id_produit = ?";
$stmt_categories = $conn->prepare($query_categories);
$stmt_categories->bind_param("i", $id_produit);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();

$categories = [];
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}

// Définir le chemin de l'image
$image_path = '../assets/images/produits/';
$image_url = $image_path . ($produit['image_url'] ?? 'default_product.jpg');

// Vérifier si l'image existe, sinon utiliser l'image par défaut
if (!file_exists($image_url) || empty($produit['image_url'])) {
    $image_url = $image_path . 'default_product.jpg';
}

$articleManager = new ArticleManager($conn);
$id_article = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = $articleManager->getArticle($id_article);

if (!$article) {
    // Rediriger si l'article n'existe pas
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$categories = $articleManager->getArticleCategories($id_article);
$allCategories = $articleManager->getAllCategories();

// Fonction pour obtenir la moyenne des notes et le nombre d'avis
function getProductRatingSummary($product_id)
{
    global $conn;
    $sql = "SELECT COALESCE(AVG(note), 0) as average_rating, COUNT(*) as review_count FROM avis WHERE id_produit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

$rating_summary = getProductRatingSummary($produit['id_produit']);
$average_rating = $rating_summary['average_rating'] !== null ? round($rating_summary['average_rating'], 1) : 0;
$review_count = $rating_summary['review_count'];

// Créer une instance de AvisManager
$avisManager = new AvisManager($conn);

// Récupérer les avis pour ce produit
$avis_produit = $avisManager->getAvisForProduct($id_produit);

$categoryManager = new CategoryManager($conn);

// Récupérer les catégories du produit
$categories = $categoryManager->getProductCategories($id_produit);

// Récupérer la collection du produit
$collection = $categoryManager->getCollection($id_produit);

?>

<?php include '../includes/_header.php'; ?>

<main class="flex-grow container mx-auto px-4 pt-4"> <!-- Remplacé mt-16 et py-8 par pt-4 -->

    <input type="hidden" id="id_produit" name="id_produit" value="<?php echo $id_produit; ?>">
    <?php if ($isEditMode): ?>
        <div class="bg-red-500 text-white text-center py-2 font-bold">
            Mode Admin
        </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 py-4 sm:py-8">
        <div class="bg-white p-4 rounded-lg shadow-sm">
            <!-- Contenu principal -->
            <div class="flex flex-col md:flex-row">
                <!-- Icône de retour (visible uniquement sur mobile) -->
                <div class="block md:hidden mb-4 px-2">
                    <a href="produit.php" class="inline-flex items-center text-gray-600 hover:text-blue-500 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 back-arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span class="ml-2 text-sm">Retour aux produits</span>
                    </a>
                </div>

                <!-- Conteneur pour l'icône de retour et l'image -->
                <div class="flex w-full md:w-1/3">
                    <!-- Icône de retour (visible uniquement sur desktop) -->
                    <div class="hidden md:flex md:items-start md:pr-4">
                        <a href="produit.php" class="text-gray-600 hover:text-blue-500 transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 back-arrow-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                    </div>

                    <!-- Image du produit -->
                    <div class="flex-grow px-4 sm:px-0">
                        <div class="image-container aspect-[3/4] overflow-hidden rounded-lg max-w-xs mx-auto relative">
                            <img src="<?php echo htmlspecialchars($image_url); ?>"
                                alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                class="w-full h-full object-cover">

                            <!-- Bouton Wishlist -->
                            <label class="wishlist-btn absolute bottom-4 right-4 z-10">
                                <input type="checkbox"
                                    class="wishlist-input"
                                    <?php echo (isset($isInWishlist) && $isInWishlist) ? 'checked="checked"' : ''; ?>
                                    data-product-id="<?php echo $produit['id_produit']; ?>" />
                                <div class="wishlist-heart">
                                    <svg viewBox="0 0 256 256">
                                        <rect fill="none" height="256" width="256"></rect>
                                        <path d="M224.6,51.9a59.5,59.5,0,0,0-43-19.9,60.5,60.5,0,0,0-44,17.6L128,59.1l-7.5-7.4C97.2,28.3,59.2,26.3,35.9,47.4a59.9,59.9,0,0,0-2.3,87l83.1,83.1a15.9,15.9,0,0,0,22.6,0l81-81C243.7,113.2,245.6,75.2,224.6,51.9Z"
                                            stroke-width="20px" stroke="#000" fill="none"></path>
                                    </svg>
                                </div>
                            </label>

                            <?php if ($isEditMode): ?>
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 p-2 flex justify-center">
                                    <label for="image-upload" class="cursor-pointer text-white hover:text-blue-300 flex items-center">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="mr-2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                                        </svg>
                                        <span class="text-sm">Modifier l'image</span>
                                    </label>
                                    <input
                                        type="file"
                                        id="image-upload"
                                        accept="image/*"
                                        class="hidden"
                                        onchange="updateImage(this.files[0])">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Détails du produit -->
                <div class="w-full md:w-2/3 md:pl-6 flex flex-col justify-between space-y-4 mt-6 md:mt-0" x-data="{ 
                    editingTitle: false, 
                    title: '<?php echo addslashes(htmlspecialchars($produit['nom'])); ?>',
                    editingDescription: false, 
                    description: '<?php echo addslashes(htmlspecialchars($produit['description'])); ?>',
                    editingPrice: false,
                    price: '<?php echo number_format($produit['prix'], 2); ?>',
                    editingBrand: false,
                    brand: '<?php echo addslashes(htmlspecialchars($produit['marque'])); ?>',
                    editingCollection: false,
                    collection: '<?php echo addslashes(htmlspecialchars($produit['collection'])); ?>'
                }">
                    <!-- Titre, Prix et Avis -->
                    <div class="flex flex-col space-y-3">
                        <!-- Titre et Prix sur mobile -->
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                            <!-- Titre -->
                            <div class="flex items-center">
                                <h2 class="text-2xl sm:text-3xl montserrat-bold text-blue-600" x-show="!editingTitle" x-text="title"></h2>
                                <input x-show="editingTitle"
                                    x-model="title"
                                    @keydown.enter="editingTitle = false; updateTitle(title)"
                                    @keydown.escape="editingTitle = false"
                                    class="text-2xl sm:text-3xl montserrat-bold border-b-2 border-blue-500 focus:outline-none w-full text-blue-600"
                                    type="text">
                                <?php if ($isEditMode): ?>
                                    <button @click="editingTitle = !editingTitle" class="ml-2 text-gray-600 hover:text-blue-500">
                                        <svg class="w-5 h-5" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <!-- Prix -->
                            <div class="flex items-center">
                                <p class="text-xl sm:text-2xl font-bold text-blue-600" x-show="!editingPrice" x-text="price + ' €'"></p>
                                <input x-show="editingPrice"
                                    x-model="price"
                                    @keydown.enter="editingPrice = false; updatePrice(price)"
                                    @keydown.escape="editingPrice = false"
                                    class="text-xl sm:text-2xl font-bold text-blue-600 border-b-2 border-blue-500 focus:outline-none"
                                    type="text">
                                <?php if ($isEditMode): ?>
                                    <button @click="editingPrice = !editingPrice" class="ml-2 text-gray-600 hover:text-blue-500">
                                        <svg class="w-5 h-5" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-4 sm:mt-6">
                            <div class="prose max-w-none">
                                <p class="text-gray-600 text-sm sm:text-base" x-show="!editingDescription" x-text="description"></p>
                                <textarea x-show="editingDescription"
                                    x-model="description"
                                    @keydown.enter="editingDescription = false; updateDescription(description)"
                                    @keydown.escape="editingDescription = false"
                                    class="w-full text-sm sm:text-base text-gray-600 border-b-2 border-blue-500 focus:outline-none min-h-[100px]"
                                    rows="3"></textarea>
                                <?php if ($isEditMode): ?>
                                    <button @click="editingDescription = !editingDescription" class="mt-2 text-gray-600 hover:text-blue-500">
                                        <svg class="w-5 h-5" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Marque -->
                        <div class="flex items-center mt-2">
                            <span class="font-semibold">Marque:</span>
                            <span class="ml-2 text-gray-600" x-show="!editingBrand" x-text="brand"></span>
                            <input x-show="editingBrand"
                                x-model="brand"
                                @keydown.enter="editingBrand = false; updateBrand(brand)"
                                @keydown.escape="editingBrand = false"
                                class="ml-2 text-gray-600 border-b-2 border-blue-500 focus:outline-none"
                                type="text">
                            <?php if ($isEditMode): ?>
                                <button @click="editingBrand = !editingBrand" class="ml-2 text-gray-600 hover:text-blue-500">
                                    <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>



                    <!-- Boutons d'action -->
                    <?php
                    $tailles_disponibles = !empty($produit['tailles_disponibles'])
                        ? explode(',', $produit['tailles_disponibles'])
                        : [];
                    ?>
                    <?php if ($isEditMode): ?>
                        <!-- Section de gestion du stock en mode édition -->
                        <div class="mt-4 p-4 border rounded-lg bg-gray-50">
                            <div class="flex items-center justify-between" x-data="{ 
                                editingStock: false,
                                stock: '<?php echo $produit['stock']; ?>'
                            }">
                                <div class="flex items-center space-x-2">
                                    <h3 class="font-semibold text-gray-700">Stock disponible :</h3>
                                    <div x-show="!editingStock" class="text-lg">
                                        <span x-text="stock"></span> unités
                                    </div>
                                    <input
                                        x-show="editingStock"
                                        type="number"
                                        x-model="stock"
                                        @keydown.enter="editingStock = false; updateStock(stock)"
                                        @keydown.escape="editingStock = false"
                                        class="w-24 px-2 py-1 border rounded focus:outline-none focus:border-blue-500"
                                        min="0">
                                </div>
                                <button
                                    @click="editingStock = !editingStock"
                                    class="text-blue-500 hover:text-blue-700">
                                    <svg x-show="!editingStock" width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Section existante pour l'ajout au panier -->
                        <div class="mt-4 space-y-2">
                            <?php if (!isset($produit['id_produit'])): ?>
                                <p>Erreur : Aucun ID de produit fourni.</p>
                            <?php else: ?>
                                <form id="add-to-cart-form" class="mt-4">
                                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4">
                                        <div class="mb-2 md:mb-0 flex-grow">
                                            <div class="flex flex-col gap-1">
                                                <p id="taille-error" class="text-red-500 text-sm hidden">Veuillez sélectionner une taille</p>
                                                <label class="text-sm font-medium text-gray-700">Taille</label>
                                            </div>
                                            <div class="flex flex-wrap gap-2">
                                                <?php if (!empty($tailles_disponibles)):
                                                    foreach ($tailles_disponibles as $taille): ?>
                                                        <label class="inline-flex items-center">
                                                            <input type="radio" name="taille" value="<?php echo htmlspecialchars($taille); ?>" class="hidden">
                                                            <span class="px-4 py-3 border rounded-lg cursor-pointer hover:bg-gray-100 text-base"><?php echo htmlspecialchars($taille); ?></span>
                                                        </label>
                                                    <?php endforeach;
                                                else: ?>
                                                    <p>Aucune taille disponible</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="quantity-container flex justify-end md:justify-start mb-2 md:mb-0 w-full md:w-auto">
                                            <label for="quantite" class="quantity-label mr-3">Quantité</label>
                                            <select id="quantite" name="quantite" class="quantity-select" required>
                                                <?php for ($i = 1; $i <= min($produit['stock'], 10); $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php echo $i === 1 ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="add-to-cart-button mt-4 md:mt-0">
                                            <button type="submit" id="add-to-cart-btn" class="add-to-cart-button-wrapper">
                                                <div class="add-to-cart-text">Ajouter au panier</div>
                                                <span class="add-to-cart-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z" />
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ajoutez cette section pour les onglets -->
            <div class="mt-8">
                <div class="tab-container flex items-center">
                    <input type="radio" name="tab" id="tab1" class="tab tab--1" checked />
                    <label class="tab_label" for="tab1">Détails</label>

                    <input type="radio" name="tab" id="tab2" class="tab tab--2" />
                    <label class="tab_label flex items-center" for="tab2">
                        Avis
                        <span class="ml-2 text-sm font-semibold flex items-center">
                            <?php if ($review_count > 0): ?>
                                <span class="flex items-center">
                                    <?php
                                    $full_stars = floor($average_rating);
                                    $half_star = $average_rating - $full_stars >= 0.5;
                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

                                    for ($i = 0; $i < $full_stars; $i++) {
                                        echo '<svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                                    }
                                    if ($half_star) {
                                        echo '<svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-path="url(#half-star-clip)"></path><clipPath id="half-star-clip"><rect x="0" y="0" width="10" height="20" /></clipPath></svg>';
                                    }
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                                    }
                                    ?>
                                </span>
                                <span class="ml-1">(<?php echo $review_count; ?>)</span>
                            <?php else: ?>
                                <span>(Aucun avis)</span>
                            <?php endif; ?>
                        </span>
                    </label>

                    <div class="indicator"></div>
                </div>

                <!-- Contenu des onglets -->
                <div class="tab-content mt-4">
                    <div id="tab1-content" class="tab-pane active">
                        <!-- Contenu de l'onglet Détails -->
                        <h3 class="text-xl font-semibold mb-4">Détails du produit</h3>
                        <p class="mb-4" x-text="description"></p>

                        <div class="flex flex-col space-y-4">
                            <!-- Collection -->
                            <div class="flex items-center">
                                <span class="font-semibold text-blue-500">Collection:</span>
                                <?php if ($isEditMode): ?>
                                    <div class="ml-2" x-data="{ 
                                        editingCollection: false,
                                        collection: '<?php echo htmlspecialchars($collection); ?>',
                                        tempCollection: '<?php echo htmlspecialchars($collection); ?>'
                                    }">
                                        <span x-show="!editingCollection" class="text-gray-600" x-text="collection"></span>
                                        <div x-show="editingCollection" class="flex items-center space-x-2">
                                            <select
                                                x-model="tempCollection"
                                                class="border-b-2 border-blue-500 focus:outline-none text-gray-600">
                                                <option value="Homme">Homme</option>
                                                <option value="Femme">Femme</option>
                                                <option value="Enfant">Enfant</option>
                                            </select>
                                            <button
                                                @click="collection = tempCollection; editingCollection = false; updateCollection(tempCollection)"
                                                class="text-green-500 hover:text-green-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            <button
                                                @click="editingCollection = false; tempCollection = collection"
                                                class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                        <button
                                            x-show="!editingCollection"
                                            @click="editingCollection = true"
                                            class="ml-2 text-gray-600 hover:text-blue-500">
                                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="ml-2 text-gray-600"><?php echo htmlspecialchars($collection ?? 'Non spécifiée'); ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Affichage des catégories -->
                            <div class="flex items-center">
                                <span class="font-semibold text-blue-500">Catégories:</span>
                                <?php if ($isEditMode): ?>
                                    <div id="edit-categories-container" class="ml-2 w-full"
                                        data-categories='<?php echo json_encode(array_map(function ($cat) {
                                                                return $cat['id_categorie'];
                                                            }, $categories)); ?>'>
                                    </div>
                                <?php else: ?>
                                    <span class="ml-2 text-gray-600">
                                        <?php if (!empty($categories)): ?>
                                            <?php echo implode(', ', array_map(function ($cat) {
                                                return htmlspecialchars($cat['nom']);
                                            }, $categories)); ?>
                                        <?php else: ?>
                                            Aucune catégorie
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div id="tab2-content" class="tab-pane">
                        <?php if (isset($_SESSION['id_utilisateur'])): ?>
                            <input type="hidden" id="current_user_id" value="<?php echo $_SESSION['id_utilisateur']; ?>">
                        <?php endif; ?>
                        <!-- Contenu de l'onglet Avis -->
                        <div id="comments-list">
                            <?php if (!empty($avis_produit)): ?>
                                <?php foreach (array_slice($avis_produit, 0, 5) as $avis): ?>
                                    <div class="mb-6 p-6 border rounded-xl shadow-lg bg-white" data-avis-id="<?php echo $avis->getIdAvis(); ?>">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                                                <span class="font-semibold"><?php echo htmlspecialchars($avis->getNomUtilisateur()); ?></span>
                                                <div class="rateyo-readonly" data-rating="<?php echo $avis->getNote(); ?>"></div>
                                            </div>

                                            <!-- Partie droite avec la date et les icônes (inchangée) -->
                                            <div class="flex items-center gap-4">
                                                <span class="text-sm text-gray-500"><?php echo $avis->getFormattedDate(); ?></span>
                                                <?php if (isset($_SESSION['id_utilisateur']) && $_SESSION['id_utilisateur'] == $avis->getIdUtilisateur()): ?>
                                                    <div class="flex gap-2">
                                                        <button onclick="modifierAvis(<?php echo $avis->getIdAvis(); ?>)" class="text-blue-600 hover:text-blue-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                        </button>
                                                        <button onclick="supprimerAvis(<?php echo $avis->getIdAvis(); ?>)" class="text-red-600 hover:text-red-800">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 mt-4 editable"
                                            data-name="commentaire"
                                            data-type="textarea"
                                            data-avis-id="<?php echo $avis->getIdAvis(); ?>">
                                            <?php echo htmlspecialchars($avis->getCommentaire()); ?>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                                
                                <?php if (count($avis_produit) > 5): ?>
                                    <div class="flex justify-center mt-6">
                                        <a href="avis.php?id=<?php echo $id_produit; ?>" 
                                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                            <span>Voir tous les avis (<?php echo count($avis_produit); ?>)</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p>Aucun avis pour ce produit pour le moment.</p>
                            <?php endif; ?>
                        </div>
                        <!-- Section pour ajouter un commentaire -->
                        <div class="mt-8 mb-4">
                            <h3 class="text-xl font-semibold mb-4">Ajouter un commentaire</h3>
                            <!-- Vérification de la connexion utilisateur -->
                            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                                <form id="comment-form" class="space-y-4">
                                    <!-- Champs cachés -->
                                    <input type="hidden" name="id_produit" value="<?php echo htmlspecialchars($id_produit); ?>">
                                    <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($_SESSION['id_utilisateur']); ?>">
                                    <input type="hidden" name="note" id="rating-input" value="0">

                                    <!-- Zone de commentaire -->
                                    <div class="flex flex-col gap-4">
                                        <!-- Textarea pour le commentaire -->
                                        <div class="w-full">
                                            <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-1">Votre commentaire</label>
                                            <textarea
                                                id="commentaire"
                                                name="commentaire"
                                                rows="4"
                                                required
                                                minlength="10"
                                                class="w-full h-25 px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500"
                                                placeholder="Écrivez votre commentaire ici (minimum 10 caractères)..."></textarea>
                                        </div>

                                        <!-- Système de notation avec RateYo -->
                                        <div class="w-full flex flex-col items-center gap-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm text-gray-700">Note* :</span>
                                                <div id="rateYo"></div>
                                            </div>
                                        </div>

                                        <!-- Bouton Envoyer -->
                                        <button type="submit" class="avis-button w-1/2">
                                            <div class="avis-outline"></div>
                                            <div class="avis-state avis-state--default">
                                                <div class="avis-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="1em" width="1em">
                                                        <g style="filter: url(#shadow)">
                                                            <path fill="currentColor" d="M14.2199 21.63C13.0399 21.63 11.3699 20.8 10.0499 16.83L9.32988 14.67L7.16988 13.95C3.20988 12.63 2.37988 10.96 2.37988 9.78001C2.37988 8.61001 3.20988 6.93001 7.16988 5.60001L15.6599 2.77001C17.7799 2.06001 19.5499 2.27001 20.6399 3.35001C21.7299 4.43001 21.9399 6.21001 21.2299 8.33001L18.3999 16.82C17.0699 20.8 15.3999 21.63 14.2199 21.63ZM7.63988 7.03001C4.85988 7.96001 3.86988 9.06001 3.86988 9.78001C3.86988 10.5 4.85988 11.6 7.63988 12.52L10.1599 13.36C10.3799 13.43 10.5599 13.61 10.6299 13.83L11.4699 16.35C12.3899 19.13 13.4999 20.12 14.2199 20.12C14.9399 20.12 16.0399 19.13 16.9699 16.35L19.7999 7.86001C20.3099 6.32001 20.2199 5.06001 19.5699 4.41001C18.9199 3.76001 17.6599 3.68001 16.1299 4.19001L7.63988 7.03001Z"></path>
                                                            <path fill="currentColor" d="M10.11 14.4C9.92005 14.4 9.73005 14.33 9.58005 14.18C9.29005 13.89 9.29005 13.41 9.58005 13.12L13.16 9.53C13.45 9.24 13.93 9.24 14.22 9.53C14.51 9.82 14.51 10.3 14.22 10.59L10.64 14.18C10.5 14.33 10.3 14.4 10.11 14.4Z"></path>
                                                        </g>
                                                        <defs>
                                                            <filter id="shadow">
                                                                <fedropshadow flood-opacity="0.5" stdDeviation="0.6" dy="1" dx="0"></fedropshadow>
                                                            </filter>
                                                        </defs>
                                                    </svg>
                                                </div>
                                                <p>
                                                    <span style="--i:0">E</span>
                                                    <span style="--i:1">n</span>
                                                    <span style="--i:2">v</span>
                                                    <span style="--i:3">o</span>
                                                    <span style="--i:4">y</span>
                                                    <span style="--i:5">e</span>
                                                    <span style="--i:6">r</span>
                                                </p>
                                            </div>
                                            <div class="avis-state avis-state--sent">
                                                <div class="avis-icon">
                                                    <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <g style="filter: url(#shadow)">
                                                            <path d="M12 22.75C6.07 22.75 1.25 17.93 1.25 12C1.25 6.07 6.07 1.25 12 1.25C17.93 1.25 22.75 6.07 22.75 12C22.75 17.93 17.93 22.75 12 22.75ZM12 2.75C6.9 2.75 2.75 6.9 2.75 12C2.75 17.1 6.9 21.25 12 21.25C17.1 21.25 21.25 17.1 21.25 12C21.25 6.9 17.1 2.75 12 2.75Z" fill="currentColor"></path>
                                                            <path d="M10.5795 15.5801C10.3795 15.5801 10.1895 15.5001 10.0495 15.3601L7.21945 12.5301C6.92945 12.2401 6.92945 11.7601 7.21945 11.4701C7.50945 11.1801 7.98945 11.1801 8.27945 11.4701L10.5795 13.7701L15.7195 8.6301C16.0095 8.3401 16.4895 8.3401 16.7795 8.6301C17.0695 8.9201 17.0695 9.4001 16.7795 9.6901L11.1095 15.3601C10.9695 15.5001 10.7795 15.5801 10.5795 15.5801Z" fill="currentColor"></path>
                                                        </g>
                                                    </svg>
                                                </div>
                                                <p>
                                                    <span style="--i:5">E</span>
                                                    <span style="--i:6">n</span>
                                                    <span style="--i:7">v</span>
                                                    <span style="--i:8">o</span>
                                                    <span style="--i:8">y</span>
                                                    <span style="--i:8">é</span>
                                                </p>
                                            </div>
                                        </button>
                                    </div>

                                    <p class="text-sm text-gray-500 mt-2">* Champs obligatoires</p>
                                </form>
                            <?php else: ?>
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Information :</strong>
                                    <span class="block sm:inline"> Vous devez être connecté pour laisser un avis.</span>
                                    <a href="/shopping-website/pages/connexion.php" class="underline">Se connecter</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>



                </div>
            </div>

            <div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
            </div>

            <!-- Toast notification -->
            <div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
                Article ajouté au panier
            </div>
</main>
<?php include '../includes/_scripts.php'; ?>

<?php include '../includes/_footer.php'; ?>


</body>

</html>