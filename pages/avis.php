<?php
ob_start();
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

// Vérification de la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour voir les avis.";
    header("Location: connexion.php");
    exit();
}

// Récupération de l'ID du produit
$id_produit = isset($_GET['id_produit']) ? intval($_GET['id_produit']) : 0;

// Récupération de TOUS les avis (sans LIMIT)
$sql = "SELECT a.*, u.nom as nom_utilisateur 
        FROM avis a 
        LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
        WHERE a.id_produit = ? 
        ORDER BY a.date_creation DESC"; // Supprimez le LIMIT 5

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_produit);
$stmt->execute();
$avis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Compter le nombre total d'avis
$sql_count = "SELECT COUNT(*) as total FROM avis WHERE id_produit = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $id_produit);
$stmt_count->execute();
$total_avis = $stmt_count->get_result()->fetch_assoc()['total'];

// Si c'est une requête AJAX pour charger tous les avis
if (isset($_GET['action']) && $_GET['action'] === 'load_all') {
    $sql = "SELECT a.*, u.nom as nom_utilisateur 
            FROM avis a 
            LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
            WHERE a.id_produit = ? 
            ORDER BY a.date_creation DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produit);
    $stmt->execute();
    $all_avis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'avis' => $all_avis]);
    exit();
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Bouton retour -->
    <div class="mb-6">
        <a href="./detail.php?id_produit=<?php echo $id_produit; ?>&tab=tab2" 
           class="inline-flex items-center text-blue-500 hover:text-blue-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au produit
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-6">Avis clients (<?php echo $total_avis; ?>)</h2>

    <!-- Liste des 5 premiers avis -->
    <div id="avis-container">
        <?php if (empty($avis)): ?>
            <p class="text-gray-500">Aucun avis pour ce produit.</p>
        <?php else: ?>
            <?php foreach ($avis as $review): ?>
                <div class="mb-4 p-4 bg-white shadow rounded">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg class="w-5 h-5 <?php echo $i <= $review['note'] ? 'text-yellow-400' : 'text-gray-300'; ?>" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <div class="font-semibold mt-1"><?php echo htmlspecialchars($review['nom_utilisateur'] ?? 'Anonyme'); ?></div>
                        </div>
                        <div class="text-sm text-gray-500">
                            <?php echo date('d/m/Y', strtotime($review['date_creation'])); ?>
                        </div>
                    </div>
                    <p class="mt-2 text-gray-700"><?php echo nl2br(htmlspecialchars($review['commentaire'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/_footer.php'; ?>
