<?php 
ob_start(); // Démarre la mise en mémoire tampon de sortie
session_start();
include_once "../includes/_db.php";
require_once "../classe/Panier.php";

$panier = new Panier();

// Traitement de la mise à jour de la quantité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit']) && isset($_POST['action'])) {
    $id_update = $_POST['id_produit'];
    
    // Vérifier l'action (augmentation ou diminution)
    if ($_POST['action'] === 'increase') {
        $panier->augmenterQuantite($id_update);
    } elseif ($_POST['action'] === 'decrease') {
        $panier->diminuerQuantite($id_update);
    }
    header("Location: panier.php"); // Rediriger pour éviter le rafraîchissement
    exit(); // Terminer le script après la redirection
}

// Supprimer les produits
if (isset($_GET['del'])) {
    $id_del = $_GET['del'];
    $panier->retirerProduit($id_del);
}

// Mettre à jour la quantité du produit
if (isset($_POST['update'])) {
    $id_update = $_POST['id_produit'];
    $quantity = $_POST['quantite'];
    
    // Vérifier si la quantité est valide
    if (is_numeric($quantity) && $quantity > 0) {
        $panier->mettreAJourQuantite($id_update, intval($quantity));
    } else {
        $panier->retirerProduit($id_update); // Retirer le produit si la quantité n'est pas valide
    }
}

// Inclusion du header
include '../includes/_header.php';
?>

<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Votre Panier</h1>
    <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Liste des produits -->
        <div class="w-full lg:w-2/3 mb-8 lg:mb-0">
            <?php 
            $total = 0;
            $contenuPanier = $panier->getContenu();

            if (empty($contenuPanier)) {
                echo '<div class="text-center p-6 bg-gray-100 rounded-lg shadow-md">'; 
                echo '<h2 class="text-2xl font-bold mb-4 text-red-600">Panier vide !</h2>'; 
                echo '<img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">'; 
                echo '<p class="text-gray-700 mb-6">Votre panier est actuellement vide.</p>'; 
                echo '<div class="flex flex-col space-y-4">'; 
                echo '<a href="produit.php" class="bg-blue-600 text-white px-6 py-3 rounded-full hover:bg-blue-700 transition duration-200">Continuer vos achats</a>'; 
                echo '<a href="auth.php" class="text-blue-600 underline text-sm px-6 py-3 rounded-full hover:no-underline inline-block">Connectez-vous pour récupérer votre panier</a>'; 
                echo '</div>';
                echo '</div>';
            } else {
                $ids = array_map(function($key) {
                    return explode('_', $key)[0];
                }, array_keys($contenuPanier));
                $ids = array_unique($ids);

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql = "SELECT * FROM produits WHERE id_produit IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($product = $result->fetch_assoc()) {
                    foreach ($contenuPanier as $key => $quantity) {
                        list($id, $taille) = explode('_', $key . '_');
                        if ($id == $product['id_produit']) {
                            $product_total = $product['prix'] * intval($quantity);
                            $total += $product_total;

                            $img = $product['image_url'] ?? '';
                            $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');

                            if (is_array($img)) {
                                $img = htmlspecialchars($img[0] ?? 'default-image.png', ENT_QUOTES, 'UTF-8');
                            } else {
                                $img = htmlspecialchars($img ?? 'default-image.png', ENT_QUOTES, 'UTF-8');
                            }
                            ?>
                            <div class="flex items-center border-b border-gray-200 py-4">
                                <img src="../assets/images/produits/<?= $img ?>" alt="<?= $nom ?>" class="w-24 h-24 object-cover rounded mr-4">
                                <div class="flex-grow">
                                    <h3 class="font-semibold"><?= $nom ?> <?= $taille ? "(Taille: $taille)" : '' ?></h3>
                                    <p class="text-gray-600"><?= number_format($product['prix'], 2); ?>€</p>
                                    <form method="post" action="" class="flex items-center mt-2">
                                        <input type="hidden" name="id_produit" value="<?= $key ?>">
                                        <button type="submit" name="action" value="decrease" class="bg-gray-200 text-gray-600 px-2 py-1 rounded-l">-</button>
                                        <span class="px-4 py-1 bg-gray-100"><?= $quantity ?></span>
                                        <button type="submit" name="action" value="increase" class="bg-gray-200 text-gray-600 px-2 py-1 rounded-r">+</button>
                                    </form>
                                </div>
                                <a href="panier.php?del=<?= urlencode($key); ?>" class="text-red-500 hover:text-red-700 ml-4">
                                    <img src="../assets/images/supprimer-removebg-preview.png" alt="Supprimer" width="24" height="24">
                                </a>
                            </div>
                            <?php 
                        }
                    }
                }
            }
            ?>
        </div>
        
        <!-- Résumé du panier -->
        <div class="w-full lg:w-1/3">
            <div class="bg-gray-50 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Résumé du Panier</h2>
                <div class="mb-4">
                    <p class="text-lg">Total à payer :</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($total, 2); ?>€</p>
                </div>
                <div class="flex flex-col space-y-2">
                    <a href="process_paiement.php" class="bg-green-600 text-white px-4 py-2 rounded-full hover:bg-green-700 transition duration-200 text-center <?= empty($contenuPanier) ? 'opacity-50 cursor-not-allowed' : '' ?>" <?= empty($contenuPanier) ? 'onclick="return false;"' : '' ?>>Payer</a>
                    <a href="produit.php" class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition duration-200 text-center">Continuer vos achats</a>
                    <?php if (!isset($_SESSION['id_utilisateur'])) { 
                        echo '<a href="auth.php" class="text-blue-600 underline text-sm text-center">Se connecter pour récupérer votre panier</a>';
                    } ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/_footer.php'; ?>
