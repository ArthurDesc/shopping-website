<?php 
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";
require_once "../classe/Produit.php";
require_once "../classe/Filtre.php";
require_once "../functions/url.php";
require_once "../classe/CategoryManager.php";
$categoryManager = new CategoryManager($conn);

$panier = new Panier();

// Définir le chemin de base pour les images des produits
$image_base_path = '../assets/images/produits/';

// Récupérer toutes les catégories
$categories = $categoryManager->getAllCategories();

// Récupérer toutes les marques uniques
$marques_query = mysqli_query($conn, "SELECT DISTINCT marque FROM produits WHERE marque IS NOT NULL AND marque != ''");
$marques = mysqli_fetch_all($marques_query, MYSQLI_ASSOC);

// Récupérer toutes les collections uniques
$collections_query = mysqli_query($conn, "SELECT DISTINCT collection FROM produits WHERE collection IS NOT NULL AND collection != ''");
$collections = mysqli_fetch_all($collections_query, MYSQLI_ASSOC);

// Récupération des produits
$filtre = new Filtre();

// Appliquer les filtres en fonction des paramètres GET
if (isset($_GET['categories'])) {
    $filtre->setCategories($_GET['categories']);
}
if (isset($_GET['marques'])) {
    $filtre->setMarques($_GET['marques']);
}
if (isset($_GET['collections'])) {
    $filtre->setCollections($_GET['collections']);
}
if (isset($_GET['prix_min']) && isset($_GET['prix_max'])) {
    $filtre->setPrixRange($_GET['prix_min'], $_GET['prix_max']);
}

// Récupération des produits
$requete = $filtre->getRequeteSQL();
$stmt = mysqli_prepare($conn, $requete['sql']);

