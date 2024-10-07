<?php
session_start();
require_once '../includes/session.php';
require_once '../includes/_db.php';

$erreurs = [];
$success_message = "";
$panier = [];
$total = 0;

// Vérifier si l'utilisateur est connecté
if (is_logged_in()) {
    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Récupérer les produits du panier depuis la base de données
    $query = "SELECT cp.id_produit, p.nom, cp.quantite, p.prix 
              FROM commande_produit cp
              INNER JOIN produits p ON cp.id_produit = p.id_produit
              INNER JOIN commandes c ON cp.id_commande = c.id_commande
              WHERE c.id_utilisateur = ? AND c.statut = 'panier'";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    $panier = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Calcul du total panier
    foreach ($panier as $produit) {
        $total += $produit['prix'] * $produit['quantite'];
    }

    // Traitement des actions POST (modifier quantité, supprimer produit)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ... (le code pour modifier la quantité et supprimer un produit reste inchangé)
        
        header("Location: panier.php");
        exit();
    }
}
require_once '../includes/_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
</head>
<body>
 
<main>

<div class="flex items-start justify-center min-h-screen pt-20">
    <div class="container max-w-md mx-auto px-4">
    <?php if (empty($panier)) : ?>
        <div class="text-center">
            <h2 class="text-xl font-semibold mb-4">Panier vide !</h2>
            <img src="<?php echo BASE_URL; ?>assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">
            <p class="text-gray-600 mb-6">Votre panier est actuellement vide.</p>
            <div class="flex flex-col space-y-4">
                <a href="<?php echo BASE_URL; ?>pages/produit.php" class="bg-blue-500 text-white px-6 py-3 rounded-full hover:bg-blue-600 inline-block">Continuer vos achats</a>
                <a href="<?php echo BASE_URL; ?>pages/auth.php" class="text-blue-500 underline text-sm px-6 py-3 rounded-full hover:no-underline inline-block">Connectez vous pour récupérer votre panier</a>
            </div>
        </div>
    <?php else : ?>
        <div class="space-y-6">
            <?php foreach ($panier as $produit) : ?>
                <div class="flex items-center justify-between border-b border-gray-200 py-4">
                    <div class="flex items-center">
                        <img src="<?php echo BASE_URL; ?>assets/images/products/<?php echo $produit['id_produit']; ?>.jpg" alt="<?php echo htmlspecialchars($produit['nom']); ?>" class="w-20 h-20 object-cover rounded-lg mr-4">
                        <div>
                            <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($produit['nom']); ?></h3>
                            <p class="text-gray-600"><?php echo number_format($produit['prix'], 2); ?> €</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <form action="" method="post" class="flex items-center mr-4">
                            <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                            <input type="number" name="quantite" value="<?php echo $produit['quantite']; ?>" min="1" class="w-16 text-center border rounded p-1">
                            <button type="submit" name="modifier_quantite" class="ml-2 text-blue-500"><i class="fas fa-sync-alt"></i></button>
                        </form>
                        <form action="" method="post">
                            <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                            <button type="submit" name="supprimer_produit" class="text-red-500"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="text-right">
                <h3 class="text-2xl font-semibold">Total : <?php echo number_format($total, 2); ?> €</h3>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="<?php echo BASE_URL; ?>pages/valider_commande.php" class="bg-green-500 text-white px-8 py-3 rounded-full hover:bg-green-600 inline-block">Valider la commande</a>
        </div>
    <?php endif; ?>
</div>
</div>
</main>

<?php include '../includes/_footer.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>

</body>
</html>