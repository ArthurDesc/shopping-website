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
$query = "SELECT DISTINCT marque FROM produits WHERE marque IS NOT NULL AND marque != '' ORDER BY marque";
$result = mysqli_query($conn, $query);
$marques = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $marques[] = $row['marque'];
    }
}

// Récupérer toutes les collections uniques
$collections_query = "SELECT DISTINCT collection FROM produits WHERE collection IS NOT NULL AND collection != '' ORDER BY collection";
$collections_result = mysqli_query($conn, $collections_query);
$collections = [];
if ($collections_result) {
    while ($row = mysqli_fetch_assoc($collections_result)) {
        $collections[] = $row['collection'];
    }
}

// Remplacer l'ancien code de récupération des produits par :
$query = "SELECT p.*, GROUP_CONCAT(pc.id_categorie) as categories 
          FROM produits p 
          LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
          WHERE p.stock > 0 
          GROUP BY p.id_produit";
$result = mysqli_query($conn, $query);

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

    .modal {
        visibility: hidden;
        opacity: 0;
    }

    .modal.scale-100 {
        visibility: visible;
        opacity: 1;
    }
</style>
<?php require_once '../includes/_header.php'; ?>

<!-- Ajoutez cette div pour créer l'espace supplémentaire -->
<div class="mt-8"></div> <!-- Vous pouvez ajuster la valeur (16) selon vos besoins -->

<main class="container mx-auto px-4 mt-2">
    <div class="flex flex-col md:flex-row relative">
        <!-- Filtres (optimisés pour la version mobile et desktop) -->
        <div id="filterForm" x-data="{ openTab: null }" class="fixed md:static inset-0 bg-gradient-to-b from-blue-400 to-blue-600 z-[1000] transform translate-y-full md:translate-y-0 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 md:w-1/4 md:h-fit md:z-auto overflow-y-auto">
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
                        <div x-show="openTab === 'categories'" x-collapse>
                            <div class="py-2 pl-4">
                                <div class="search__container mb-2">
                                    <input class="search__input" type="text" id="categories-search" placeholder="Rechercher">
                                </div>
                                <div class="space-y-2">
                                    <?php foreach ($categories as $id_categorie => $categorie): ?>
                                        <label class="checkbox-container flex items-center">
                                            <input type="checkbox" 
                                                   class="hidden"
                                                   data-category="<?= htmlspecialchars($categorie['id_categorie']) ?>"
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
                                                    <label class="checkbox-container flex items-center">
                                                        <input type="checkbox" 
                                                               class="hidden"
                                                               data-category="<?= htmlspecialchars($sous_categorie['id']) ?>"
                                                               name="categories[]"
                                                               value="<?= htmlspecialchars($sous_categorie['id']) ?>">
                                                        <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                            <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                                class="checkbox-path">
                                                            </path>
                                                        </svg>
                                                        <span class="ml-2 text-white select-none"><?= htmlspecialchars($sous_categorie['nom']) ?></span>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
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
                        <div x-show="openTab === 'marques'" x-collapse>
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
                        <div x-show="openTab === 'collections'" x-collapse>
                            <div class="py-2 pl-4">
                                <div class="space-y-2">
                                    <?php foreach ($collections as $collection): ?>
                                        <div class="flex items-center mb-2">
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


            <div class="wave-group mb-8">
                <input type="text" class="input search" id="search-products">
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

            <section id="products">
                <!-- Barre de recherche -->
                <div class="wave-group mb-8">
                    <input required="" type="text" class="search input" id="products-search">
                    <!-- ... reste du code de la barre de recherche ... -->
                </div>

                <!-- Container pour les produits -->
                <div class="list grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card list-item"
                             data-category="<?= htmlspecialchars(implode(',', $produit->getCategories())) ?>"
                             data-brand="<?= htmlspecialchars($produit->getMarque()) ?>"
                             data-collection="<?= htmlspecialchars($produit->getCollection()) ?>">
                            <!-- Contenu de la carte produit -->
                            <h3 class="product-name text-sm font-semibold mb-1 truncate">
                                <?= htmlspecialchars($produit->getNom()) ?>
                            </h3>
                            <p class="product-price text-sm text-blue-600 font-bold">
                                <?= $produit->formatPrix() ?>
                            </p>
                            <!-- ... reste du contenu de la carte ... -->
                        </div>
                    <?php endforeach; ?>
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
            <!-- Ajoutez cette ligne pour le message d'erreur -->
            <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
            <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
                <!-- Les options seront ajoutées dynamiquement -->
            </select>
            <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                <button id="cancelBtn" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                    Annuler
                </button>
                <div class="cart-add-button button-shadow" id="addToCartBtn" data-tooltip="<?php echo number_format($produit->getPrix(), 2); ?>€">
                    <div class="cart-add-button-wrapper">
                        <div class="cart-add-button-text">Ajouter au panier</div>
                        <span class="cart-add-button-icon">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="16"
                                height="16"
                                fill="currentColor"
                                class="bi bi-cart2"
                                viewBox="0 0 16 16">
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

<?php include '../includes/_footer.php'; ?>


</body>

</html>

