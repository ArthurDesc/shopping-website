<?php
if (!isset($_SESSION)) {
    session_start();
}

// Définir le chemin de base
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

require_once __DIR__ . '/../functions/url.php';
require_once __DIR__ . '/../includes/_db.php';

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Récupération des commandes de l'utilisateur
$stmt = $conn->prepare("
    SELECT c.*, p.montant, p.date_paiement, p.methode_paiement
    FROM commandes c 
    LEFT JOIN paiements p ON c.id_commande = p.id_commande 
    WHERE c.id_utilisateur = ?
    ORDER BY c.date_commande DESC
");
$stmt->bind_param("i", $_SESSION['id_utilisateur']);
$stmt->execute();
$result = $stmt->get_result();
$commandes = $result->fetch_all(MYSQLI_ASSOC);

require_once '../includes/_header.php';
?>

<main class="flex">
    <div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Mes commandes</h2>

        <?php if (empty($commandes)): ?>
            <div class="text-center p-6 min-h-[50vh] flex flex-col items-center justify-center">
                <div class="w-48 h-48 mb-6 mx-auto text-blue-400">
                    <svg viewBox="0 0 24 24" class="w-full h-full">
                        <path fill="currentColor" d="M7 22c-.55 0-1.02-.196-1.412-.587A1.926 1.926 0 0 1 5 20v-9c0-.55.196-1.02.588-1.413A1.926 1.926 0 0 1 7 9h2c0-1.383.488-2.563 1.463-3.538S12.617 4 14 4s2.563.487 3.537 1.462S19 7.617 19 9h2c.55 0 1.02.196 1.413.587c.391.392.587.863.587 1.413v9c0 .55-.196 1.02-.587 1.413A1.926 1.926 0 0 1 21 22H7Zm0-2h14v-9H7v9Zm7-5c1.383 0 2.563-.488 3.537-1.463S19 11.383 19 10h-2c0 .833-.292 1.542-.875 2.125A2.893 2.893 0 0 1 14 13c-.833 0-1.542-.292-2.125-.875A2.893 2.893 0 0 1 11 10H9c0 1.383.488 2.563 1.463 3.537S12.617 15 14 15Zm0-8c.833 0 1.542.292 2.125.875S17 9.167 17 10h-6c0-.833.292-1.542.875-2.125A2.893 2.893 0 0 1 14 7Z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-4 text-blue-400">Aucune commande</h2>
                <p class="text-gray-700 mb-6">Vous n'avez pas encore passé de commande.</p>
                <div class="flex flex-col items-center space-y-4">
                    <a href="<?php echo url('pages/produit.php'); ?>" class="btn btn-small">
                        Découvrir nos produits
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($commandes as $commande): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-lg font-semibold">Commande #<?php echo $commande['id_commande']; ?></h2>
                                <p class="text-sm text-gray-600">
                                    Passée le <?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?>
                                </p>
                            </div>
                        </div>

                        <?php
                        // Récupération des produits de la commande
                        $stmt_produits = $conn->prepare("
                        SELECT cp.*, p.nom, p.image_url 
                        FROM commande_produit cp 
                        JOIN produits p ON cp.id_produit = p.id_produit 
                        WHERE cp.id_commande = ?
                    ");
                        $stmt_produits->bind_param("i", $commande['id_commande']);
                        $stmt_produits->execute();
                        $result_produits = $stmt_produits->get_result();
                        $produits = $result_produits->fetch_all(MYSQLI_ASSOC);
                        ?>

                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <div class="space-y-4">
                                <?php foreach ($produits as $produit): ?>
                                    <div class="flex items-center space-x-4">
                                        <img src="<?php echo url('assets/images/produits/' . htmlspecialchars($produit['image_url'])); ?>"
                                            alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                                            class="w-16 h-16 object-cover rounded">
                                        <div class="flex-1">
                                            <h3 class="font-medium"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                                            <p class="text-sm text-gray-600">
                                                Quantité : <?php echo $produit['quantite']; ?> ×
                                                <?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> €
                                            </p>
                                        </div>
                                        <p class="font-medium">
                                            <?php echo number_format($produit['prix_unitaire'] * $produit['quantite'], 2, ',', ' '); ?> €
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="border-t border-gray-200 mt-4 pt-4">
                                <div class="flex justify-between">
                                    <span class="font-semibold">Total</span>
                                    <span class="font-semibold"><?php echo number_format($commande['montant_total'], 2, ',', ' '); ?> €</span>
                                </div>
                                <?php if ($commande['methode_paiement']): ?>
                                    <p class="text-sm text-gray-600 mt-2">
                                        Payé par <?php echo $commande['methode_paiement']; ?>
                                        le <?php echo date('d/m/Y à H:i', strtotime($commande['date_paiement'])); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
    <?php 
    
    // Inclure le footer seulement si on n'est pas dans la page profil
    if (!isset($is_included_in_profile)) {
        require_once '../includes/_footer.php';
        require_once '../includes/_scripts.php';
    }
    ?>