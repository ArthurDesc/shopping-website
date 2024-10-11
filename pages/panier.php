<?php 
session_start();
include_once "../includes/_db.php";

// Supprimer les produits si la variable 'del' existe
if (isset($_GET['del'])) {
    $id_del = $_GET['del'];
    unset($_SESSION['panier'][$id_del]);
}

// Mettre à jour la quantité du produit si le formulaire est soumis
if (isset($_POST['update'])) {
    $id_update = $_POST['id_produit'];
    $quantity = $_POST['quantite'];
    
    // Vérifier si la quantité est valide
    if (is_numeric($quantity) && $quantity > 0) {
        $_SESSION['panier'][$id_update] = intval($quantity);
    } else {
        unset($_SESSION['panier'][$id_update]); // Retirer le produit si la quantité n'est pas valide
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="panier">
    <?php include '../includes/_header.php';?>
    <section>
        <table>
            <tr>
                <th></th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Action</th>
            </tr>
            <?php 
            $total = 0;
            // Récupérer les clés du tableau session
            $ids = array_keys($_SESSION['panier']);

            if (empty($ids)) {
                echo '<div class="text-center">';
                echo '<h2 class="text-xl font-semibold mb-4">Panier vide !</h2>';
                echo '<img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">';
                echo '<p class="text-gray-600 mb-6">Votre panier est actuellement vide.</p>';
                echo '<div class="flex flex-col space-y-4">';
                echo '<a href="produit.php" class="bg-blue-500 text-white px-6 py-3 rounded-full hover:bg-blue-600 inline-block">Continuer vos achats</a>';
                echo '<a href="auth.php" class="text-blue-500 underline text-sm px-6 py-3 rounded-full hover:no-underline inline-block">Connectez-vous pour récupérer votre panier</a>';
                echo '</div>';
                echo '</div>';
            } else {
                // Récupérer les produits dans le panier
                $products = mysqli_query($conn, "SELECT * FROM produits WHERE id_produit IN (".implode(',', $ids).")");

                foreach ($products as $product) {
                    // Quantité du produit dans le panier
                    $quantity = $_SESSION['panier'][$product['id_produit']];

                    // S'assurer que la quantité est un entier valide
                    if (!is_numeric($quantity)) {
                        die("La quantité pour le produit ID {$product['id_produit']} n'est pas valide.");
                    }

                    // Calculer le total pour ce produit
                    $product_total = $product['prix'] * intval($quantity);
                    $total += $product_total;

                    // Utilisation de 'htmlspecialchars()' avec vérification des valeurs nulles
                    $img = htmlspecialchars($product['img'] ?? '', ENT_QUOTES, 'UTF-8');
                    $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td><img src="project_images/<?= $img ?>" alt="<?= $nom ?>"></td>
                    <td><?= $nom ?></td>
                    <td><?= number_format($product['prix'], 2); ?>€</td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="id_produit" value="<?= $product['id_produit'] ?>">
                            <input type="number" name="quantite" value="<?= intval($quantity) ?>" min="1">
                            <button type="submit" name="update">Mettre à jour</button>
                        </form>
                    </td>
                    <td><a href="panier.php?del=<?= $product['id_produit']; ?>"><img src="assets/images/delete.png" alt="Supprimer"></a></td>
                </tr>
            <?php 
                }
            }
            ?>
            <tr class="total">
                <th>Total : <?= number_format($total, 2); ?>€</th>
            </tr>
        </table>
    </section>
    <?php include '../includes/_footer.php'; ?>

    <script src="assets/js/script.js" defer></script>
    <script src="assets/js/navbar.js" defer></script>
</body>
</html>
