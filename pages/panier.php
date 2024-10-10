<?php
session_start();
require_once __DIR__ . '/../includes/_db.php'; // Assurez-vous que le chemin est correct


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panier</title>
    </head>
    <body>
        <?php require_once '../includes/_header.php';?>
        <main>

            <div class="flex items-start justify-center min-h-screen pt-20">
                <div class="container max-w-md mx-auto px-4">
                    <?php  $panier = array_keys($_SESSION['panier']);
                     if (empty($panier)) : ?>
                <div class="text-center">
                <h2 class="text-xl font-semibold mb-4">Panier vide !</h2>
                <img src="<?php echo BASE_URL; ?>assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">
                <p class="text-gray-600 mb-6">Votre panier est actuellement vide.</p>
                <div class="flex flex-col space-y-4">
                    <a href="<?php echo BASE_URL; ?>pages/produit.php" class="bg-blue-500 text-white px-6 py-3 rounded-full hover:bg-blue-600 inline-block">Continuer vos achats</a>
                    <a href="<?php echo BASE_URL; ?>pages/auth.php" class="text-blue-500 underline text-sm px-6 py-3 rounded-full hover:no-underline inline-block">Connectez vous pour récupérer votre panier</a>
                </div>
            </div>
                <?php
                    else :
                        $produit = mysqli_query($conn, "SELECT * FROM produits WHERE id_produit IN(".implode(',', $panier).")");
                     
                ?>
                    <div class="space-y-6">
                        <?php foreach ($produit as $item) : ?>
                        <div class="flex items-center justify-between border-b border-gray-200 py-4">
                            <div class="flex items-center">
                                <img src="assets/images/products/<?=$item['id_produit']?>.jpg" alt="<?=$item['nom']?>" class="w-20 h-20 object-cover rounded-lg mr-4">
                                <div>
                                    <?=$_SESSION['panier'][$item['id_produit']]?>
                                    <h3 class="font-semibold text-lg"><?=$item['nom']?></h3>
                                    <p class="text-gray-600"><?=$item['prix']?>€</p>
                                </div>
                            </div>
                            <?php  

                            ?>
                            <div class="flex items-center">
                                <form action="" method="post" class="flex items-center mr-4">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['id_produit']; ?>">
                                    <input type="number" name="quantite" value="<?php echo $_SESSION['panier'][$item['id_produit']]['quantite']; ?>" min="1" class="w-16 text-center border rounded p-1">
                                    <button type="submit" name="modifier_quantite" class="ml-2 text-blue-500"><i class="fas fa-sync-alt"></i></button>
                                </form>
                                <form action="" method="post">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['id_produit']; ?>">
                                    <button type="submit" name="supprimer_produit" class="text-red-500"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                            <?php endforeach; ?>
                            <div class="text-right">
                                <?php $total = 0; // Initialiser le total
                                foreach ($produit as $item) {
                                    if (isset($_SESSION['panier'][$item['id_produit']])) {
                                        $quantite = $_SESSION['panier'][$item['id_produit']]['quantite']; // Assurez-vous que la quantité est correctement référencée
                                        $total += $item['prix'] * $quantite;
                                    }
                                } ?>
                                <h3 class="text-2xl font-semibold">Total : <?php echo number_format($total, 2); ?> €</h3>
                            </div>
                    </div>

                    <div class="text-center mt-8">
                        <a href="<?php echo BASE_URL; ?>pages/valider_commande.php" class="bg-green-500 text-white px-8 py-3 rounded-full hover:bg-green-600 inline-block">Valider la commande</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <?php include '../includes/_footer.php'; ?>

        <script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
        <script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>

    </body>
</html>