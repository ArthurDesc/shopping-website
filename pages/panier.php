<?php
session_start();
require_once '../includes/session.php';
require_once '../includes/_db.php';

if (!is_logged_in()) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$erreurs = [];
$success_message = "";

// Récupération des produits du panier (en supposant que le panier est stocké dans la session)
$panier = isset($_SESSION['panier']) ? $_SESSION['panier'] : [];

// Calcul du total
$total = 0;
foreach ($panier as $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}

// Gestion de la modification des quantités et suppression
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Modifier la quantité
    if (isset($_POST['modifier_quantite'])) {
        $produit_id = $_POST['produit_id'];
        $nouvelle_quantite = $_POST['quantite'];

        // Vérifier si le produit existe dans le panier
        if (isset($panier[$produit_id])) {
            $_SESSION['panier'][$produit_id]['quantite'] = $nouvelle_quantite;
        }
    }

    // Supprimer un produit 
    if (isset($_POST['supprimer_produit'])) {
        $produit_id = $_POST['produit_id'];
        unset($_SESSION['panier'][$produit_id]);
    }

    // Redirection pour éviter les doubles soumissions de formulaire
    header("Location: panier.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
</head>
<body>
    <h1>Votre Panier</h1>

    <?php if (empty($panier)) : ?>
        <p>Votre panier est vide.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $produit_id => $produit) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
                                <input type="number" name="quantite" value="<?php echo $produit['quantite']; ?>" min="1">
                                <button type="submit" name="modifier_quantite">Modifier</button>
                            </form>
                        </td>
                        <td><?php echo number_format($produit['prix'], 2); ?> €</td>
                        <td><?php echo number_format($produit['prix'] * $produit['quantite'], 2); ?> €</td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="produit_id" value="<?php echo $produit_id; ?>">
                                <button type="submit" name="supprimer_produit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total : <?php echo number_format($total, 2); ?> €</h3>

        <form action="valider_commande.php" method="post">
            <button type="submit">Valider la commande</button>
        </form>
    <?php endif; ?>
</body>
</html>
