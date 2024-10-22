<?php 
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";
require_once "../classe/Produit.php";
require_once "../functions/url.php";

$panier = new Panier();

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



if (isset($_POST['ajouter_au_panier']) && isset($_POST['id_produit'])) {
    $id_produit = $_POST['id_produit'];
    $panier->ajouter($id_produit);
    // Redirigez vers la même page pour éviter les soumissions multiples
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Au début du fichier produit.php, après avoir démarré la session

// Récupérer les paramètres de l'URL
$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$collection_filter = isset($_GET['collection']) ? $_GET['collection'] : null;
$marque_filter = isset($_GET['marque']) ? $_GET['marque'] : null; // Ajoutez cette ligne
?>
<style>
    .filter-dropdown {
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .filter-dropdown.show {
        opacity: 1;
        transform: translateY(0);
    }

    #filterDropdowns {
        overflow: hidden;
    }

    #filterDropdowns.show {
        display: block !important;
    }
</style>
<?php require_once '../includes/_header.php'; ?>

<!-- Ajoutez cette div pour créer l'espace supplémentaire -->
<div class="mt-16"></div> <!-- Vous pouvez ajuster la valeur (16) selon vos besoins -->

<main class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row relative">
        <!-- Filtres (optimisés pour la version mobile et desktop) -->
        <div id="filterForm" x-data="{ openTab: null }" class="fixed inset-0 bg-white z-[1000] transform translate-y-full md:translate-y-0 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:w-1/4 md:bg-transparent md:z-auto overflow-y-auto">
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
                    <div class="border-b">
                        <div @click="openTab = openTab === 'categories' ? null : 'categories'" class="flex items-center justify-between cursor-pointer py-4">
                            <span class="font-semibold text-gray-600">Catégories</span>
                            <svg :class="{'rotate-180': openTab === 'categories'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'categories'" x-collapse>
                            <div class="py-4 pl-4">
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
                        </div>
                    </div>

                    <!-- Marques -->
                    <div class="border-b">
                        <div @click="openTab = openTab === 'marques' ? null : 'marques'" class="flex items-center justify-between cursor-pointer py-4">
                            <span class="font-semibold text-gray-600">Marques</span>
                            <svg :class="{'rotate-180': openTab === 'marques'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'marques'" x-collapse>
                            <div class="py-4 pl-4">
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
                        </div>
                    </div>

                    <!-- Collections -->
                    <div class="border-b">
                        <div @click="openTab = openTab === 'collections' ? null : 'collections'" class="flex items-center justify-between cursor-pointer py-4">
                            <span class="font-semibold text-gray-600">Collections</span>
                            <svg :class="{'rotate-180': openTab === 'collections'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'collections'" x-collapse>
                            <div class="py-4 pl-4">
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
                        </div>
                    </div>
                </div>
                
                <!-- Bouton Valider pour mobile uniquement -->
                <div class="mt-4 md:hidden">
                    <button id="applyFilters" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300 ease-in-out">
                        Valider les filtres
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des produits à droite -->
        <div class="w-full md:w-3/4">
            <div class="flex flex-col mb-4 mt-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold mb-2">
                        <span id="filterTitle">Tous les articles</span>
                    </h2>
                    <!-- Bouton pour afficher les filtres en version mobile -->
                    <button id="toggleFilters" class="md:hidden bg-blue-500 text-white px-3 py-1 text-sm rounded">
                        Filtres
                    </button>
                </div>
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-2">
                    <!-- Les étiquettes seront ajoutées ici dynamiquement -->
                </div>
            </div>


