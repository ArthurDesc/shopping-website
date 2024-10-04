<?php
session_start();
require_once '../includes/session.php';
require_once '../includes/_db.php'; // Assurez-vous que ce fichier contient la connexion à la base de données
require_once '../includes/_header.php';

if (!is_logged_in()) {
    echo '<div class="flex flex-col space-y-4 md:flex-row md:space-x-4 md:space-y-0">
            <a href="' . BASE_URL . 'pages/connexion.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                Connexion
            </a>
            <a href="' . BASE_URL . 'pages/inscription.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                Inscription
            </a>
        </div>';
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];
$erreurs = [];
$success_message = "";
// Initialiser un tableau pour stocker les produits du panier

// Initialiser un tableau pour stocker les produits du panier
$panier = [];

// Récupérer les produits du panier depuis la base de données
$query = "SELECT cp.id_produit, p.nom, cp.quantite, p.prix 
          FROM commande_produit cp
          INNER JOIN produits p ON cp.id_produit = p.id_produit
          INNER JOIN commandes c ON cp.id_commande = c.id_commande
          WHERE c.id_utilisateur = ? AND c.statut = 'panier'"; // statut 'panier' pour récupérer seulement les commandes non finalisées

$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$panier = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

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
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param('iii', $nouvelle_quantite, $produit_id, $id_utilisateur);
        $update_stmt->execute();
        $update_stmt->close();
    }

    // Supprimer un produit du panier
    if (isset($_POST['supprimer_produit'])) {
        $produit_id = $_POST['produit_id'];

        // Supprimer l'élément de la table commande_produit
        $delete_query = "DELETE FROM commande_produit WHERE id_produit = ? 
                         AND id_commande = (
                             SELECT id_commande FROM commandes WHERE id_utilisateur = ? AND statut = 'panier'
                         )";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param('ii', $produit_id, $id_utilisateur);
        $delete_stmt->execute();
        $delete_stmt->close();
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