if (!empty($requete['params'])) {
    $types = str_repeat('s', count($requete['params']));
    mysqli_stmt_bind_param($stmt, $types, ...$requete['params']);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$produits = array();
while ($row = mysqli_fetch_assoc($result)) {
    $produit = new Produit(
        $row['id_produit'],
        $row['nom'],
        $row['prix'],
        $row['image_url'],
        $row['marque'],
        $row['description'],
        $row['stock'],
        $row['tailles_disponibles']
    );
    
    // Récupérer les catégories pour ce produit
    $productCategories = $categoryManager->getProductCategories($row['id_produit']);
    $categorieIds = array_column($productCategories, 'id_categorie');
    $produit->setCategories($categorieIds);
    
    $produits[] = $produit;
}

// Récupération des catégories et sous-catégories
$query_categories = "SELECT c1.id_categorie, c1.nom AS categorie_principale, c2.id_categorie AS id_sous_categorie, c2.nom AS sous_categorie
          FROM categories c1
          LEFT JOIN categories c2 ON c2.parent_id = c1.id_categorie
          WHERE c1.parent_id IS NULL
          ORDER BY c1.nom, c2.nom";

$result_categories = mysqli_query($conn, $query_categories);

$categories = array();
while ($row = mysqli_fetch_assoc($result_categories)) {
    if (!isset($categories[$row['id_categorie']])) {
        $categories[$row['id_categorie']] = array(
            'nom' => $row['categorie_principale'],
            'sous_categories' => array()
        );
    }
    if ($row['id_sous_categorie']) {
        $categories[$row['id_categorie']]['sous_categories'][] = array(
            'id' => $row['id_sous_categorie'],
            'nom' => $row['sous_categorie']
        );
    }
}

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
                                <?php foreach ($categories as $id_categorie => $category): ?>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               id="cat_<?php echo $id_categorie; ?>" 
                                               name="categories[]" 
                                               value="<?php echo $id_categorie; ?>" 
                                               class="mr-2"
                                               <?php echo ($filtre->hasCategory($id_categorie)) ? 'checked' : ''; ?>>
                                        <label for="cat_<?php echo $id_categorie; ?>"><?php echo htmlspecialchars($category['nom']); ?></label>
                                    </div>
                                    <?php if (!empty($category['sous_categories'])): ?>
                                        <?php foreach ($category['sous_categories'] as $sous_categorie): ?>
                                            <div class="flex items-center mb-2 ml-4">
                                                <input type="checkbox" 
                                                       id="cat_<?php echo $sous_categorie['id']; ?>" 
                                                       name="categories[]" 
                                                       value="<?php echo $sous_categorie['id']; ?>" 
                                                       class="mr-2"
                                                       <?php echo ($filtre->hasCategory($sous_categorie['id'])) ? 'checked' : ''; ?>>
                                                <label for="cat_<?php echo $sous_categorie['id']; ?>"><?php echo htmlspecialchars($sous_categorie['nom']); ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
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
                                               <?php echo ($filtre->hasMarque($marque['marque'])) ? 'checked' : ''; ?>>
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
                                               <?php echo ($filtre->hasCollection($collection['collection'])) ? 'checked' : ''; ?>>
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
    if (!empty($produits)) {
        echo '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6 mt-6">';
        foreach ($produits as $produit) {
            $image_url = $image_base_path . ($produit->getImageUrl() ?? 'default_product.jpg');
            
            if (!file_exists($image_url) || empty($produit->getImageUrl())) {
                $image_url = $image_base_path . 'default_product.jpg';
            }
    ?>
        <div class="bg-white rounded-lg shadow-md p-4" 
             data-categories="<?php echo htmlspecialchars(implode(',', $produit->getCategories())); ?>"
             data-collection="<?php echo htmlspecialchars($produit->getCollection()); ?>"
             data-brand="<?php echo htmlspecialchars($produit->getMarque()); ?>">
            <a href="<?php echo url('pages/detail.php?id=' . $produit->getId()); ?>" class="block">
                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($produit->getNom()); ?>" class="w-full h-48 object-cover mb-4">
                <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($produit->getNom()); ?></h3>
                <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($produit->getMarque()); ?></p>
                <p class="text-blue-600 font-bold"><?php echo $produit->formatPrix(); ?></p>
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


<script src="<?php echo url('assets/js/cart.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/scripts.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/navbar.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/filtre.js'); ?>" defer></script>
<script src="<?php echo url('assets/js/filterToggle.js'); ?>" defer></script>

<!-- Modal pour choisir la taille -->
<div id="modal-container" class="fixed inset-0 z-50 overflow-auto bg-smoke-light flex" style="display: none;">
  <div class="modal-background relative p-8 bg-white w-full max-w-md m-auto flex-col flex rounded-lg">
    <div class="modal">
      <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
      <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
        <!-- Les options seront ajoutées dynamiquement -->
      </select>
      <button id="addToCartBtn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
        Ajouter au panier
      </button>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    let currentProductId = null;

    // Ouvrir le modal
    $('.open-modal-btn').click(function(){
        currentProductId = $(this).data('product-id');
        // Charger les tailles disponibles pour ce produit
        $('#productSize').html(`
            <option value="">Choisissez une taille</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
        `);
        $('#modal-container').show().removeClass('out').addClass('five');
        $('body').addClass('modal-active');
    });

    // Fermer le modal
    $('#modal-container').click(function(e){
        if ($(e.target).is('#modal-container')) {
            closeModal();
        }
    });

    function closeModal() {
        $('#modal-container').removeClass('five').addClass('out');
        $('body').removeClass('modal-active');
        setTimeout(function() {
            $('#modal-container').removeClass('out').hide();
        }, 500); // Correspond à la durée de l'animation
    }

    // Ajouter au panier
    $('#addToCartBtn').click(function() {
        const selectedSize = $('#productSize').val();
        if (!selectedSize) {
            alert('Veuillez choisir une taille');
            return;
        }

        // Envoyer la requête AJAX pour ajouter au panier
        $.ajax({
            url: '<?php echo BASE_URL; ?>ajax/add_to_cart.php',
            method: 'POST',
            data: {
                id_produit: currentProductId,
                taille: selectedSize,
                quantite: 1
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    closeModal();
                    // Mettre à jour le compteur du panier si nécessaire
                    updateCartCount(data.cartCount);
                } else {
                    alert('Erreur : ' + data.message);
                }
            },
            error: function() {
                alert('Une erreur s\'est produite lors de l\'ajout au panier.');
            }
        });
    });

    function updateCartCount(count) {
        const cartCountElement = $('#cart-count');
        if (cartCountElement.length) {
            cartCountElement.text(count);
        }
    }
});
</script>
</body>
</html>

