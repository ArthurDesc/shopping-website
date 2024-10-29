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

// Récupération des filtres depuis l'URL
$filtre = new Filtre();

// Récupérer le paramètre de catégorie
if (isset($_GET['categories'])) {
    $categories = explode(',', $_GET['categories']);
    $filtre->setCategories($categories);
}

// Récupérer le paramètre de collection
if (isset($_GET['collection'])) {
    $collection = strtolower($_GET['collection']);
    $filtre->setCollections([$collection]);
}

// Récupérer le paramètre de marque
if (isset($_GET['marques'])) {
    $marques_filter = is_array($_GET['marques']) ? $_GET['marques'] : [$_GET['marques']];
    $filtre->setMarques($marques_filter);
}

// Obtenir la requête SQL et les paramètres
$requete = $filtre->getRequeteSQL();

// Préparer la requête
$stmt = mysqli_prepare($conn, $requete['sql']);

// Vérifier si la requête a des paramètres avant de les lier
if (!empty($requete['params'])) {
    // Créer le string de types
    $types = str_repeat('s', count($requete['params']));
    
    // Créer un tableau de références
    $bindParams = array();
    $bindParams[] = &$types; // Le type doit aussi être passé par référence
    
    // Ajouter chaque paramètre par référence
    foreach ($requete['params'] as &$param) {
        $bindParams[] = &$param;
    }
    
    // Lier les paramètres
    call_user_func_array(array($stmt, 'bind_param'), $bindParams);
}

