<?php
include '../includes/session.php';
require_once '../includes/_header.php';
include '../includes/_db.php';
require_once '../classe/ArticleManager.php';
require_once '../classe/AdminManager.php'; // Assurez-vous que cette classe existe

// Créez une instance de AdminManager
$adminManager = new AdminManager($conn);

$mode = isset($_GET['mode']) && $_GET['mode'] === 'edit' ? 'edit' : 'view';
$isEditMode = $mode === 'edit';

// Vérifiez si l'utilisateur est admin pour le mode édition
if ($isEditMode) {
    $id_utilisateur = get_id_utilisateur();
    if (!$id_utilisateur || !$adminManager->isAdmin($id_utilisateur)) {
        header("Location: " . BASE_URL . "index.php");
        exit();
    }
}

// Vérifier si un ID de produit est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de produit non valide";
    exit();
}

$id_produit = $_GET['id'];

// Récupérer les détails du produit
$query = "SELECT * FROM produits WHERE id_produit = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_produit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Produit non trouvé";
    exit();
}

$produit = $result->fetch_assoc();

// Récupérer les catégories du produit
$query_categories = "SELECT c.* 
                     FROM categories c
                     JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie
                     WHERE pc.id_produit = ?";
$stmt_categories = $conn->prepare($query_categories);
$stmt_categories->bind_param("i", $id_produit);
$stmt_categories->execute();
$result_categories = $stmt_categories->get_result();

$categories = [];
while ($row = $result_categories->fetch_assoc()) {
    $categories[] = $row;
}

// Définir le chemin de l'image
$image_path = '../assets/images/produits/';
$image_url = $image_path . ($produit['image_url'] ?? 'default_product.jpg');

// Vérifier si l'image existe, sinon utiliser l'image par défaut
if (!file_exists($image_url) || empty($produit['image_url'])) {
    $image_url = $image_path . 'default_product.jpg';
}

$articleManager = new ArticleManager($conn);
$id_article = isset($_GET['id']) ? intval($_GET['id']) : 0;
$article = $articleManager->getArticle($id_article);

if (!$article) {
    // Rediriger si l'article n'existe pas
    header("Location: " . BASE_URL . "index.php");
    exit();
}

$categories = $articleManager->getArticleCategories($id_article);
$allCategories = $articleManager->getAllCategories();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - Détails</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</head>

