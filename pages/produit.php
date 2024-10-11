<?php 
session_start();

// Connexion à la base de données
require_once "../includes/_db.php"; 
require_once "../includes/_header.php";

// Initialiser la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Afficher le nombre de produits dans le panier -->

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold">Voirs tous</h2>
            <button class="px-4 py-2 bg-white rounded shadow">Filtrer</button>
        </div>

        <section class="products_list">
            <?php 
            // Requête pour récupérer tous les produits depuis la table 'produits'
            $req = mysqli_query($conn, "SELECT * FROM produits");
            
            if ($req->num_rows > 0) {
                echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">';
                while ($row = mysqli_fetch_assoc($req)) { 
            ?>
                <div class="bg-white rounded-lg shadow-md p-4">
                    <a href="detail.php?id=<?php echo $row['id_produit']; ?>">
                        <div class="aspect-w-1 aspect-h-1 mb-4">
                            <img src="project_images/<?php echo htmlspecialchars($row['img']); ?>" 
                                 alt="<?php echo htmlspecialchars($row['nom']); ?>" 
                                 class="w-full h-full object-cover rounded-lg">
                        </div>
                        <h3 class="font-semibold text-sm"><?php echo htmlspecialchars($row['nom']); ?></h3>
                        <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars($row['description']); ?></p>
                    </a>
                    <div class="flex justify-between items-center mt-2">
                        <span class="font-bold text-sm"><?php echo number_format($row['prix'], 2); ?> €</span>
                        <form action="ajouter_panier.php" method="post">
                            <input type="hidden" name="id_produit" value="<?php echo $row['id_produit']; ?>">
                            <button type="submit" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition">
                                <img src="../assets/images/addCart.png" alt="Ajouter au panier" class="w-5 h-5">
                            </button>
                        </form>
                    </div>
                </div>
            <?php 
                }
                echo '</div>';
            } else {
                echo "<p>Aucun produit disponible.</p>";
            }
            ?>
        </section>
    </div>

</body>
</html>
