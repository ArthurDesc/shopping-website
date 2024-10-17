<?php
ob_start();
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';
require_once '../includes/product_functions.php';

// Vérification de la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour voir les avis.";
    header("Location: connexion.php");
    exit();
}

// Récupération de l'ID du produit
$id_produit = isset($_GET['id_produit']) ? intval($_GET['id_produit']) : 0;

if ($id_produit === 0) {
    $_SESSION['error_message'] = "ID de produit invalide.";
    header("Location: index.php");
    exit();
}

// Récupération des détails du produit
$produit = getProductDetails($id_produit);

if (!$produit) {
    $_SESSION['error_message'] = "Produit non trouvé.";
    header("Location: index.php");
    exit();
}

// Récupération des avis
$avis = getProductReviews($id_produit);

// Affichage des messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">';
    echo '<strong class="font-bold">Succès!</strong>';
    echo '<span class="block sm:inline"> ' . $_SESSION['success_message'] . '</span>';
    echo '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
    echo '<strong class="font-bold">Erreur!</strong>';
    echo '<span class="block sm:inline"> ' . $_SESSION['error_message'] . '</span>';
    echo '</div>';
    unset($_SESSION['error_message']);
}

// Affichage du titre
echo "<h1>Avis pour " . htmlspecialchars($produit['nom']) . "</h1>";

// Affichage des avis
if (empty($avis)): ?>
    <p>Aucun avis pour ce produit.</p>
<?php else: ?>
    <?php foreach ($avis as $review): ?>
        <div class="mb-4 p-4 bg-white shadow rounded">
            <div class="flex items-center mb-2">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <svg class="w-5 h-5 <?php echo $i <= $review['note'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                <?php endfor; ?>
                <span class="ml-2 text-sm font-semibold"><?php echo htmlspecialchars($review['nom_utilisateur']); ?></span>
            </div>
            <p class="text-gray-700"><?php echo htmlspecialchars($review['commentaire']); ?></p>
            <p class="text-sm text-gray-500 mt-2"><?php echo date('d/m/Y', strtotime($review['date_creation'])); ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Avis pour <?php echo htmlspecialchars($produit['nom']); ?></h1>
    <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $id_produit; ?>" class="text-blue-600 hover:underline mb-4 inline-block">Retour au produit</a>
</div>

<?php require_once '../includes/_footer.php'; ?>
