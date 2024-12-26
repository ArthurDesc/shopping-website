<?php
session_start();

// Définir le chemin de base
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

// Chemin absolu vers la racine du projet
$root_path = __DIR__ . '/..';

require_once __DIR__ . '/../functions/url.php';
require_once __DIR__ . '/../includes/_db.php';
require_once __DIR__ . '/../classe/Panier.php';
require_once __DIR__ . '/../classe/produit.php';
require_once __DIR__ . '/../classe/Filtre.php';
require_once __DIR__ . '/../classe/CategoryManager.php';

$categoryManager = new CategoryManager($conn);
$panier = new Panier();

// Définir le chemin de base pour les images des produits en utilisant url()
$image_base_path = url('assets/images/produits/');

// Récupérer toutes les catégories
$categories = $categoryManager->getAllCategories();

// Récupérer toutes les marques uniques
$query = "SELECT DISTINCT marque FROM produits WHERE marque IS NOT NULL AND marque != '' ORDER BY marque";
$result = mysqli_query($conn, $query);
$marques = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $marques[] = $row['marque'];
    }
}

// Remplacer la requête des collections par un tableau statique
$collections = ['Homme', 'Femme', 'Enfant'];

// Construction de la requête avec les filtres
$where_clauses = ["p.stock > 0"];
$params = [];

// Filtre par collection
if (isset($_GET['collections'])) {
    $collection = $_GET['collections'];
    $where_clauses[] = "p.collection = ?";
    $params[] = $collection;
}

// Filtre par catégorie
if (isset($_GET['categories'])) {
    $category_id = $_GET['categories'];
    $where_clauses[] = "pc.id_categorie = ?";
    $params[] = $category_id;
}

// Construire la requête finale
$query = "SELECT p.*, GROUP_CONCAT(pc.id_categorie) as categories 
          FROM produits p 
          LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
          WHERE " . implode(" AND ", $where_clauses) . "
          GROUP BY p.id_produit";

// Préparer et exécuter la requête
$stmt = mysqli_prepare($conn, $query);
if ($params) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$produits = [];
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
        explode(',', $row['categories'] ?? ''),
        $row['collection']
    );
    $produits[] = $produit;
}

// Récupération des catégories et sous-catégories
$query_categories = "SELECT 
    c1.id_categorie, 
    c1.nom AS categorie_principale, 
    c2.id_categorie AS id_sous_categorie, 
    c2.nom AS sous_categorie,
    COALESCE(c1.id_categorie, '') as id_cat,
    COALESCE(c1.nom, '') as nom_cat,
    COALESCE(c2.id_categorie, '') as id_sous_cat,
    COALESCE(c2.nom, '') as nom_sous_cat
FROM categories c1
LEFT JOIN categories c2 ON c2.parent_id = c1.id_categorie
WHERE c1.parent_id IS NULL
ORDER BY c1.nom, c2.nom";

$result_categories = mysqli_query($conn, $query_categories);

