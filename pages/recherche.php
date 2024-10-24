<?php
require_once '../includes/_db.php';
require_once '../includes/_header.php';


// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

$search = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ?");
$searchTerm = '%' . $search . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Définir le chemin de base pour les images des produits
$image_base_path = '../assets/images/produits/';
?>

<div class="container mx-auto px-4">
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-xl font-semibold text-center sm:text-left">Résultats de recherche pour "<?php echo htmlspecialchars($search); ?>"</h2>
        <!-- Remplacement de la barre de recherche -->
        <form method="get" action="" class="flex items-center mt-4 sm:mt-0">
            <div class="wave-group">
                <input required type="text" name="q" id="input" class="input" value="<?php echo htmlspecialchars($search); ?>" style="width: 100%; max-width: 600px; padding-right: 40px;"> <!-- Agrandi la barre de recherche pour la version ordinateur -->
                <span class="bar"></span>
                <label class="label">
                    <span class="label-char" style="--index: 0">R</span>
                    <span class="label-char" style="--index: 1">e</span>
                    <span class="label-char" style="--index: 2">c</span>
                    <span class="label-char" style="--index: 3">h</span>
                    <span class="label-char" style="--index: 4">e</span>
                    <span class="label-char" style="--index: 5">r</span>
                    <span class="label-char" style="--index: 6">c</span>
                    <span class="label-char" style="--index: 7">h</span>
                    <span class="label-char" style="--index: 8">e</span>
                </label>
                <button type="submit" class="search-button" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%);"> <!-- Positionnement de l'icône -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Champ de recherche avec autocomplétion -->
    

    <section class="products_list">
        <?php 
        if ($result->num_rows > 0) {
            echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6 mt-6">';
            while ($row = $result->fetch_assoc()) { 
                // Construire le chemin de l'image
                $image_url = $image_base_path . ($row['image_url'] ?? 'default_product.jpg');
                
                // Vérifier si l'image existe, sinon utiliser l'image par défaut
                if (!file_exists($image_url) || empty($row['image_url'])) {
                    $image_url = $image_base_path . 'default_product.jpg';
                }
        ?>
            <div class="bg-white rounded-lg shadow-md p-4">
                <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $row['id_produit']; ?>" class="block">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>" class="w-full h-48 object-cover mb-4">
                    <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($row['nom']); ?></h3>
                    <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($row['marque']); ?></p>
                    <p class="text-blue-600 font-bold"><?php echo number_format($row['prix'], 2); ?> €</p>
                </a>
                <div class="mt-2 flex justify-between items-center">
                    <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $row['id_produit']; ?>" class="text-blue-500 hover:underline">Voir détails</a>
                    <form method="post" action="">
                        <input type="hidden" name="id_produit" value="<?php echo $row['id_produit']; ?>">
                        <button type="submit" name="ajouter_au_panier" class="add-to-cart">
                            <img src="<?php echo BASE_URL; ?>assets/images/addCart.png" alt="Ajouter au panier" class="w-6 h-6">
                        </button>
                    </form>
                </div>
            </div>
        <?php 
            }
            echo '</div>';
        } else {
            echo "<p class='mt-4'>Aucun produit trouvé pour cette recherche.</p>";
        }
        ?>
    </section>
</div>

<?php require_once '../includes/_footer.php'; ?>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/scripts.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>

</body>
</html>
