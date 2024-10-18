<?php 
session_start();
$collection_filter = isset($_GET['collection']) ? $_GET['collection'] : null;
$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$marque_filter = isset($_GET['marque']) ? $_GET['marque'] : null;

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

// Au début du fichier produit.php, après avoir démarré la session

// Récupérer les paramètres de l'URL
$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$collection_filter = isset($_GET['collection']) ? $_GET['collection'] : null;

?>

<?php require_once '../includes/_header.php'; ?>

<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-4 mt-4">
        <h2 class="text-xl font-semibold">Voir tous</h2>
        <!-- Bouton pour afficher les filtres en version mobile -->
        <button id="toggleFilters" class="md:hidden bg-blue-500 text-white px-4 py-2 text-sm rounded">
            Filtres
        </button>
    </div>

    <div class="flex flex-col md:flex-row relative">
        <!-- Filtres (optimisés pour la version mobile et desktop) -->
        <div id="filterForm" class="fixed inset-0 bg-white z-[1000] transform translate-y-full md:translate-y-0 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:w-1/4 md:bg-transparent md:z-auto overflow-y-auto">
    <div class="h-full p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold text-lg">Filtres</h3>
            <button id="closeFilters" class="md:hidden text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>  
                
                <!-- Contenu des filtres -->
                <div class="flex-grow overflow-y-auto px-4">
                    <!-- Catégories -->
                    <details class="mb-4" open>
                        <summary class="font-semibold mb-2 cursor-pointer">Catégories</summary>
                        <div class="pl-4">
                            <?php foreach ($categories as $category): ?>
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" 
                                           id="cat_<?php echo $category['id_categorie']; ?>" 
                                           name="categories[]" 
                                           value="<?php echo $category['id_categorie']; ?>" 
                                           class="mr-2"
                                           <?php echo ($categorie_filter && $category['id_categorie'] == $categorie_filter) ? 'checked' : ''; ?>>
                                    <label for="cat_<?php echo $category['id_categorie']; ?>"><?php echo htmlspecialchars($category['nom']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <!-- Marques -->
                    <details class="mb-4" open>
                        <summary class="font-semibold mb-2 cursor-pointer">Marques</summary>
                        <div class="pl-4">
                            <?php foreach ($marques as $marque): ?>
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" 
                                           id="marque_<?php echo htmlspecialchars($marque['marque']); ?>" 
                                           name="marques[]" 
                                           value="<?php echo htmlspecialchars($marque['marque']); ?>" 
                                           class="mr-2"
                                           <?php echo ($marque_filter && strtolower($marque['marque']) == strtolower($marque_filter)) ? 'checked' : ''; ?>>
                                    <label for="marque_<?php echo htmlspecialchars($marque['marque']); ?>"><?php echo htmlspecialchars($marque['marque']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>

                    <!-- Collections -->
                    <details class="mb-4" open>
                        <summary class="font-semibold mb-2 cursor-pointer">Collections</summary>
                        <div class="pl-4">
                            <?php foreach ($collections as $collection): ?>
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" 
                                           id="collection_<?php echo htmlspecialchars($collection['collection']); ?>" 
                                           name="collections[]" 
                                           value="<?php echo htmlspecialchars($collection['collection']); ?>" 
                                           class="mr-2"
                                           <?php echo ($collection_filter && strtolower($collection['collection']) == strtolower($collection_filter)) ? 'checked' : ''; ?>>
                                    <label for="collection_<?php echo htmlspecialchars($collection['collection']); ?>"><?php echo htmlspecialchars($collection['collection']); ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>
                </div>
                
                <!-- Bouton Valider pour mobile et desktop -->
                <div class="mt-4">
                    <button id="applyFilters" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300 ease-in-out">
                        Valider les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des produits à droite -->
        <div class="w-full md:w-3/4">
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

<!-- Ajout d'Alpine.js -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<!-- Ajoutez ce script juste avant la fermeture de la balise body -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const toggleFiltersButton = document.getElementById('toggleFilters');
    const closeFiltersButton = document.getElementById('closeFilters');
    const applyFiltersButton = document.getElementById('applyFilters');

    function openFilters() {
        filterForm.classList.remove('translate-y-full');
        document.body.style.overflow = 'hidden';
    }

    function closeFilters() {
        filterForm.classList.add('translate-y-full');
        document.body.style.overflow = '';
    }

    toggleFiltersButton.addEventListener('click', openFilters);
    closeFiltersButton.addEventListener('click', closeFilters);

    applyFiltersButton.addEventListener('click', function() {
        if (window.innerWidth < 768) { // Si on est en version mobile
            closeFilters();
        }
        // Appliquer les filtres
        if (typeof applyFilters === 'function') {
            applyFilters();
        }
    });

    // Appliquer les filtres au chargement de la page
    if (typeof applyFilters === 'function') {
        applyFilters();
    }
});
</script>
<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/filtre.js" defer></script>
</body>
</html>