// Exécuter la requête
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Traiter les résultats
$produits = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Créer des objets Produit uniquement si le stock est supérieur à 0
    if ($row['stock'] > 0) {
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
                    <!-- Déplacez la barre de recherche ici, en bas de tous les articles -->


                    <!-- Catégories -->
                    <div id="categories-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" @click="openTab = openTab === 'categories' ? null : 'categories'"> <span class="font-semibold text-gray-600">Catégories</span>
                            <svg :class="{'rotate-180': openTab === 'categories'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'categories'" x-collapse>
                            <div class="py-2 pl-4">
                                <!-- Pour la recherche de catégories -->
                                <div class="search__container mb-2">
                                    <input class="search__input" type="text" id="categories-search" placeholder="Rechercher">
                                </div>
                                <div id="categories-list">
                                    <?php foreach ($categories as $id => $category): ?>
                                        <div class="mb-2">
                                            <div class="flex items-center">
                                                <label class="checkbox-container flex items-center">
                                                    <input type="checkbox"
                                                        name="categories[]"
                                                        value="<?= htmlspecialchars($id) ?>"
                                                        <?= in_array($id, $filtre->getCategories()) ? 'checked' : '' ?>>
                                                    <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                        <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                            pathLength="575.0541381835938"
                                                            class="checkbox-path">
                                                        </path>
                                                    </svg>
                                                    <span class="ml-2 text-white select-none"><?= htmlspecialchars($category['nom']) ?></span>
                                                </label>
                                            </div>

                                            <!-- Pour les sous-catégories -->
                                            <?php if (!empty($category['sous_categories'])): ?>
                                                <div class="ml-8 mt-2">
                                                    <?php foreach ($category['sous_categories'] as $sous_cat): ?>
                                                        <div class="flex items-center mb-2">
                                                            <label class="checkbox-container flex items-center">
                                                                <input type="checkbox"
                                                                    name="categories[]"
                                                                    value="<?= htmlspecialchars($sous_cat['id']) ?>"
                                                                    <?= in_array($sous_cat['id'], $filtre->getCategories()) ? 'checked' : '' ?>>
                                                                <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                                    <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                                        pathLength="575.0541381835938"
                                                                        class="checkbox-path">
                                                                    </path>
                                                                </svg>
                                                                <span class="ml-2 text-white select-none"><?= htmlspecialchars($sous_cat['nom']) ?></span>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marques -->
                    <div id="marques-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" @click="openTab = openTab === 'marques' ? null : 'marques'"> 
                            <span class="font-semibold text-gray-600">Marques</span>
                            <svg :class="{'rotate-180': openTab === 'marques'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'marques'" x-collapse>
                            <div class="py-2 pl-4">
                                <!-- Pour la recherche de marques -->
                                <div class="search__container mb-2">
                                    <input class="search__input" type="text" id="marques-search" placeholder="Rechercher">
                                </div>
                                <div id="marques-list">
                                    <?php foreach ($marques as $marque): ?>
                                        <div class="flex items-center mb-2">
                                            <label class="checkbox-container flex items-center">
                                                <input type="checkbox" name="marques[]" value="<?php echo htmlspecialchars($marque); ?>">
                                                <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                    <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                        pathLength="575.0541381835938"
                                                        class="checkbox-path">
                                                    </path>
                                                </svg>
                                                <span class="ml-2 text-white"><?php echo htmlspecialchars($marque); ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collections -->
                    <div id="collections-filter" class="filter-section mb-4">
                        <div class="flex items-center justify-between cursor-pointer py-2" @click="openTab = openTab === 'collections' ? null : 'collections'">
                            <span class="font-semibold text-gray-600">Collections</span>
                            <svg :class="{'rotate-180': openTab === 'collections'}" class="w-6 h-6 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div x-show="openTab === 'collections'" x-collapse>
                            <div class="py-2 pl-4">
                                <?php
                                $staticCollections = ['Homme', 'Femme', 'Enfant'];
                                foreach ($staticCollections as $collection):
                                    $isChecked = isset($_GET['collection']) && 
                                                 strtolower($_GET['collection']) === strtolower($collection);
                                ?>
                                    <div class="flex items-center mb-2">
                                        <label class="checkbox-container flex items-center">
                                            <input type="checkbox" 
                                                   name="collections[]" 
                                                   value="<?php echo htmlspecialchars($collection); ?>"
                                                   <?php echo $isChecked ? 'checked' : ''; ?>>
                                            <svg viewBox="0 0 64 64" height="2em" width="2em">
                                                <path d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                                    pathLength="575.0541381835938"
                                                    class="checkbox-path">
                                                </path>
                                            </svg>
                                            <span class="ml-2 text-white"><?php echo htmlspecialchars($collection); ?></span>
                                        </label>
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
                    <button id="toggleFilters" class="md:hidden bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition-all duration-300 ease-in-out">
                        Filtres
                    </button>
                </div>
                <div id="activeFilters" class="flex flex-wrap gap-2 mb-2">
                    <!-- Les étiquettes seront ajoutées ici dynamiquement -->
                </div>
            </div>


            <div class="wave-group mb-8">
                <input required="" type="text" class="input" id="products-search">
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

            <section class="products_list">
                <?php if (!empty($produits)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <?php foreach ($produits as $produit): ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden product-card flex flex-col h-full">
                                <!-- Lien produit -->
                                <a href="<?php echo url('pages/detail.php?id=' . $produit->getId()); ?>" class="product-link block flex-grow">
                                    <div class="relative pb-[125%]">
                                        <img src="<?php echo $image_base_path . ($produit->getImageUrl() ?? 'default_product.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($produit->getNom()); ?>" 
                                             class="absolute inset-0 w-full h-full object-cover object-top">
                                    </div>
                                    <div class="p-3">
                                        <h3 class="text-sm font-semibold mb-1 truncate"><?php echo htmlspecialchars($produit->getNom()); ?></h3>
                                        <p class="text-xs text-gray-600 mb-1"><?php echo htmlspecialchars($produit->getMarque()); ?></p>
                                    </div>
                                </a>
                                
                                <!-- Conteneur séparé pour le prix et le bouton panier -->
                                <div class="product-price-cart-container px-3 pb-3 mt-auto flex justify-between items-center">
                                    <p class="product-price text-sm text-blue-600 font-bold"><?php echo $produit->formatPrix(); ?></p>
                                    <button type="button" 
                                            class="product-cart-button open-modal-btn flex items-center justify-center" 
                                            data-product-id="<?php echo $produit->getId(); ?>" 
                                            data-product-price="<?php echo $produit->getPrix(); ?>">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon">
                                            <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8ZM10.84 5.69C10.84 4.04 12.2 2.69 13.84 2.69C15.49 2.69 16.84 3.69 16.84 5.69V6.69H10.84V5.69ZM23.82 18.41L22.39 8.41C22.25 7.43 21.41 6.69 20.41 6.69H18.84V5.69C18.84 2.69 16.6 0.69 13.84 0.69C11.08 0.69 8.84 2.93 8.84 5.69V6.69H7.57C6.58 6.69 5.43 7.43 5.29 8.41L3.86 18.41C3.69 19.62 4.62 20.69 5.84 20.69H21.84C23.06 20.69 23.99 19.62 23.82 18.41Z" fill="currentColor"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Aucun produit disponible.</p>
                <?php endif; ?>
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



