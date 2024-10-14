<?php 
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
  }

// Connexion à la base de données
require_once "../includes/_db.php"; 

// Initialiser la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Définir le chemin de base pour les images des produits
$image_base_path = '../assets/images/produits/';
?>

<?php require_once '../includes/_header.php'; ?>

<div class="container mx-auto px-4">
    <div class="mt-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold">Voir tous</h2>
        <button class="px-4 py-2 bg-white rounded shadow">Filtrer</button>
    </div>

    <section class="products_list">
        <?php 
        // Requête pour récupérer tous les produits depuis la table 'produits'
        $req = mysqli_query($conn, "SELECT * FROM produits");
        
        if ($req->num_rows > 0) {
            echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6 mt-6">';
            while ($row = mysqli_fetch_assoc($req)) { 
                // Construire le chemin de l'image
                $image_url = $image_base_path . ($row['image_url'] ?? 'default_product.jpg');
                
                // Vérifier si l'image existe, sinon utiliser l'image par défaut
                if (!file_exists($image_url) || empty($row['image_url'])) {
                    $image_url = $image_base_path . 'default_product.jpg';
                }
        ?>
            <div class="bg-white rounded-lg shadow-md p-4">
                <a href="detail.php?id=<?php echo $row['id_produit']; ?>">
                    <div class="aspect-w-1 aspect-h-1 mb-4">
                        <img src="<?php echo htmlspecialchars($image_url); ?>" 
                             alt="<?php echo htmlspecialchars($row['nom'] ?? 'Produit sans nom'); ?>" 
                             class="w-full h-full object-cover rounded-lg">
                    </div>
                    <h3 class="font-semibold text-sm sm:text-base"><?php echo htmlspecialchars($row['nom']); ?></h3>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 line-clamp-2"><?php echo htmlspecialchars($row['description']); ?></p>
                </a>
                <div class="flex justify-between items-center mt-4">
                    <span class="font-bold text-sm sm:text-base"><?php echo number_format($row['prix'], 2); ?> €</span>
                    <form action="ajouter_panier.php" method="get">
                        <input type="hidden" name="id_produit" value="<?php echo $row['id_produit']; ?>">
                        <button type="submit" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition">
                            <img src="../assets/images/addCart.png" alt="Ajouter au panier" class="w-4 h-4 sm:w-6 sm:h-6">
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
<?php include '../includes/_footer.php'; ?>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
</body>
</html>
