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

// Récupérer toutes les catégories
$categories_query = mysqli_query($conn, "SELECT * FROM categories");
$categories = mysqli_fetch_all($categories_query, MYSQLI_ASSOC);

// Récupérer toutes les marques uniques
$marques_query = mysqli_query($conn, "SELECT DISTINCT marque FROM produits WHERE marque IS NOT NULL AND marque != ''");
$marques = mysqli_fetch_all($marques_query, MYSQLI_ASSOC);

// Récupérer toutes les collections uniques
$collections_query = mysqli_query($conn, "SELECT DISTINCT collection FROM produits WHERE collection IS NOT NULL AND collection != ''");
$collections = mysqli_fetch_all($collections_query, MYSQLI_ASSOC);

// Ajoutez cette fonction pour gérer l'ajout au panier
function ajouterAuPanier($id_produit) {
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }
    if (isset($_SESSION['panier'][$id_produit])) {
        $_SESSION['panier'][$id_produit]++;
    } else {
        $_SESSION['panier'][$id_produit] = 1;
    }
}

// Vérifiez si un produit a été ajouté au panier
if (isset($_POST['ajouter_au_panier']) && isset($_POST['id_produit'])) {
    $id_produit = $_POST['id_produit'];
    ajouterAuPanier($id_produit);
    // Redirigez vers la même page pour éviter les soumissions multiples
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

?>

<?php require_once '../includes/_header.php'; ?>

<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row">
        <!-- Filtres à gauche -->
        <div id="filterForm" class="w-full md:w-1/4 bg-white p-4 rounded shadow mt-4 md:mt-0 md:mr-4">
            <h3 class="font-semibold mb-4">Filtres</h3>
            <div class="space-y-2">
                <!-- Catégories -->
                <div x-data="{ categoriesOpen: false }" class="filter-category">
                    <div @click="categoriesOpen = !categoriesOpen" class="flex items-center text-gray-600 w-full border-b overflow-hidden cursor-pointer">
                        <div class="w-10 border-r px-2 transform transition duration-300 ease-in-out" :class="{'rotate-90': categoriesOpen, '-translate-y-0.0': !categoriesOpen}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <div class="flex items-center px-2 py-3">
                            <div class="mx-3">
                                <button class="hover:underline">Catégories</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex p-5 w-full transform transition duration-300 ease-in-out border-b pb-10"
                         x-cloak x-show="categoriesOpen" x-collapse x-collapse.duration.500ms>
                        <ul class="w-full space-y-1 text-sm text-gray-700">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                                        <input id="category-<?php echo $category['id_categorie']; ?>" type="checkbox" name="category" value="<?php echo $category['id_categorie']; ?>" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="category-<?php echo $category['id_categorie']; ?>" class="w-full ms-2 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($category['nom']); ?></label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Marques -->
                <div x-data="{ marquesOpen: false }" class="filter-category">
                    <div @click="marquesOpen = !marquesOpen" class="flex items-center text-gray-600 w-full border-b overflow-hidden cursor-pointer">
                        <div class="w-10 border-r px-2 transform transition duration-300 ease-in-out" :class="{'rotate-90': marquesOpen, '-translate-y-0.0': !marquesOpen}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <div class="flex items-center px-2 py-3">
                            <div class="mx-3">
                                <button class="hover:underline">Marques</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex p-5 w-full transform transition duration-300 ease-in-out border-b pb-10"
                         x-cloak x-show="marquesOpen" x-collapse x-collapse.duration.500ms>
                        <ul class="w-full space-y-1 text-sm text-gray-700">
                            <?php foreach ($marques as $marque): ?>
                                <li>
                                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                                        <input id="marque-<?php echo htmlspecialchars($marque['marque']); ?>" type="checkbox" name="brand" value="<?php echo htmlspecialchars($marque['marque']); ?>" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="marque-<?php echo htmlspecialchars($marque['marque']); ?>" class="w-full ms-2 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($marque['marque']); ?></label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Collections -->
                <div x-data="{ collectionsOpen: false }" class="filter-category">
                    <div @click="collectionsOpen = !collectionsOpen" class="flex items-center text-gray-600 w-full border-b overflow-hidden cursor-pointer">
                        <div class="w-10 border-r px-2 transform transition duration-300 ease-in-out" :class="{'rotate-90': collectionsOpen, '-translate-y-0.0': !collectionsOpen}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                        <div class="flex items-center px-2 py-3">
                            <div class="mx-3">
                                <button class="hover:underline">Collections</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex p-5 w-full transform transition duration-300 ease-in-out border-b pb-10"
                         x-cloak x-show="collectionsOpen" x-collapse x-collapse.duration.500ms>
                        <ul class="w-full space-y-1 text-sm text-gray-700">
                            <?php foreach ($collections as $collection): ?>
                                <li>
                                    <div class="flex items-center p-2 rounded hover:bg-gray-100">
                                        <input id="collection-<?php echo htmlspecialchars($collection['collection']); ?>" type="checkbox" name="collection" value="<?php echo htmlspecialchars($collection['collection']); ?>" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="collection-<?php echo htmlspecialchars($collection['collection']); ?>" class="w-full ms-2 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($collection['collection']); ?></label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Ajoutez d'autres filtres ici si nécessaire -->

            </div>
        </div>

        <!-- Liste des produits à droite -->
        <div class="w-full md:w-3/4">
            <div class="mt-6 flex justify-between items-center">
                <h2 class="text-xl font-semibold">Voir tous</h2>
            </div>
            <section class="products_list">
                <?php 
                // Requête pour récupérer tous les produits avec leurs catégories
                $req = mysqli_query($conn, "SELECT p.*, GROUP_CONCAT(c.id_categorie) as categories 
                                            FROM produits p 
                                            LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                                            LEFT JOIN categories c ON pc.id_categorie = c.id_categorie 
                                            GROUP BY p.id_produit, p.nom, p.image_url, p.description, p.prix, p.stock, p.taille, p.marque, p.date_ajout, p.collection");
                
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
                    <div class="bg-white rounded-lg shadow-md p-4" 
                         data-categories="<?php echo htmlspecialchars($row['categories'] ?? ''); ?>"
                         data-collection="<?php echo htmlspecialchars($row['collection'] ?? ''); ?>"
                         data-brand="<?php echo htmlspecialchars($row['marque'] ?? ''); ?>">
                        <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $row['id_produit']; ?>" class="block">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>" class="w-full h-48 object-cover mb-4">
                            <?php echo "<!-- Debug: Image path: $image_url -->"; ?>
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
                    echo "<p>Aucun produit disponible.</p>";
                }
                ?>
            </section>
        </div>
    </div>
</div>
<?php include '../includes/_footer.php'; ?>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
<!-- Ajout du script de filtrage -->
<script src="<?php echo BASE_URL; ?>assets/js/filtre.js" defer></script>
<!-- Ajout d'Alpine.js -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</body>
</html>