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
                echo '<button onclick="window.location.href=\'produit.php\'" class="sm:w-80 md:w-80 lg:w-80 mx-auto bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-4">Continuer vos achats</button>';
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
                    $img = htmlspecialchars($product['image_url'] ?? '', ENT_QUOTES, 'UTF-8');
                    $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
                <tr>
                    <td>
                        <img src="../assets/images/produits/<?= $img ?>" alt="<?= $nom ?>" class="w-20 h-20 object-cover">
                    </td>
                    <td><?= $nom ?></td>
                    <td><?= number_format($product['prix'], 2); ?>€</td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="id_produit" value="<?= $product['id_produit'] ?>">
                            <button type="button" class="change-quantity" onclick="changeQuantity(<?= $product['id_produit'] ?>, -1)">-</button>
                            <input type="number" name="quantite" value="<?= intval($quantity) ?>" min="1" id="quantity-<?= $product['id_produit'] ?>" onchange="updateQuantity(<?= $product['id_produit'] ?>)">
                            <button type="button" class="change-quantity" onclick="changeQuantity(<?= $product['id_produit'] ?>, 1)">+</button>
                            <button type="submit" name="update" style="display:none;">Mettre à jour</button> <!-- Cacher le bouton de mise à jour -->
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

    <script src="../assets/js/script.js" defer></script>
    <script src="../assets/js/navbar.js" defer></script>
    <script>
        function changeQuantity(productId, change) {
            const quantityInput = document.getElementById('quantity-' + productId);
            let currentQuantity = parseInt(quantityInput.value);
            currentQuantity += change;
            if (currentQuantity < 1) currentQuantity = 1; // Ne pas permettre une quantité inférieure à 1
            quantityInput.value = currentQuantity;
            updateQuantity(productId); // Appeler la fonction pour mettre à jour automatiquement
        }

        function updateQuantity(productId) {
            const form = document.querySelector(`input[name="id_produit"][value="${productId}"]`).closest('form');
            console.log("Submitting form for product ID:", productId); // Debugging line
            form.submit(); // Soumettre le formulaire pour mettre à jour la quantité
        }
    </script>
</body>
</html>
