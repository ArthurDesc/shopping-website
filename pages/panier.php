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
    <section class="p-4 bg-white shadow-md rounded-lg">
        <table class="min-w-full">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="p-3">Image</th>
                    <th class="p-3">Nom</th>
                    <th class="p-3">Prix</th>
                    <th class="p-3">Quantité</th>
                    <th class="p-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                // Récupérer les clés du tableau session
                $ids = array_keys($_SESSION['panier']);

                if (empty($ids)) {
                    echo '<div class="text-center p-6 bg-gray-100 rounded-lg shadow-md">'; // Ajout de classes pour le style
                    echo '<h2 class="text-2xl font-bold mb-4 text-blue-700">Panier vide !</h2>'; // Augmentation de la taille de la police et changement de couleur
                    echo '<img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">'; 
                    echo '<p class="text-gray-700 mb-6">Votre panier est actuellement vide.</p>'; // Changement de couleur
                    echo '<div class="flex flex-col space-y-4">'; 
                    echo '<a href="produit.php" class="bg-blue-600 text-white px-1 py-3 rounded-full hover:bg-blue-700 transition duration-200">Continuer vos achats</a>'; // Réduction supplémentaire de la taille sur les côtés
                    echo '<a href="auth.php" class="text-blue-600 underline text-sm px-6 py-3 rounded-full hover:no-underline inline-block">Connectez-vous pour récupérer votre panier</a>'; // Changement de couleur
                    echo '</div>';
                    echo '</div>';
                } else {
                    // Récupérer les produits dans le panier
                    $products = mysqli_query($conn, "SELECT * FROM produits WHERE id_produit IN (".implode(',', $ids).")");

                    foreach ($products as $product) {
                        // Quantité du produit dans le panier
                        $quantity = $_SESSION['panier'][$product['id_produit']];
                        $product_total = $product['prix'] * intval($quantity);
                        $total += $product_total;

                        // Utilisation de 'htmlspecialchars()' avec vérification des valeurs nulles
                        $img = htmlspecialchars($product['image_url'] ?? '', ENT_QUOTES, 'UTF-8');
                        $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <tr class="hover:bg-blue-100 transition duration-200">
                    <td class="p-2">
                        <img src="../assets/images/produits/<?= $img ?>" alt="<?= $nom ?>" class="w-20 h-20 object-cover rounded">
                    </td>
                    <td class="p-2"><?= $nom ?></td>
                    <td class="p-2"><?= number_format($product['prix'], 2); ?>€</td>
                    <td class="p-2">
                        <form method="post" action="">
                            <input type="hidden" name="id_produit" value="<?= $product['id_produit'] ?>">
                            <div class="flex items-center">
                                <button type="submit" name="action" value="decrease" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-1 px-2 rounded-l">-</button>
                                <span class="mx-2"><?= $quantity ?></span>
                                <button type="submit" name="action" value="increase" class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-1 px-2 rounded-r">+</button>
                            </div>
                        </form>
                    </td>
                    <td class="p-2">
                        <a href="panier.php?del=<?= $product['id_produit']; ?>" class="text-red-500 hover:text-red-700 transition duration-200">
                            <img src="../assets/images/supprimer-removebg-preview.png" alt="Supprimer" width="30" height="30">
                        </a>
                    </td>
                </tr>
                <?php 
                    }
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="bg-gray-200">
                    <th colspan="4" class="p-3 text-right">Total :</th>
                    <th class="p-3"><?= number_format($total, 2); ?>€</th>
                </tr>
            </tfoot>
        </table>
    </section>
    
    <?php 
    // Traitement de la mise à jour de la quantité
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit']) && isset($_POST['action'])) {
        $id_update = $_POST['id_produit'];
        
        // Vérifier l'action (augmentation ou diminution)
        if ($_POST['action'] === 'increase') {
            $_SESSION['panier'][$id_update] = isset($_SESSION['panier'][$id_update]) ? $_SESSION['panier'][$id_update] + 1 : 1;
        } elseif ($_POST['action'] === 'decrease') {
            if (isset($_SESSION['panier'][$id_update]) && $_SESSION['panier'][$id_update] > 1) {
                $_SESSION['panier'][$id_update]--;
            } else {
                unset($_SESSION['panier'][$id_update]); // Retirer le produit si la quantité devient 0
            }
        }
    }
    ?>

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
