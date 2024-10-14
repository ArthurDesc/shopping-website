<?php 
session_start();
include_once "../includes/_db.php";

// Initialiser le panier si ce n'est pas déjà fait
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = []; // Initialisation du panier comme tableau vide
}

// Supprimer les produits si la variable 'del' existe
if (isset($_GET['del'])) {
    $id_del = $_GET['del'];
    unset($_SESSION['panier'][$id_del]);
}

// Mettre à jour la quantité du produit si le formulaire est soumis
if (isset($_POST['update'])) {
    $id_update = $_POST['id_produit'];
    $quantity = $_POST['quantite']; // Changez 'stock' en 'quantite'
    
    // Vérifier si la quantité est valide
    if (is_numeric($quantity) && $quantity > 0) {
        $_SESSION['panier'][$id_update] = intval($quantity);
    } else {
        unset($_SESSION['panier'][$id_update]); // Retirer le produit si la quantité n'est pas valide
    }
}
?>

    <?php include '../includes/_header.php';?>
    <section>
        <table>
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
                echo '<tr>'; 
                echo '<th></th>';
                echo '<th>Nom</th>';
                echo '<th>Prix</th>';
                echo '<th>Quantité</th>';
                echo '<th>Action</th>';
                echo '</tr>';
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
                    $img = htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8');
                    $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td><img src="project_images/<?= $img ?>" alt="<?= $nom ?>"></td>
                    <td><?= $nom ?></td>
                    <td><?= number_format($product['prix'], 2); ?>€</td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="id_produit" value="<?= $product['id_produit'] ?>">
                            <select name="quantite" id="quantity-<?= $product['id_produit'] ?>" onchange="updateQuantity(<?= $product['id_produit'] ?>)">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?= $i ?>" <?= $i == intval($quantity) ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </form>
                    </td>
                    <td><a href="panier.php?del=<?= $product['id_produit']; ?>"><img src="../assets/images/supprimer-removebg-preview.png" alt="Supprimer" width="30" height="30"></a></td>
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

    <script src="../assets/js/script.js" defer></script>
    <script src="../assets/js/navbar.js" defer></script>
    <script>
        function updateQuantity(productId) {
            const form = document.querySelector(`input[name="id_produit"][value="${productId}"]`).closest('form');
            console.log("Submitting form for product ID:", productId); // Ligne de débogage
            form.submit(); // Soumettre le formulaire pour mettre à jour la quantité
        }
    </script>
</body>
</html>