$categories = array();
while ($row = mysqli_fetch_assoc($result_categories)) {
    $id_categorie = $row['id_cat'];
    if (!empty($id_categorie) && !isset($categories[$id_categorie])) {
        $categories[$id_categorie] = array(
            'id_categorie' => $id_categorie,
            'nom' => $row['nom_cat'],
            'sous_categories' => array()
        );
    }
    if (!empty($row['id_sous_cat'])) {
        $categories[$id_categorie]['sous_categories'][] = array(
            'id' => $row['id_sous_cat'],
            'nom' => $row['nom_sous_cat']
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

$produits_page = $produits;

// Gestion du pré-filtrage depuis l'URL
if (isset($_GET['filtre']) && isset($_GET['valeur'])) {
    $type_filtre = $_GET['filtre'];
    $valeur_filtre = $_GET['valeur'];
    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Trouver la checkbox correspondante
            let checkbox = document.querySelector(`input[data-{$type_filtre}=\"{$valeur_filtre}\"]`);
            if (checkbox) {
                checkbox.checked = true;
                // Déclencher l'événement change pour activer le filtre
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    </script>";
}

// Après la ligne où $produits est rempli
$categories_avec_produits = array();
$sous_categories_avec_produits = array();
$categories_forcees = array();

// Récupérer les catégories depuis l'URL
if (isset($_GET['categories'])) {
    $requested_categories = explode(',', $_GET['categories']);
    foreach ($requested_categories as $cat_id) {
        $categories_forcees[$cat_id] = true;
    }
}

// Parcourir tous les produits pour identifier les catégories utilisées
foreach ($produits as $produit) {
    $cats = $produit->getCategories();
    foreach ($cats as $cat_id) {
        foreach ($categories as $id_categorie => $categorie) {
            // Vérifier si c'est une sous-catégorie
            foreach ($categorie['sous_categories'] as $sous_cat) {
                if ($sous_cat['id'] == $cat_id) {
                    $sous_categories_avec_produits[$cat_id] = true;
                    $categories_avec_produits[$id_categorie] = true;
                }
            }
            // Vérifier si c'est une catégorie principale
            if ($cat_id == $id_categorie) {
                $categories_avec_produits[$cat_id] = true;
            }
        }
    }
}
?>

<?php require_once __DIR__ . '/../includes/_header.php'; ?>

<!-- Ajoutez cette div pour créer l'espace supplémentaire -->
<div class="mt-8"></div>

<main class="container mx-auto px-4 mt-2">
    <div class="flex flex-col md:flex-row relative">
        <!-- Filtres (optimisés pour la version mobile et desktop) -->
        <div id="filterForm" 
             x-data="{ openTab: null, isReady: false }" 
             x-init="setTimeout(() => { isReady = true }, 50)"
             :class="{ 'is-ready': isReady }"
             class="fixed md:block inset-0 bg-gradient-to-b from-blue-400 to-blue-600 z-[1000] md:static md:relative md:w-1/4 md:h-fit md:z-auto overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-full"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-full">
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
                    <!-- Catgories -->
                    <div id="categories-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" 
                             @click="openTab = openTab === 'categories' ? null : 'categories'">
                            <span class="font-semibold text-gray-600">Catégories</span>
                            <svg :class="{'rotate-180': openTab === 'categories'}" 
                                 class="w-6 h-6 transform transition-transform duration-200" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'categories'" 
                             x-collapse 
                             x-cloak 
                             style="display: none;">
                            <div class="py-2 pl-4">
                                <div class="search__container mb-2">
                                    <input class="search__input" type="text" id="categories-search" placeholder="Rechercher">
                                </div>
                                <div class="space-y-2">
                                    <?php foreach ($categories as $id_categorie => $categorie): 
                                        $has_active_subcategories = false;
                                        foreach ($categorie['sous_categories'] as $sous_cat) {
                                            if (isset($sous_categories_avec_produits[$sous_cat['id']]) || isset($categories_forcees[$sous_cat['id']])) {
                                                $has_active_subcategories = true;
                                                break;
                                            }
                                        }
                                        
                                        if (isset($categories_avec_produits[$id_categorie]) || 
                                            $has_active_subcategories || 
                                            isset($categories_forcees[$id_categorie])): 
                                    ?>
                                        <label class="checkbox-container flex items-center">
                                            <input type="checkbox" 
                                                   class="hidden"
                                                   data-category="<?= htmlspecialchars($categorie['id_categorie']) ?>"
                                                   data-name="<?= htmlspecialchars($categorie['nom']) ?>"
                                                   name="categories[]"
                                                   value="<?= htmlspecialchars($categorie['id_categorie']) ?>">
                                            <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                    class="checkbox-path">
                                                </path>
                                            </svg>
                                            <span class="ml-2 text-white select-none"><?= htmlspecialchars($categorie['nom']) ?></span>
                                        </label>
                                        
                                        <?php if (!empty($categorie['sous_categories'])): ?>
                                            <div class="ml-6 space-y-1">
                                                <?php foreach ($categorie['sous_categories'] as $sous_categorie): ?>
                                                    <?php if (isset($sous_categories_avec_produits[$sous_categorie['id']]) || 
                                                             isset($categories_forcees[$sous_categorie['id']])): ?>
                                                        <label class="checkbox-container flex items-center">
                                                            <input type="checkbox" 
                                                                   class="hidden"
                                                                   data-category="<?= htmlspecialchars($sous_categorie['id']) ?>"
                                                                   data-name="<?= htmlspecialchars($sous_categorie['nom']) ?>"
                                                                   name="categories[]"
                                                                   value="<?= htmlspecialchars($sous_categorie['id']) ?>">
                                                            <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                                <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                                    class="checkbox-path">
                                                                </path>
                                                            </svg>
                                                            <span class="ml-2 text-white select-none"><?= htmlspecialchars($sous_categorie['nom']) ?></span>
                                                        </label>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marques -->
                    <div id="marques-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" 
                             @click="openTab = openTab === 'marques' ? null : 'marques'">
                            <span class="font-semibold text-gray-600">Marques</span>
                            <svg :class="{'rotate-180': openTab === 'marques'}" 
                                 class="w-6 h-6 transform transition-transform duration-200" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'marques'" 
                             x-collapse 
                             x-cloak
                             style="display: none;">
                            <div class="py-2 pl-4">
                                <div class="search__container mb-2">
                                    <input class="search__input" type="text" id="marques-search" placeholder="Rechercher">
                                </div>
                                <div id="marques-list">
                                    <?php foreach ($marques as $marque): ?>
                                        <div class="flex items-center mb-2">
                                            <label class="checkbox-container flex items-center">
                                                <input type="checkbox"
                                                       class="hidden"
                                                       data-brand="<?= htmlspecialchars($marque) ?>"
                                                       name="marques[]"
                                                       value="<?= htmlspecialchars($marque) ?>">
                                                <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                    <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                        class="checkbox-path"></path>
                                                </svg>
                                                <span class="ml-2 text-white select-none"><?= htmlspecialchars($marque) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collections -->
                    <div id="collections-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" 
                             @click="openTab = openTab === 'collections' ? null : 'collections'">
                            <span class="font-semibold text-gray-600">Collections</span>
                            <svg :class="{'rotate-180': openTab === 'collections'}" 
                                 class="w-6 h-6 transform transition-transform duration-200" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'collections'" 
                             x-collapse 
                             x-cloak
                             style="display: none;">                            <div class="pl-4">
                                <div class="space-y-2">
                                    <?php foreach ($collections as $collection): ?>
                                        <div class="flex items-center">
                                            <label class="checkbox-container flex items-center">
                                                <input type="checkbox" 
                                                       class="hidden"
                                                       data-collection="<?= htmlspecialchars($collection) ?>"
                                                       name="collections[]"
                                                       value="<?= htmlspecialchars($collection) ?>">
                                                <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                    <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                        class="checkbox-path"></path>
                                                </svg>
                                                <span class="ml-2 text-white select-none"><?= htmlspecialchars($collection) ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bouton d'application des filtres (version mobile) -->
                <div class="filter-apply md:hidden">
                    <button type="button" id="applyFilters">
                        Appliquer
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des produits -->
        <div class="w-full md:w-3/4">
            <div class="flex flex-col mb-4 mt-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold mb-2">
                        <span id="filterTitle">Tous les articles</span>
                    </h2>

                    <!-- Bouton pour afficher les filtres en version mobile -->
                    <button id="toggleFilters" class="md:hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-300 ease-in-out">
                        Filtres
                    </button>
                </div>
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-4"></div>
            </div>

            <section id="products">
                <div class="wave-group mb-8">
                    <input required="" type="text" class="input search" id="search-products">
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
                        <span class="label-char" style="--index: 9">r</span>
                    </label>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>

                <!-- Container pour les produits -->
                <div class="list grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 transition-opacity duration-300"
                     x-data="{ loaded: true }"
                     :class="{ 'opacity-100': loaded }">
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card list-item bg-white rounded-lg shadow-md overflow-hidden flex flex-col h-full"
                             data-category="<?= htmlspecialchars(implode(',', $produit->getCategories())) ?>"
                             data-brand="<?= htmlspecialchars($produit->getMarque()) ?>"
                             data-collection="<?= htmlspecialchars($produit->getCollection()) ?>">
                            
                            <!-- Lien produit avec image -->
                            <a href="<?= url('pages/detail.php?id=' . $produit->getId()) ?>" class="product-link block flex-grow">
                                <div class="relative pb-[125%]">
                                    <img src="<?= url('assets/images/produits/' . ($produit->getImageUrl() ?? 'default_product.jpg')) ?>" 
                                         alt="<?= htmlspecialchars($produit->getNom()) ?>" 
                                         class="absolute inset-0 w-full h-full object-cover object-top">
                                    
                                    <!-- Bouton Wishlist -->
                                    <label class="wishlist-btn absolute top-2 right-2 z-10">
                                        <input type="checkbox" 
                                               class="wishlist-input"
                                               data-product-id="<?= $produit->getId() ?>"
                                        />
                                        <div class="wishlist-heart">
                                        <svg viewBox="0 0 256 256">
                                        <rect fill="none" height="256" width="256"></rect>
                                        <path d="M224.6,51.9a59.5,59.5,0,0,0-43-19.9,60.5,60.5,0,0,0-44,17.6L128,59.1l-7.5-7.4C97.2,28.3,59.2,26.3,35.9,47.4a59.9,59.9,0,0,0-2.3,87l83.1,83.1a15.9,15.9,0,0,0,22.6,0l81-81C243.7,113.2,245.6,75.2,224.6,51.9Z"
                                            stroke-width="20px" stroke="#000" fill="none"></path>
                                    </svg>
                                        </div>
                                    </label>
                                </div>
                                <div class="p-3">
                                    <h3 class="product-name text-sm font-semibold mb-1 truncate">
                                        <?= htmlspecialchars($produit->getNom()) ?>
                                    </h3>
                                    <p class="text-xs text-gray-600 mb-1">
                                        <?= htmlspecialchars($produit->getMarque()) ?>
                                    </p>
                                </div>
                            </a>
                            
                            <!-- Prix et bouton panier -->
                            <div class="product-price-cart-container px-3 pb-3 mt-auto flex justify-between items-center">
                                <p class="product-price text-sm text-blue-600 font-bold">
                                    <?= $produit->formatPrix() ?>
                                </p>
                                <button type="button" 
                                        class="product-cart-button open-modal-btn flex items-center justify-center" 
                                        data-product-id="<?= $produit->getId() ?>" 
                                        data-product-price="<?= $produit->getPrix() ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon">
                                            <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8ZM10.84 5.69C10.84 4.04 12.2 2.69 13.84 2.69C15.49 2.69 16.84 3.69 16.84 5.69V6.69H10.84V5.69ZM23.82 18.41L22.39 8.41C22.25 7.43 21.41 6.69 20.41 6.69H18.84V5.69C18.84 2.69 16.6 0.69 13.84 0.69C11.08 0.69 8.84 2.93 8.84 5.69V6.69H7.57C6.58 6.69 5.43 7.43 5.29 8.41L3.86 18.41C3.69 19.62 4.62 20.69 5.84 20.69H21.84C23.06 20.69 23.99 19.62 23.82 18.41Z" fill="currentColor"/>
                                        </svg>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message "Aucun article trouvé" -->
                <div id="no-results" class="hidden flex flex-col items-center justify-center py-12 mt-8">
                    <p class="text-lg text-gray-600 mb-4">Aucun article ne correspond à votre recherche</p>
                    <button id="reset-filters" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition-all duration-300 ease-in-out flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        Retirer les filtres
                    </button>
                </div>
            </section>

        </div>
    </div>

</main>





<!-- Modal avec animation -->
<div id="modal-container">
    <div class="modal-background bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
            <!-- Message d'erreur -->
            <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
            <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
                <!-- Les options seront ajoutées dynamiquement -->
            </select>
            <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                <button id="cancelBtn" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                    Annuler
                </button>
                <!-- Version mobile du bouton -->
                <button id="addToCartBtnMobile" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md sm:hidden">
                    <span>Ajouter au panier</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                        <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                    </svg>
                </button>
                <!-- Version desktop du bouton -->
                <div id="addToCartBtn" class="cart-add-button button-shadow hidden sm:block" data-tooltip="<?php echo number_format($produit->getPrix(), 2); ?>€">
                    <div class="cart-add-button-wrapper">
                        <div class="cart-add-button-text">Ajouter au panier</div>
                        <span class="cart-add-button-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast notification -->
<div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
    Article ajouté au panier
</div>

<!-- Toast pour les favoris -->
<div id="wishlistToast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50 hover:bg-green-600 cursor-pointer">
    <a href="<?= url('pages/wishlist.php') ?>" class="flex items-center text-white">
        <span class="toast-message">Produit ajouté aux favoris</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
        </svg>
    </a>
</div>

<?php require_once __DIR__ . '/../includes/_scripts.php'; ?>
<?php require_once __DIR__ . '/../includes/_footer.php'; ?>


</body>

</html>

