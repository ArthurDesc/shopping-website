<?php
include '../includes/session.php';
include '../includes/_db.php';
require_once '../classe/produit.php';
require_once '../classe/ArticleManager.php';
require_once '../classe/AdminManager.php';
require_once '../includes/product_functions.php';
require_once "../classe/Panier.php";


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
function getProductRatingSummary($product_id) {
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

?>

<?php include '../includes/_header.php'; ?>


<body class="bg-gray-100">
    <?php if ($isEditMode): ?>
        <div class="bg-red-500 text-white text-center py-2 font-bold">
            Mode Admin
        </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white p-4 rounded-lg shadow-md">
            <!-- En-tête avec bouton retour -->
            <div class="mb-4">
                <a href="produit.php" class="text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
            </div>

            <!-- Contenu principal -->
            <div class="flex flex-col md:flex-row">
                <!-- Image du produit -->
                <div class="md:w-1/3 mb-4 md:mb-0">
                    <img src="<?php echo htmlspecialchars($image_url); ?>"
                         alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                         class="w-full h-auto object-cover rounded-lg">
                </div>

                <!-- Détails du produit -->
                <div class="md:w-2/3 md:pl-6 space-y-4" x-data="{ 
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
                    <!-- Titre -->
                    <div class="flex items-center">
                        <h2 class="text-2xl font-bold" x-show="!editingTitle" x-text="title"></h2>
                        <input x-show="editingTitle"
                               x-model="title"
                               @keydown.enter="editingTitle = false; updateTitle(title)"
                               @keydown.escape="editingTitle = false"
                               class="text-2xl font-bold border-b-2 border-blue-500 focus:outline-none"
                               type="text">
                        <?php if ($isEditMode): ?>
                            <button @click="editingTitle = !editingTitle" class="ml-2 text-gray-600 hover:text-blue-500">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>

                    <!-- Étoiles et avis -->
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $average_rating) {
                                    echo '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                                } else {
                                    echo '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                                }
                            }
                            ?>
                        </div>
                        <p class="ml-2 text-sm text-gray-600">
                            <?php 
                            if ($review_count > 0) {
                                echo "$average_rating sur 5 ($review_count avis)";
                            } else {
                                echo "Aucun avis pour le moment";
                            }
                            ?>
                        </p>
                    </div>

                    <!-- Description -->
                    <div x-data="{ editingDescription: false, description: '<?php echo addslashes(htmlspecialchars($produit['description'])); ?>' }">
                        <div class="flex items-start">
                            <?php if ($isEditMode): ?>
                            <button @click="editingDescription = !editingDescription" class="mr-2 text-gray-600 hover:text-blue-500 flex-shrink-0">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <?php endif; ?>
                            <p class="text-gray-600 text-sm flex-grow" x-show="!editingDescription" x-text="description"></p>
                            <textarea x-show="editingDescription"
                                      x-model="description"
                                      @keydown.enter="editingDescription = false; updateDescription(description)"
                                      @keydown.escape="editingDescription = false"
                                      class="w-full text-sm border-b-2 border-blue-500 focus:outline-none"
                                      rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <span class="font-bold text-lg" x-show="!editingPrice" x-text="price + ' €'"></span>
                            <input x-show="editingPrice"
                                   x-model="price"
                                   @keydown.enter="editingPrice = false; updatePrice(price)"
                                   @keydown.escape="editingPrice = false"
                                   class="font-bold text-lg border-b-2 border-blue-500 focus:outline-none"
                                   type="number"
                                   step="0.01">
                            <?php if ($isEditMode): ?>
                            <button @click="editingPrice = !editingPrice" class="ml-2 text-gray-600 hover:text-blue-500">
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quantité -->
                    <select class="taille-select" name="taille" class="w-full mb-2 p-2 border rounded" required>
                        <option value="">Choisissez une taille</option>
                        <?php foreach ($tailles_disponibles as $taille): ?>
                            <option value="<?php echo htmlspecialchars($taille); ?>"><?php echo htmlspecialchars($taille); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="quantite-select" name="quantite" class="w-full mb-2 p-2 border rounded" required>
                        <option value="">Choisissez une quantité</option>
                        <?php for ($i = 1; $i <= min($produit['stock'], 10); $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>

                    <!-- Marque -->
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Marque : </span>
                        <span x-show="!editingBrand" x-text="brand" class="text-sm text-gray-600 ml-1"></span>
                        <input x-show="editingBrand"
                               x-model="brand"
                               @keydown.enter="editingBrand = false; updateBrand(brand)"
                               @keydown.escape="editingBrand = false"
                               class="text-sm border-b-2 border-blue-500 focus:outline-none ml-1"
                               type="text">
                        <?php if ($isEditMode): ?>
                        <button @click="editingBrand = !editingBrand" class="ml-2 text-gray-600 hover:text-blue-500">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Collection -->
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600">Collection : </span>
                        <span x-show="!editingCollection" x-text="collection" class="text-sm text-gray-600 ml-1"></span>
                        <select x-show="editingCollection"
                                x-model="collection"
                                @change="editingCollection = false; updateCollection(collection)"
                                @keydown.escape="editingCollection = false"
                                class="text-sm border-b-2 border-blue-500 focus:outline-none ml-1">
                            <option value="Homme">Homme</option>
                            <option value="Femme">Femme</option>
                            <option value="Enfant">Enfant</option>
                        </select>
                        <?php if ($isEditMode): ?>
                        <button @click="editingCollection = !editingCollection" class="ml-2 text-gray-600 hover:text-blue-500">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <?php endif; ?>
                    </div>

                    <!-- Catégories -->
                    <?php if (!empty($categories)): ?>
                        <p class="text-sm text-gray-600">Catégories :
                            <?php
                            $cat_names = array_map(function ($cat) {
                                return htmlspecialchars($cat['nom']);
                            }, $categories);
                            echo implode(', ', $cat_names);
                            ?>
                        </p>
                    <?php endif; ?>

                    <!-- Boutons d'action -->
                    <?php
                    $tailles_disponibles = explode(',', $produit['tailles_disponibles']);
                    ?>
                    <div class="mt-4 space-y-2">
                        <!-- Check if product ID is set -->
                        <?php if (!isset($produit['id_produit'])): ?>
                            <p>Erreur : Aucun ID de produit fourni.</p>
                        <?php else: ?>
                            <form id="add-to-cart-form" class="mt-4 space-y-2">
                                <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                                <select name="taille" class="w-full mb-2 p-2 border rounded" required>
                                    <option value="">Choisissez une taille</option>
                                    <?php foreach ($tailles_disponibles as $taille): ?>
                                        <option value="<?php echo htmlspecialchars($taille); ?>"><?php echo htmlspecialchars($taille); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <select name="quantite" class="w-full mb-2 p-2 border rounded" required>
                                    <option value="">Choisissez une quantité</option>
                                    <?php for ($i = 1; $i <= min($produit['stock'], 10); $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                                <div class="flex space-x-2">
                                    <button type="submit" id="add-to-cart-btn" class="flex-1 bg-blue-600 text-white font-semibold py-2 rounded">
                                        Ajouter au panier
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>

                     <script>
                    function acheterMaintenant() {
                        var form = document.getElementById('product-form');
                        form.action = 'process_paiement.php'; // Redirige vers la page de paiement
                        form.submit();
                    }
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Après les détails du produit et avant le formulaire d'ajout au panier -->
    <div class="mt-8" x-data="{ openReviews: false, showAllReviews: false }">
        <h3 class="text-xl font-semibold mb-4 cursor-pointer flex items-center ml-2" @click="openReviews = !openReviews">
            Avis des clients
            <svg :class="{'rotate-180': openReviews}" class="w-5 h-5 ml-2 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </h3>
        
        <div x-show="openReviews" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="mt-4">
            <!-- Affichage des avis existants -->
            <div class="mb-6 space-y-6">
                <?php
                $avis = getProductReviews($produit['id_produit']);
                $avisCount = count($avis);
                $avisToShow = 3; // Nombre d'avis à afficher initialement
                
                if (empty($avis)) {
                    echo "<p class='text-lg'>Aucun avis pour ce produit.</p>";
                } else {
                    for ($i = 0; $i < min($avisToShow, $avisCount); $i++) {
                        $review = $avis[$i];
                        ?>
                        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                            <div class="flex items-center mb-4">
                                <div class="flex items-center">
                                    <?php for ($j = 1; $j <= 5; $j++): ?>
                                        <svg class="w-6 h-6 <?php echo $j <= $review['note'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    <?php endfor; ?>
                                </div>
                                <span class="ml-3 text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($review['nom_utilisateur']); ?></span>
                            </div>
                            <p class="text-lg text-gray-700 mb-2"><?php echo htmlspecialchars($review['commentaire']); ?></p>
                            <?php if (isset($review['date_avis']) && $review['date_avis']): ?>
                                <p class="text-sm text-gray-500">Publié le <?php echo date('d/m/Y à H:i', strtotime($review['date_avis'])); ?></p>
                            <?php else: ?>
                                <p class="text-sm text-gray-500">Date de publication non disponible</p>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                    
                    if ($avisCount > $avisToShow) {
                        echo '<div x-show="!showAllReviews" class="text-center mt-4">';
                        echo '<button @click="showAllReviews = true" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Voir plus d\'avis</button>';
                        echo '</div>';
                        
                        echo '<div x-show="showAllReviews" class="space-y-6">';
                        for ($i = $avisToShow; $i < $avisCount; $i++) {
                            $review = $avis[$i];
                            ?>
                            <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                                <div class="flex items-center mb-4">
                                    <div class="flex items-center">
                                        <?php for ($j = 1; $j <= 5; $j++): ?>
                                            <svg class="w-6 h-6 <?php echo $j <= $review['note'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="ml-3 text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($review['nom_utilisateur']); ?></span>
                                </div>
                                <p class="text-lg text-gray-700 mb-2"><?php echo htmlspecialchars($review['commentaire']); ?></p>
                                <?php if (isset($review['date_avis']) && $review['date_avis']): ?>
                                    <p class="text-sm text-gray-500">Publié le <?php echo date('d/m/Y à H:i', strtotime($review['date_avis'])); ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500">Date de publication non disponible</p>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                        echo '<div class="text-center mt-4">';
                        echo '<button @click="showAllReviews = false" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">Voir moins</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <!-- Formulaire pour ajouter un avis -->
            <h4 class="text-lg font-semibold mb-2 ml-2">Donnez votre avis</h4>
            <form action="<?php echo BASE_URL; ?>pages/ajouter_avis.php" method="POST" id="avis-form" class="text-base">
                <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 ml-2">Note</label>
                    <div class="flex items-center" id="star-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg class="w-8 h-8 text-gray-300 cursor-pointer star-icon" data-rating="<?php echo $i; ?>" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="note" id="note-input" value="" required>
                </div>
                <div class="mb-4">
                    <div class="bg-white border border-slate-200 grid grid-cols-6 gap-2 rounded-xl p-2 text-sm">
                        <h1 class="text-center text-slate-200 text-xl font-bold col-span-6">Votre avis</h1>
                        <textarea name="commentaire" id="commentaire" placeholder="Votre avis..." class="bg-slate-100 text-slate-600 h-28 placeholder:text-slate-600 placeholder:opacity-50 border border-slate-200 col-span-6 resize-none outline-none rounded-lg p-2 duration-300 focus:border-slate-600" required></textarea>
                        
                        <span class="col-span-2"></span>
                        <button type="submit" class="bg-slate-100 stroke-slate-600 border border-slate-200 col-span-2 flex justify-center rounded-lg p-2 duration-300 hover:border-slate-600 hover:text-white focus:stroke-blue-200 focus:bg-blue-400">
                            <svg fill="none" viewBox="0 0 24 24" height="30px" width="30px" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" d="M7.39999 6.32003L15.89 3.49003C19.7 2.22003 21.77 4.30003 20.51 8.11003L17.68 16.6C15.78 22.31 12.66 22.31 10.76 16.6L9.91999 14.08L7.39999 13.24C1.68999 11.34 1.68999 8.23003 7.39999 6.32003Z"></path>
                                <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" d="M10.11 13.6501L13.69 10.0601"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-12">
        <h3 class="text-xl font-semibold mb-4">Produits associés</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            // Récupérer les produits associés (à implémenter)
            $produits_associes = getRelatedProducts($produit['id_produit'], $produit['id_categorie']);
            foreach ($produits_associes as $produit_associe) {
                echo "<div class='border p-4 rounded'>";
                echo "<img src='" . BASE_URL . "assets/images/produits/" . $produit_associe['image_url'] . "' alt='" . $produit_associe['nom'] . "' class='w-full h-48 object-cover mb-2'>";
                echo "<h4 class='font-semibold'>" . $produit_associe['nom'] . "</h4>";
                echo "<p class='text-gray-600'>" . number_format($produit_associe['prix'], 2) . " €</p>";
                echo "<a href='detail.php?id=" . $produit_associe['id_produit'] . "' class='mt-2 inline-block bg-blue-500 text-white px-4 py-2 rounded'>Voir le produit</a>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <?php if ($isEditMode): ?>
        <script src="../assets/js/editMode.js">
            function updateField(field, newValue) {
                fetch('/shopping-website/admin/update_article.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_produit: <?php echo $produit['id_produit']; ?>,
                        field: field,
                        new_value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(field + ' mis à jour avec succès');
                    } else {
                        console.error('Erreur lors de la mise à jour de ' + field + ':', data.message);
                        alert('Erreur lors de la mise à jour de ' + field + ': ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur s\'est produite lors de la mise à jour de ' + field);
                });
            }

            // Utilisez cette fonction pour chaque champ
            function updateTitle(newTitle) {
                updateField('nom', newTitle);
            }

            function updateDescription(newDescription) {
                updateField('description', newDescription);
            }

            function updatePrice(newPrice) {
                updateField('prix', newPrice);
            }

            function updateBrand(newBrand) {
                updateField('marque', newBrand);
            }

            function updateCollection(newCollection) {
                if (['Homme', 'Femme', 'Enfant'].includes(newCollection)) {
                    updateField('collection', newCollection);
                } else {
                    console.error('Collection invalide');
                    alert('Veuillez choisir une collection valide : Homme, Femme ou Enfant');
                }
            }
        </script>
    <?php endif; ?>

    <script src="<?php echo BASE_URL; ?>assets/js/scripts.js" defer></script>
    <script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
    <script src="<?php echo BASE_URL; ?>assets/js/detail.js" defer></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('add-to-cart-form');
        const addToCartBtn = document.getElementById('add-to-cart-btn');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            addToCartBtn.disabled = true;
            addToCartBtn.textContent = 'Ajout en cours...';

            const formData = new FormData(form);

            fetch('<?php echo BASE_URL; ?>ajax/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.cartCount);
                } else {
                    alert('Erreur : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur s\'est produite lors de l\'ajout au panier.');
            })
            .finally(() => {
                addToCartBtn.disabled = false;
                addToCartBtn.textContent = 'Ajouter au panier';
            });
        });

        function updateCartCount(count) {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = count;
            }
        }
    });
    </script>
</body>

</html>