<body class="bg-gray-100">

    <?php if ($isEditMode): ?>
        <div class="bg-red-500 text-white text-center py-2 font-bold">
            Mode Admin
        </div>
    <?php endif; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="bg-white p-4 flex flex-col h-screen">
            <!-- En-tête avec bouton retour et partage -->
            <div class="flex justify-between items-center mb-4">
                <a href="produit.php" class="text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <button class="text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684z" />
                    </svg>
                </button>
            </div>

            <!-- Image du produit -->
            <div class="flex-grow mb-4">
                <img src="<?php echo htmlspecialchars($image_url); ?>"
                    alt="<?php echo htmlspecialchars($produit['nom']); ?>"
                    class="w-full h-full object-cover rounded-lg">
            </div>

            <!-- Détails du produit -->
            <div class="space-y-2" x-data="{ 
                editingTitle: false, 
                title: '<?php echo addslashes(htmlspecialchars($produit['nom'])); ?>',
                editingDescription: false, 
                description: '<?php echo addslashes(htmlspecialchars($produit['description'])); ?>',
                editingPrice: false,
                price: '<?php echo number_format($produit['prix'], 2); ?>',
                editingBrand: false,
                brand: '<?php echo addslashes(htmlspecialchars($produit['marque'])); ?>',
                editingCollection: false,
                collection: '<?php echo addslashes(htmlspecialchars($produit['collection'])); ?>'
            }">
                <div class="flex items-center">
                    <h2 class="text-xl font-bold" x-show="!editingTitle" x-text="title"></h2>
                    <input x-show="editingTitle"
                        x-model="title"
                        @keydown.enter="editingTitle = false; updateTitle(title)"
                        @keydown.escape="editingTitle = false"
                        class="text-xl font-bold border-b-2 border-blue-500 focus:outline-none"
                        type="text">
                    <?php if ($isEditMode): ?>
                        <button @click="editingTitle = !editingTitle" class="ml-2 text-gray-600 hover:text-blue-500">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                        </button>
                    <?php endif; ?>
                </div>

                <div class="flex items-center">
                    <div class="flex text-yellow-400">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <span class="ml-1 text-gray-600">4.50 (85)</span>
                </div>
                <div x-data="{ editingDescription: false, description: '<?php echo addslashes(htmlspecialchars($produit['description'])); ?>' }">
                    <div class="flex items-start">
                        <?php if ($isEditMode): ?>
                        <button @click="editingDescription = !editingDescription" class="ml-2 text-gray-600 hover:text-blue-500 flex-shrink-0">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <?php endif; ?>
                        <p class="text-gray-600 text-sm flex-grow" x-show="!editingDescription" x-text="description"></p>
                        <textarea x-show="editingDescription"
                                  x-model="description"
                                  @keydown.enter="editingDescription = false; updateDescription(description)"
                                  @keydown.escape="editingDescription = false"
                                  class="w-full text-sm border-b-2 border-blue-500 focus:outline-none"
                                  rows="3"></textarea>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <span class="font-bold text-lg" x-show="!editingPrice" x-text="price + ' €'"></span>
                        <input x-show="editingPrice"
                               x-model="price"
                               @keydown.enter="editingPrice = false; updatePrice(price)"
                               @keydown.escape="editingPrice = false"
                               class="font-bold text-lg border-b-2 border-blue-500 focus:outline-none"
                               type="number"
                               step="0.01">
                        <?php if ($isEditMode): ?>
                        <button @click="editingPrice = !editingPrice" class="ml-2 text-gray-600 hover:text-blue-500">
                            <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <?php endif; ?>
                    </div>
                    <select class="border rounded px-2 py-1 text-sm">
                        <option>Taille</option>
                        <option><?php echo htmlspecialchars($produit['taille']); ?></option>
                    </select>
                </div>
                <select class="w-full border rounded px-2 py-1 text-sm">
                    <option>Quantité</option>
                    <?php for ($i = 1; $i <= min($produit['stock'], 10); $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600">Marque : </span>
                    <span x-show="!editingBrand" x-text="brand" class="text-sm text-gray-600 ml-1"></span>
                    <input x-show="editingBrand"
                           x-model="brand"
                           @keydown.enter="editingBrand = false; updateBrand(brand)"
                           @keydown.escape="editingBrand = false"
                           class="text-sm border-b-2 border-blue-500 focus:outline-none ml-1"
                           type="text">
                    <?php if ($isEditMode): ?>
                    <button @click="editingBrand = !editingBrand" class="ml-2 text-gray-600 hover:text-blue-500">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>
                <div class="flex items-center">
                    <span class="text-sm text-gray-600">Collection : </span>
                    <span x-show="!editingCollection" x-text="collection" class="text-sm text-gray-600 ml-1"></span>
                    <select x-show="editingCollection"
                            x-model="collection"
                            @change="editingCollection = false; updateCollection(collection)"
                            @keydown.escape="editingCollection = false"
                            class="text-sm border-b-2 border-blue-500 focus:outline-none ml-1">
                        <option value="Homme">Homme</option>
                        <option value="Femme">Femme</option>
                        <option value="Enfant">Enfant</option>
                    </select>
                    <?php if ($isEditMode): ?>
                    <button @click="editingCollection = !editingCollection" class="ml-2 text-gray-600 hover:text-blue-500">
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.70837 3.16668H3.16671C2.74678 3.16668 2.34405 3.3335 2.04712 3.63043C1.75019 3.92736 1.58337 4.33009 1.58337 4.75002V15.8333C1.58337 16.2533 1.75019 16.656 2.04712 16.9529C2.34405 17.2499 2.74678 17.4167 3.16671 17.4167H14.25C14.67 17.4167 15.0727 17.2499 15.3696 16.9529C15.6666 16.656 15.8334 16.2533 15.8334 15.8333V10.2917M14.6459 1.97918C14.9608 1.66424 15.388 1.4873 15.8334 1.4873C16.2788 1.4873 16.7059 1.66424 17.0209 1.97918C17.3358 2.29413 17.5128 2.72128 17.5128 3.16668C17.5128 3.61208 17.3358 4.03924 17.0209 4.35418L9.50004 11.875L6.33337 12.6667L7.12504 9.50002L14.6459 1.97918Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <?php endif; ?>
                </div>
                <?php if (!empty($categories)): ?>
                    <p class="text-sm text-gray-600">Catégories :
                        <?php
                        $cat_names = array_map(function ($cat) {
                            // Remplacez 'nom' par le nom réel de la colonne contenant le nom de la catégorie
                            return htmlspecialchars($cat['nom']);
                        }, $categories);
                        echo implode(', ', $cat_names);
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 space-y-2">
                <form action="" method="post">
                    <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                    <button type="submit" name="ajouter_au_panier" class="w-full bg-gray-200 text-blue-600 font-semibold py-2 rounded">Ajouter au panier</button>
                </form>
                <button class="w-full bg-blue-600 text-white font-semibold py-2 rounded">Acheter maintenant</button>
            </div>
        </div>
    </div>

    <?php if ($isEditMode): ?>
        <script>
            function updateField(field, newValue) {
                fetch('/shopping-website/admin/update_article.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id_produit: <?php echo $produit['id_produit']; ?>,
                        field: field,
                        new_value: newValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(field + ' mis à jour avec succès');
                    } else {
                        console.error('Erreur lors de la mise à jour de ' + field + ':', data.message);
                        alert('Erreur lors de la mise à jour de ' + field + ': ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur s\'est produite lors de la mise à jour de ' + field);
                });
            }

            // Utilisez cette fonction pour chaque champ
            function updateTitle(newTitle) {
                updateField('nom', newTitle);
            }

            function updateDescription(newDescription) {
                updateField('description', newDescription);
            }

            function updatePrice(newPrice) {
                updateField('prix', newPrice);
            }

            function updateBrand(newBrand) {
                updateField('marque', newBrand);
            }

            function updateCollection(newCollection) {
                if (['Homme', 'Femme', 'Enfant'].includes(newCollection)) {
                    updateField('collection', newCollection);
                } else {
                    console.error('Collection invalide');
                    alert('Veuillez choisir une collection valide : Homme, Femme ou Enfant');
                }
            }
        </script>
    <?php endif; ?>
    <script src="<?php echo BASE_URL; ?>assets/js/scripts.js" defer></script>
 <script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>

</body>

</html>