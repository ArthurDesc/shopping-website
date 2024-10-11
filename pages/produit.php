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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4">
   

    <div class="mt-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold font-medium">Voir tous</h2>
        <button class="px-4 py-2 bg-white rounded shadow font-medium">Filtrer</button>
    </div>

    <?php
    if ($result->num_rows > 0) {
        echo '<div class="grid grid-cols-2 gap-6 mt-6">';
        while ($produit = $result->fetch_assoc()) {
    ?>
        <div class="bg-white rounded-lg shadow-md p-4">
            <a href="detail.php?id=<?php echo $produit['id_produit']; ?>">
                <div class="aspect-w-1 aspect-h-1 mb-4">
                    <img src="<?php echo htmlspecialchars($produit['image_url'] ?? ''); ?>" 
                         alt="<?php echo htmlspecialchars($produit['nom'] ?? ''); ?>" 
                         class="w-full h-full object-cover rounded-lg">
                </div>
                <h3 class="font-semibold text-lg font-medium"><?php echo htmlspecialchars($produit['nom'] ?? ''); ?></h3>
                <p class="text-sm text-gray-600 mt-2 font-medium"><?php echo htmlspecialchars($produit['description'] ?? ''); ?></p>
            </a>
            <div class="flex justify-between items-center mt-4">
                <span class="font-bold text-lg font-medium"><?php echo number_format($produit['prix'] ?? 0, 2); ?> €</span>
                <form action="" method="post">
                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit'] ?? ''; ?>">
                    <button type="submit" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition">
                        <img src="../assets/images/addCart.png" alt="Ajouter au panier" class="w-6 h-6">
                    </button>
                </form>
            </div>
        </div>
    <?php
        }
        echo '</div>';
    } else {
        echo "<p class='mt-6 text-center font-medium'>Aucun produit trouvé.</p>";
    }
    ?>
</div>

</body>
</html>
