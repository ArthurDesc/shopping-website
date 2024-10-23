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
        $row['tailles_disponibles'],
        [],  // catégories, à remplir plus tard
        $row['collection']  // ajoutez cette ligne
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

// Au dbut du fichier produit.php, après avoir démarré la session

// Récupérer les paramètres de l'URL
$categorie_filter = isset($_GET['categorie']) ? $_GET['categorie'] : null;
$collection_filter = isset($_GET['collection']) ? $_GET['collection'] : null;
$marque_filter = isset($_GET['marque']) ? $_GET['marque'] : null; // Ajoutez cette ligne

$query_categories_actives = "
    SELECT DISTINCT c.id_categorie, c.nom
    FROM categories c
    INNER JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie
    INNER JOIN produits p ON pc.id_produit = p.id_produit
    ORDER BY c.nom
";

$result_categories_actives = mysqli_query($conn, $query_categories_actives);

$categories_actives = array();
while ($row = mysqli_fetch_assoc($result_categories_actives)) {
    $categories_actives[] = $row;
}
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
                    <div id="categories-filter" class="filter-section">
                        <div class="flex items-center justify-between cursor-pointer py-4" id="categories-toggle">
                            <span class="font-semibold text-gray-600">Catégories</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div id="categories-content" class="py-2 pl-4" style="display: none;">
                            <div class="search__container mb-6"> <!-- Augmentez cette valeur pour plus d'espace -->
                                <input class="search__input" type="text" id="categories-search" placeholder="Filtrer">
                            </div>
                            <div id="categories-list">
                                <?php foreach ($categories as $id => $category): ?>
                                    <div class="mb-2">
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   id="cat_<?= htmlspecialchars($id) ?>" 
                                                   name="categories[]" 
                                                   value="<?= htmlspecialchars($id) ?>" 
                                                   class="mr-2"
                                                   <?= in_array($id, $filtre->getCategories()) ? 'checked' : '' ?>>
                                            <label for="cat_<?= htmlspecialchars($id) ?>" class="font-semibold"><?= htmlspecialchars($category['nom']) ?></label>
                                        </div>
                                        <?php if (!empty($category['sous_categories'])): ?>
                                            <div class="ml-4 mt-1">
                                                <?php foreach ($category['sous_categories'] as $sous_cat): ?>
                                                    <div class="flex items-center mb-1">
                                                        <input type="checkbox" 
                                                               id="cat_<?= htmlspecialchars($sous_cat['id']) ?>" 
                                                               name="categories[]" 
                                                               value="<?= htmlspecialchars($sous_cat['id']) ?>" 
                                                               class="mr-2"
                                                               <?= in_array($sous_cat['id'], $filtre->getCategories()) ? 'checked' : '' ?>>
                                                        <label for="cat_<?= htmlspecialchars($sous_cat['id']) ?>"><?= htmlspecialchars($sous_cat['nom']) ?></label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Marques -->
                    <div id="marques-filter" class="filter-section">
                        <div class="flex items-center justify-between cursor-pointer py-4" id="marques-toggle">
                            <span class="font-semibold text-gray-600">Marques</span>
                            <svg class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div id="marques-content" class="py-2 pl-4" style="display: none;">
                            <div class="search__container mb-6">
                                <input class="search__input" type="text" id="marques-search" placeholder="Filtrer">
                            </div>
                            <div id="marques-list">
                                <?php foreach ($marques as $marque): ?>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               id="marque_<?= htmlspecialchars($marque['marque']) ?>" 
                                               name="marques[]" 
                                               value="<?= htmlspecialchars($marque['marque']) ?>" 
                                               class="mr-2"
                                               <?= in_array($marque['marque'], $filtre->getMarques()) ? 'checked' : '' ?>>
                                        <label for="marque_<?= htmlspecialchars($marque['marque']) ?>"><?= htmlspecialchars($marque['marque']) ?></label>
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
                                <?php
                                $staticCollections = ['Homme', 'Femme', 'Enfant'];
                                foreach ($staticCollections as $collection): 
                                ?>
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" 
                                               id="collection_<?php echo htmlspecialchars($collection); ?>" 
                                               name="collections[]" 
                                               value="<?php echo htmlspecialchars($collection); ?>" 
                                               class="mr-2"
                                               <?php echo ($filtre->hasCollection($collection)) ? 'checked' : ''; ?>>
                                        <label for="collection_<?php echo htmlspecialchars($collection); ?>"><?php echo htmlspecialchars($collection); ?></label>
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
        echo '<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mt-6">';
        foreach ($produits as $produit) {
            $image_url = $image_base_path . ($produit->getImageUrl() ?? 'default_product.jpg');
            
            if (!file_exists($image_url) || empty($produit->getImageUrl())) {
                $image_url = $image_base_path . 'default_product.jpg';
            }
    ?>
        <div class="bg-white rounded-lg shadow-md overflow-hidden product-card flex flex-col h-full" 
             data-categories="<?php echo htmlspecialchars(implode(',', $produit->getCategories())); ?>"
             data-collection="<?php echo htmlspecialchars($produit->getCollection()); ?>"
             data-brand="<?php echo htmlspecialchars($produit->getMarque()); ?>">
            <a href="<?php echo url('pages/detail.php?id=' . $produit->getId()); ?>" class="block flex-grow flex flex-col">
                <div class="relative pb-[125%] flex-grow">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($produit->getNom()); ?>" class="absolute inset-0 w-full h-full object-cover object-top">
                </div>
                <div class="p-3 flex-shrink-0">
                    <h3 class="text-sm font-semibold mb-1 truncate"><?php echo htmlspecialchars($produit->getNom()); ?></h3>
                    <p class="text-xs text-gray-600 mb-1"><?php echo htmlspecialchars($produit->getMarque()); ?></p>
                </div>
            </a>
            <div class="px-3 pb-3 mt-auto flex justify-between items-center">
                <p class="text-sm text-blue-600 font-bold"><?php echo $produit->formatPrix(); ?></p>
                <form method="post" action="" class="add-to-cart-form">
                    <input type="hidden" name="id_produit" value="<?php echo $produit->getId(); ?>">
                    <button type="button" class="open-modal-btn" data-product-id="<?php echo $produit->getId(); ?>" data-product-price="<?php echo $produit->getPrix(); ?>">
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
<div id="modal-container" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0">
  <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
    <div class="p-6">
      <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
      <!-- Ajoutez cette ligne pour le message d'erreur -->
      <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
      <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
        <!-- Les options seront ajoutées dynamiquement -->
      </select>
      <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
        <button id="cancelBtn" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
          Annuler
        </button>
        <div class="cart-add-button button-shadow" id="addToCartBtn" data-tooltip="">
          <div class="cart-add-button-wrapper">
            <div class="cart-add-button-text">Ajouter au panier</div>
            <span class="cart-add-button-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                class="bi bi-cart2"
                viewBox="0 0 16 16"
              >
                <path
                  d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"
                ></path>
              </svg>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    let currentProductId = null;
    let currentProductPrice = null;

    // Ouvrir le modal
    $('.open-modal-btn').click(function(){
        currentProductId = $(this).data('product-id');
        currentProductPrice = $(this).data('product-price');
        
        // Charger les tailles disponibles pour ce produit
        $('#productSize').html(`
            <option value="">Choisissez une taille</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>
        `);
        
        // Mettre à jour le prix dans le tooltip
        $('#addToCartBtn').attr('data-tooltip', `${currentProductPrice} €`);
        
        $('#modal-container').addClass('active');
    });

    // Fermer le modal
    $('#modal-container').click(function(e){
        if ($(e.target).is('#modal-container')) {
            closeModal();
        }
    });

    // Ajouter cette fonction pour le bouton Annuler
    $('#cancelBtn').click(function() {
        closeModal();
    });

    function closeModal() {
        $('#modal-container').removeClass('active');
        // Réinitialiser la sélection de taille et le message d'erreur
        $('#productSize').val('');
        $('#sizeError').addClass('hidden');
    }

    // Ajouter au panier
    $('#addToCartBtn').click(function() {
        const selectedSize = $('#productSize').val();
        if (!selectedSize) {
            // Afficher le message d'erreur dans le modal
            $('#sizeError').text('Veuillez choisir une taille').removeClass('hidden');
            return;
        }

        // Cacher le message d'erreur si une taille est sélectionnée
        $('#sizeError').addClass('hidden');

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

    // Réinitialiser le message d'erreur lorsqu'une taille est sélectionnée
    $('#productSize').change(function() {
        $('#sizeError').addClass('hidden');
    });

    function updateCartCount(count) {
        const cartCountElement = $('#cart-count');
        if (cartCountElement.length) {
            cartCountElement.text(count);
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoriesFilter = document.getElementById('categories-filter');
    const toggleButton = document.getElementById('categories-toggle');
    const filterContent = document.getElementById('categories-content');
    const searchInput = document.getElementById('categories-search');
    const categoriesList = document.getElementById('categories-list');

    // Toggle du dropdown
    toggleButton.addEventListener('click', function() {
        if (filterContent.style.display === 'none' || filterContent.style.display === '') {
            filterContent.style.display = 'block';
            toggleButton.querySelector('svg').classList.add('rotate-180');
        } else {
            filterContent.style.display = 'none';
            toggleButton.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Fonction de recherche
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const categories = categoriesList.querySelectorAll('.mb-2');

        categories.forEach(category => {
            const categoryName = category.querySelector('label').textContent.toLowerCase();
            const subCategories = category.querySelectorAll('.ml-4 .flex');
            let shouldShow = categoryName.includes(searchTerm);

            subCategories.forEach(subCategory => {
                const subCategoryName = subCategory.querySelector('label').textContent.toLowerCase();
                if (subCategoryName.includes(searchTerm)) {
                    shouldShow = true;
                    subCategory.style.display = 'flex';
                } else {
                    subCategory.style.display = 'none';
                }
            });

            category.style.display = shouldShow ? 'block' : 'none';
        });
    });

    // Nouvelle partie pour les marques
    const marquesFilter = document.getElementById('marques-filter');
    const marquesToggle = document.getElementById('marques-toggle');
    const marquesContent = document.getElementById('marques-content');
    const marquesSearch = document.getElementById('marques-search');
    const marquesList = document.getElementById('marques-list');

    // Toggle du dropdown des marques
    marquesToggle.addEventListener('click', function() {
        if (marquesContent.style.display === 'none' || marquesContent.style.display === '') {
            marquesContent.style.display = 'block';
            marquesToggle.querySelector('svg').classList.add('rotate-180');
        } else {
            marquesContent.style.display = 'none';
            marquesToggle.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Fonction de recherche pour les marques
    marquesSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const marques = marquesList.querySelectorAll('.flex');

        marques.forEach(marque => {
            const marqueName = marque.querySelector('label').textContent.toLowerCase();
            if (marqueName.includes(searchTerm)) {
                marque.style.display = 'flex';
            } else {
                marque.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>
