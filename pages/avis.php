<?php
require_once '../includes/_header.php';
require_once '../includes/_db.php';

if (!isset($_GET['id_produit'])) {
    header("Location: " . BASE_URL);
    exit();
}

$id_produit = $_GET['id_produit'];

// Fonction pour obtenir les détails du produit
function getProductDetails($product_id) {
    global $conn;
    $sql = "SELECT * FROM produits WHERE id_produit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Fonction pour obtenir tous les avis d'un produit
function getProductReviews($product_id) {
    global $conn;
    $sql = "SELECT a.*, u.nom, u.prenom FROM avis a JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur WHERE a.id_produit = ? ORDER BY a.date_creation DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    return $reviews;
}

$produit = getProductDetails($id_produit);
$avis = getProductReviews($id_produit);
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">Avis pour <?php echo htmlspecialchars($produit['nom']); ?></h1>
    <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $id_produit; ?>" class="text-blue-600 hover:underline mb-4 inline-block">Retour au produit</a>

    <?php if (empty($avis)): ?>
        <p>Aucun avis pour ce produit.</p>
    <?php else: ?>
        <?php foreach ($avis as $review): ?>
            <div class="bg-white shadow-md rounded-lg p-4 mb-4">
                <div class="flex items-center mb-2">
                    <div class="flex items-center">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['note']) {
                                echo '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                            } else {
                                echo '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                            }
                        }
                        ?>
                    </div>
                    <p class="ml-2 text-sm text-gray-600"><?php echo htmlspecialchars($review['nom'] . ' ' . $review['prenom']); ?></p>
                </div>
                <p class="text-gray-700"><?php echo htmlspecialchars($review['commentaire']); ?></p>
                <p class="text-sm text-gray-500 mt-2"><?php echo date('d/m/Y', strtotime($review['date_creation'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/_footer.php'; ?>