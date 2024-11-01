<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once '../includes/_db.php';

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
            <div class="text-center p-6 min-h-[50vh] flex flex-col justify-center items-center">
                <h2 class="text-2xl font-bold mb-4 text-blue-400">Aucune commande</h2>
                <p class="text-gray-700 mb-6">Vous n'avez pas encore passé de commande.</p>
                <div class="flex flex-col items-center space-y-4">
                    <a href="produit.php" class="btn btn-small">
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
                                        <img src="../assets/images/produits/<?php echo htmlspecialchars($produit['image_url']); ?>"
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
    }
    ?>