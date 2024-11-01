<?php
ob_start();
include_once "../includes/_db.php";
require_once "../includes/session.php";
require_once "../classe/Panier.php";

$panier = new Panier();

// Inclusion du header
include '../includes/_header.php';
?>

<main class="flex-grow container mx-auto px-4 py-8 mt-16">
    
    <?php
    $total = 0;
    $contenuPanier = $panier->getContenu();
    ?>
    <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Liste des produits -->
        <div class="w-full <?= !empty($contenuPanier) ? 'lg:w-2/3' : '' ?> mb-8 lg:mb-0">
            <?php if (empty($contenuPanier)): ?>
                <div class="text-center p-6">
                    <h2 class="text-2xl font-bold mb-4 text-blue-400">Panier vide !</h2>
                    <img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">
                    <p class="text-gray-700 mb-6">Votre panier est actuellement vide.</p>
                    <div class="flex flex-col items-center space-y-4">
                        <a href="produit.php" class="btn btn-small">Continuer vos achats</a>
                        <?php if (!isset($_SESSION['id_utilisateur'])): ?>
                            <a href="auth.php" class="text-blue-600 underline text-sm px-6 py-3 rounded-full hover:no-underline">
                                Connectez-vous pour récupérer votre panier
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="cart-container rounded-3xl p-4 space-y-4">
                    <?php
                    $ids = array_map(function ($key) {
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
                                <div class="cart-item flex items-center bg-white border border-gray-100 rounded-2xl h-32 shadow-md hover:shadow-lg transition-shadow duration-200">
                                    <!-- Lien sur l'image -->
                                    <a href="detail.php?id=<?= $product['id_produit'] ?>" class="h-32 w-32">
                                        <img src="../assets/images/produits/<?= $img ?>" 
                                             alt="<?= $nom ?>" 
                                             class="h-full w-full object-cover rounded-l-2xl">
                                    </a>
                                    <div class="flex-grow pl-6">
                                        <!-- Lien sur le nom -->
                                        <a href="detail.php?id=<?= $product['id_produit'] ?>" 
                                           class="inline-block">
                                            <h3 class="font-semibold hover:text-blue-600 transition-colors duration-200">
                                                <?= $nom ?> <?= $taille ? "(Taille: $taille)" : '' ?>
                                            </h3>
                                        </a>
                                        <p class="text-gray-600"><?= number_format($product['prix'], 2); ?>€</p>
                                        <form method="post" action="panier.php" class="flex items-center mt-2" data-stock="<?= $product['stock'] ?>">
                                            <input type="hidden" name="id_produit" value="<?= $id ?>"> <?php if ($taille): ?>
                                                <input type="hidden" name="taille" value="<?= $taille ?>">
                                            <?php endif; ?>
                                            <div class="quantity-control">
                                                <button type="button" value="decrease" class="quantity-btn">-</button>
                                                <span class="quantity-display"><?= $quantity ?></span>
                                                <button type="button" value="increase" class="quantity-btn">+</button>
                                            </div>
                                        </form>
                                    </div>
                                    <a href="panier.php?del=<?= urlencode($key); ?>" 
                                       class="text-red-500 hover:text-red-700 ml-4 pr-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </a>
                                </div>
                    <?php
                            }
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($contenuPanier)): ?>
            <!-- Résumé du panier -->
            <div class="w-full lg:w-1/3">
                <div class="bg-gray-50 rounded-lg shadow-lg p-4 space-y-4">
                    <h2 class="text-xl font-bold mb-4">Résumé du Panier</h2>
                    <div class="mb-4">
                        <p class="text-lg">Total à payer :</p>
                        <p class="text-2xl font-bold text-green-600" id="total-price"><?= number_format($total, 2); ?>€</p>
                    </div>
                    <div class="flex flex-col space-y-2">
                        <?php if (isset($_SESSION['id_utilisateur'])): ?>
                            <a href="process_paiement.php" class="button button-green">
                                Procéder au paiement
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="auth.php" class="button button-green">
                                Se connecter pour payer
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </a>
                        <?php endif; ?>
                        <a href="produit.php" class="button">
                            Continuer vos achats
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- Scripts -->

<?php include '../includes/_footer.php'; ?>