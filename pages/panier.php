<?php
session_start();
require_once '../includes/session.php'; // Assurez-vous que ce fichier existe et est correctement configuré
require_once '../includes/_db.php'; // Le fichier de connexion à la base de données

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
$query = "SELECT commande_produit.id_produit, produits.nom, commande_produit.quantite, produits.prix 
          FROM commande_produit 
          INNER JOIN produits  ON commande_produit.id_produit = produits.id_produit
          INNER JOIN commandes ON commande_produit.id_commande = commandes.id_commande
          WHERE commandes.id_utilisateur = ? AND commandes.statut = 'panier'";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $id_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    $panier = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

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
        $update_query = "UPDATE commande_produit SET quantite = ? 
                         WHERE id_produit = ? AND id_commande = (
                             SELECT id_commande FROM commandes WHERE id_utilisateur = ? AND statut = 'panier'
                         )";
        if ($update_stmt = $conn->prepare($update_query)) {
            $update_stmt->bind_param('iii', $nouvelle_quantite, $produit_id, $id_utilisateur);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }

    // Supprimer un produit du panier
    if (isset($_POST['supprimer_produit'])) {
        $produit_id = $_POST['produit_id'];

        // Supprimer l'élément de la table commande_produit
        $delete_query = "DELETE FROM commande_produit WHERE id_produit = ? 
                         AND id_commande = (SELECT id_commande FROM commandes WHERE id_utilisateur = ? AND statut = 'panier')";
        if ($delete_stmt = $conn->prepare($delete_query)) {
            $delete_stmt->bind_param('ii', $produit_id, $id_utilisateur);
            $delete_stmt->execute();
            $delete_stmt->close();
        }
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
                        <td><?php echo number_format($produit['prix'], 2); ?> €</td>
                        <td><?php echo number_format($produit['prix'] * $produit['quantite'], 2); ?> €</td>
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

        <h3>Total : <?php echo number_format($total, 2); ?> €</h3>

        <form action="valider_commande.php" method="post">
            <button type="submit">Valider la commande</button>
        </form>
    <?php endif; ?>
</body>
</html>