<section class="products_list">
    <?php 
    $req = mysqli_query($conn, "SELECT p.*, GROUP_CONCAT(c.id_categorie) as categories 
                                FROM produits p 
                                LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                                LEFT JOIN categories c ON pc.id_categorie = c.id_categorie 
                                GROUP BY p.id_produit, p.nom, p.image_url, p.description, p.prix, p.stock, p.taille, p.marque, p.date_ajout, p.collection");
    
    if ($req->num_rows > 0) {
        echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6 mt-6">';
        while ($row = mysqli_fetch_assoc($req)) { 
            $produit = new Produit($row['id_produit'], $row['nom'], $row['prix'], $row['image_url'], $row['marque']);
            $image_url = $image_base_path . ($produit->getImageUrl() ?? 'default_product.jpg');
            
            if (!file_exists($image_url) || empty($produit->getImageUrl())) {
                $image_url = $image_base_path . 'default_product.jpg';
            }
    ?>
        <div class="bg-white rounded-lg shadow-md p-4" 
             data-categories="<?php echo htmlspecialchars($row['categories']); ?>"
             data-collection="<?php echo htmlspecialchars($row['collection'] ?? ''); ?>"
             data-brand="<?php echo htmlspecialchars($produit->getMarque()); ?>">
            <a href="<?php echo url('pages/detail.php?id=' . $produit->getId()); ?>" class="block">
                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($produit->getNom()); ?>" class="w-full h-48 object-cover mb-4">
                <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($produit->getNom()); ?></h3>
                <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($produit->getMarque()); ?></p>
                <p class="text-blue-600 font-bold"><?php echo number_format($produit->getPrix(), 2); ?> €</p>
            </a>
            <div class="mt-2 flex justify-between items-center">
                <a href="<?php echo url('pages/detail.php?id=' . $produit->getId()); ?>" class="text-blue-500 hover:underline">Voir détails</a>
                <form method="post" action="" class="add-to-cart-form">
                    <input type="hidden" name="id_produit" value="<?php echo $produit->getId(); ?>">
                    <button type="button" class="open-modal-btn" data-product-id="<?php echo $produit->getId(); ?>">
                        <img src="<?php echo url('assets/images/addCart.png'); ?>" alt="Ajouter au panier" class="w-6 h-6">
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
</main>

<?php include '../includes/_footer.php'; ?>

<!-- Scripts -->

<!-- Ajout d'Alpine.js -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

<!-- Ajoutez ce script juste avant la fermeture de la balise body -->

<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/filtre.js" defer></script>
<script src="<?php echo url('assets/js/cart.js'); ?>" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/filterToggle.js" defer></script>

<!-- Modal pour choisir la taille -->
<div id="sizeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Choisissez une taille</h3>
            <div class="mt-2 px-7 py-3">
                <select id="productSize" class="w-full px-3 py-2 border rounded-md">
                    <!-- Les options seront ajoutées dynamiquement -->
                </select>
            </div>
            <div class="items-center px-4 py-3">
                <button id="addToCartBtn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    Ajouter au panier
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('sizeModal');
    const sizeSelect = document.getElementById('productSize');
    const addToCartBtn = document.getElementById('addToCartBtn');
    let currentProductId = null;

    // Ouvrir le modal
    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentProductId = this.getAttribute('data-product-id');
            // Ici, vous devriez charger les tailles disponibles pour ce produit
            // Pour cet exemple, nous utiliserons des tailles statiques
            sizeSelect.innerHTML = `
                <option value="">Choisissez une taille</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            `;
            modal.classList.remove('hidden');
        });
    });

    // Fermer le modal en cliquant en dehors
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Ajouter au panier
    addToCartBtn.addEventListener('click', function() {
        const selectedSize = sizeSelect.value;
        if (!selectedSize) {
            alert('Veuillez choisir une taille');
            return;
        }

        // Envoyer la requête AJAX pour ajouter au panier
        fetch('<?php echo BASE_URL; ?>ajax/add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_produit=${currentProductId}&taille=${selectedSize}&quantite=1`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.classList.add('hidden');
                // Mettre à jour le compteur du panier si nécessaire
                updateCartCount(data.cartCount);
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur s\'est produite lors de l\'ajout au panier.');
        });
    });

    function updateCartCount(count) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }
});
</script>
</body>
</html>
