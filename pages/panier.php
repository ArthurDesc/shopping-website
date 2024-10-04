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

// Initialiser un tableau pour stocker les produits du panier
$panier = [];

// Récupérer les produits du panier depuis la base de données
$query = "SELECT cp.id_produit, p.nom, cp.quantite, p.prix 
          FROM commande_produit cp
          INNER JOIN produits p ON cp.id_produit = p.id_produit
          INNER JOIN commandes c ON cp.id_commande = c.id_commande
          WHERE c.id_utilisateur = :id_utilisateur AND c.statut = 'panier'"; // statut 'panier' pour récupérer seulement les commandes non finalisées

$stmt = $conn->prepare($query);
$stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
$stmt->execute();

$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        // Mettre à jour la quantité dans la base de données
        $update_query = "UPDATE commande_produit SET quantite = :quantite 
                         WHERE id_produit = :id_produit AND id_commande = (
                             SELECT id_commande FROM commandes WHERE id_utilisateur = :id_utilisateur AND statut = 'panier'
                         )";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':quantite', $nouvelle_quantite, PDO::PARAM_INT);
        $update_stmt->bindParam(':id_produit', $produit_id, PDO::PARAM_INT);
        $update_stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $update_stmt->execute();
    }

    // Supprimer un produit du panier
    if (isset($_POST['supprimer_produit'])) {
        $produit_id = $_POST['produit_id'];

        // Supprimer l'élément de la table commande_produit
        $delete_query = "DELETE FROM commande_produit WHERE id_produit = :id_produit 
                         AND id_commande = (
                             SELECT id_commande FROM commandes WHERE id_utilisateur = :id_utilisateur AND statut = 'panier'
                         )";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->bindParam(':id_produit', $produit_id, PDO::PARAM_INT);
        $delete_stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $delete_stmt->execute();
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
                <?php foreach ($panier as $produit) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                                <input type="number" name="quantite" value="<?php echo $produit['quantite']; ?>" min="1">
                                <button type="submit" name="modifier_quantite">Modifier</button>
                            </form>
                        </td>
                        <td><?php echo number_format($produit['prix']); ?> €</td>
                        <td><?php echo number_format($produit['prix'] * $produit['quantite']); ?> €</td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="produit_id" value="<?php echo $produit['id_produit']; ?>">
                                <button type="submit" name="supprimer_produit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total : <?php echo number_format($total); ?> €</h3>

        <form action="valider_commande.php" method="post">
            <button type="submit">Valider la commande</button>
        </form>
    <?php endif; ?>
</body>
</html>
