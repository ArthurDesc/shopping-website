<?php
session_start(); // Démarrer la session pour utiliser le panier

// Connexion à la base de données
require_once '../includes/_db.php'; // Inclure le fichier de connexion à la base de données
require_once '../includes/_header.php';

// Traiter l'ajout au panier
if (isset($_POST['id_produit'])) {
    $id_produit = $_POST['id_produit'];

    // Vérifiez si le panier existe dans la session, sinon le créer
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = []; // Initialise le panier comme un tableau vide
    }

    // Ajouter le produit au panier s'il n'est pas déjà présent
    if (!in_array($id_produit, array_column($_SESSION['panier'], 'id_produit'))) {
        // Récupérer les détails du produit à partir de la base de données
        $query = "SELECT * FROM produits WHERE id_produit = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $produit = $result->fetch_assoc();
            // Ajouter les détails du produit dans le panier
            $_SESSION['panier'][] = [
                'id_produit' => $produit['id_produit'],
                'nom' => $produit['nom'],
                'prix' => $produit['prix'],
                'quantite' => 1 // Vous pouvez gérer les quantités ici
            ];
            echo "<p>Produit ajouté au panier.</p>";
        } else {
            echo "<p>Produit non trouvé.</p>";
        }
    } else {
        echo "<p>Le produit est déjà dans le panier.</p>";
    }
}

// Requête pour récupérer les produits
$query = "SELECT * FROM produits";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits</title>
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS -->
</head>
<body>

<div class="container">
    <?php
    // Vérifiez s'il y a des produits
    if ($result->num_rows > 0) {
        echo '<div class="product-grid">';

        // Boucle pour afficher chaque produit
        while ($produit = $result->fetch_assoc()) {
            ?>
            <div class="product-item">
                <h2><?php echo htmlspecialchars($produit['nom']); ?></h2>
                <p><?php echo htmlspecialchars($produit['description']); ?></p>
                <p>Prix : <?php echo number_format($produit['prix'], 2); ?> €</p>
                <form action="" method="post">
                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                    <button type="submit">Ajouter au panier</button>
                </form>
            </div>
            <?php
        }

        echo '</div>';
    } else {
        echo "<p>Aucun produit trouvé.</p>";
    }

    // Fermer la connexion
    $conn->close();
    ?>
</div>

</body>
</html>
