<?php
if (!defined('BASE_URL')) {
  define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
}

require_once '../classe/ArticleManager.php';
require_once '../classe/CategoryManager.php';
require_once '../classe/AdminManager.php';
require_once '../includes/_db.php';

session_start();

// Vérifier si l'utilisateur est connecté et est un admin
$adminManager = new AdminManager($conn);
if (!isset($_SESSION['id_utilisateur']) || !$adminManager->isAdmin($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$articleManager = new ArticleManager($conn);
$categoryManager = new CategoryManager($conn);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
                $articleManager->addArticle($_POST['nom'], $_POST['description'], $_POST['prix'], $_POST['stock'], $_POST['taille'], $_POST['marque'], $_POST['collection'], $categories);
                break;
            case 'edit_product':
                $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
                // Filtrer les catégories pour ne garder que celles qui sont sélectionnées
                $categories = array_filter($categories, function($value) { return !empty($value); });
                if ($articleManager->updateArticle(
                    $_POST['id'],
                    $_POST['nom'],
                    $_POST['description'],
                    $_POST['prix'],
                    $_POST['stock'],
                    $_POST['taille'],
                    $_POST['marque'],
                    $_POST['collection'],
                    $categories
                )) {
                    echo "Article mis à jour avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour de l'article.";
                }
                break;
            case 'delete_product':
                $articleManager->deleteArticle($_POST['id']);
                break;
            case 'add_category':
                $categoryManager->addCategory($_POST['nom'], $_POST['description']);
                break;
            case 'edit_category':
                $categoryManager->updateCategory($_POST['id'], $_POST['nom'], $_POST['description']);
                break;
            case 'delete_category':
                $categoryManager->deleteCategory($_POST['id']);
                break;
            case 'update_article_categories':
                $articleId = $_POST['article_id'];
                $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
                if ($articleManager->updateArticleCategories($articleId, $selectedCategories)) {
                    echo "Catégories mises à jour avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour des catégories.";
                }
                break;
            case 'update_article':
                $articleId = $_POST['article_id'];
                $nom = $_POST['nom'];
                $description = $_POST['description'];
                $prix = $_POST['prix'];
                $selectedCategories = isset($_POST['categories']) ? $_POST['categories'] : [];
                if ($articleManager->updateArticle($articleId, $nom, $description, $prix, $selectedCategories)) {
                    echo "Article mis à jour avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour de l'article.";
                }
                break;
            case 'update_item':
                $itemId = $_POST['item_id'];
                $nom = $_POST['nom'];
                $description = $_POST['description'];
                // Récupérez d'autres champs si nécessaire
                
                // Appelez votre méthode de mise à jour ici
                if ($itemManager->updateItem($itemId, $nom, $description /*, autres champs */)) {
                    echo "Item mis à jour avec succès.";
                } else {
                    echo "Erreur lors de la mise à jour de l'item.";
                }
                break;
        }
    }
}

// Récupération de tous les produits et catégories
$produits = $articleManager->getAllArticles();
$categories = $categoryManager->getAllCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de la boutique</title>
    <style>
        .section {
            margin-bottom: 30px;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body>
    <a href="./backofficeV2.php">BackofficeV2</a>
    <h1>Gestion de la boutique</h1>

    <div class="section">
        <h2>Gestion des produits</h2>

        <!-- Formulaire d'ajout de produit -->
        <h3>Ajouter un produit</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_product">
            <input type="text" name="nom" placeholder="Nom du produit" required>
            <textarea name="description" placeholder="Description du produit"></textarea>
            <input type="number" name="prix" step="0.01" placeholder="Prix" required>
            <input type="number" name="stock" placeholder="Stock" required>
            <input type="text" name="taille" placeholder="Taille">
            <input type="text" name="marque" placeholder="Marque">
            <input type="text" name="collection" placeholder="Collection">
            
            <fieldset>
                <legend>Catégories</legend>
                <?php foreach ($categories as $category): ?>
                    <label>
                        <input type="checkbox" name="categories[]" value="<?php echo $category['id_categorie']; ?>">
                        <?php echo htmlspecialchars($category['nom']); ?>
                    </label><br>
                <?php endforeach; ?>
            </fieldset>

            <button type="submit">Ajouter le produit</button>
        </form>

        <!-- Liste des produits -->
        <h3>Liste des produits</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Taille</th>
                <th>Marque</th>
                <th>Collection</th>
                <th>Catégories</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($produits as $produit): ?>
                <tr id="product-row-<?php echo $produit['id_produit']; ?>">
                    <td><?php echo htmlspecialchars($produit['id_produit']); ?></td>
                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td><?php echo htmlspecialchars($produit['description']); ?></td>
                    <td><?php echo htmlspecialchars($produit['prix']); ?></td>
                    <td><?php echo htmlspecialchars($produit['stock']); ?></td>
                    <td><?php echo htmlspecialchars($produit['taille']); ?></td>
                    <td><?php echo htmlspecialchars($produit['marque']); ?></td>
                    <td><?php echo htmlspecialchars($produit['collection']); ?></td>
                    <td><?php echo htmlspecialchars($produit['categories']); ?></td>
                    <td>
                        <button onclick="toggleEditProduct(<?php echo $produit['id_produit']; ?>)">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_product">
                            <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-product-form-<?php echo $produit['id_produit']; ?>" style="display:none;">
                    <td colspan="9">
                        <form method="POST">
                            <input type="hidden" name="action" value="edit_product">
                            <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
                            <textarea name="description"><?php echo htmlspecialchars($produit['description']); ?></textarea>
                            <input type="number" name="prix" step="0.01" value="<?php echo htmlspecialchars($produit['prix']); ?>" required>
                            <input type="number" name="stock" value="<?php echo htmlspecialchars($produit['stock']); ?>" required>
                            <input type="text" name="taille" value="<?php echo htmlspecialchars($produit['taille']); ?>">
                            <input type="text" name="marque" value="<?php echo htmlspecialchars($produit['marque']); ?>">
                            <input type="text" name="collection" value="<?php echo htmlspecialchars($produit['collection']); ?>">
                            
                            <fieldset>
                                <legend>Catégories</legend>
                                <?php 
                                $produitCategories = $articleManager->getArticleCategories($produit['id_produit']);
                                foreach ($categories as $category): 
                                    $isChecked = in_array($category['id_categorie'], array_column($produitCategories, 'id_categorie'));
                                ?>
                                    <label>
                                        <input type="checkbox" name="categories[]" value="<?php echo $category['id_categorie']; ?>"
                                               <?php echo $isChecked ? 'checked' : ''; ?>>
                                        <?php echo htmlspecialchars($category['nom']); ?>
                                    </label><br>
                                <?php endforeach; ?>
                            </fieldset>

                            <button type="submit">Enregistrer</button>
                            <button type="button" onclick="toggleEditProduct(<?php echo $produit['id_produit']; ?>)">Annuler</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Gestion des catégories</h2>

        <!-- Formulaire d'ajout de catégorie -->
        <h3>Ajouter une catégorie</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_category">
            <input type="text" name="nom" placeholder="Nom de la catégorie" required>
            <textarea name="description" placeholder="Description de la catégorie"></textarea>
            <button type="submit">Ajouter la catégorie</button>
        </form>

        <!-- Liste des catégories -->
        <h3>Liste des catégories</h3>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['id_categorie']); ?></td>
                    <td id="nom-<?php echo $category['id_categorie']; ?>"><?php echo htmlspecialchars($category['nom']); ?></td>
                    <td id="description-<?php echo $category['id_categorie']; ?>"><?php echo htmlspecialchars($category['description']); ?></td>
                    <td>
                        <button onclick="toggleEditCategory(<?php echo $category['id_categorie']; ?>)">Edit</button>
                        <form method="POST" style="display:none;" id="edit-category-form-<?php echo $category['id_categorie']; ?>">
                            <input type="hidden" name="action" value="edit_category">
                            <input type="hidden" name="id" value="<?php echo $category['id_categorie']; ?>">
                            <input type="text" name="nom" id="edit-nom-<?php echo $category['id_categorie']; ?>" value="<?php echo htmlspecialchars($category['nom']); ?>" required>
                            <textarea name="description" id="edit-description-<?php echo $category['id_categorie']; ?>"><?php echo htmlspecialchars($category['description']); ?></textarea>
                            <button type="submit">Enregistrer</button>
                            <button type="button" onclick="toggleEditCategory(<?php echo $category['id_categorie']; ?>)">Annuler</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_category">
                            <input type="hidden" name="id" value="<?php echo $category['id_categorie']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('.item');
    
    items.forEach(item => {
        const editButton = item.querySelector('.edit-button');
        const cancelButton = item.querySelector('.cancel-edit');
        const itemInfo = item.querySelector('.item-info');
        const itemEdit = item.querySelector('.item-edit');
        
        editButton.addEventListener('click', function() {
            itemInfo.style.display = 'none';
            itemEdit.style.display = 'block';
            editButton.style.display = 'none';
        });
        
        cancelButton.addEventListener('click', function() {
            itemInfo.style.display = 'block';
            itemEdit.style.display = 'none';
            editButton.style.display = 'block';
        });
    });
});

function editArticle(articleId) {
    document.getElementById('edit-form-' + articleId).style.display = 'table-row';
}

function cancelEdit(articleId) {
    document.getElementById('edit-form-' + articleId).style.display = 'none';
}

function toggleEditForm(productId) {
    var form = document.getElementById('edit-form-' + productId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function toggleEditCategory(categoryId) {
    var form = document.getElementById('edit-category-form-' + categoryId);
    var nomCell = document.getElementById('nom-' + categoryId);
    var descriptionCell = document.getElementById('description-' + categoryId);
    var editNomInput = document.getElementById('edit-nom-' + categoryId);
    var editDescriptionInput = document.getElementById('edit-description-' + categoryId);

    if (form.style.display === 'none' || form.style.display === '') {
        // Afficher le formulaire d'édition
        form.style.display = 'block';
        nomCell.style.display = 'none';
        descriptionCell.style.display = 'none';
    } else {
        // Cacher le formulaire d'édition et réafficher les valeurs originales
        form.style.display = 'none';
        nomCell.style.display = '';
        descriptionCell.style.display = '';
        // Réinitialiser les valeurs du formulaire
        editNomInput.value = nomCell.textContent;
        editDescriptionInput.value = descriptionCell.textContent;
    }
}

function toggleEditProduct(productId) {
    var productRow = document.getElementById('product-row-' + productId);
    var editForm = document.getElementById('edit-product-form-' + productId);
    
    if (editForm.style.display === 'none' || editForm.style.display === '') {
        // Afficher le formulaire d'édition
        editForm.style.display = 'table-row';
        productRow.style.display = 'none';
    } else {
        // Cacher le formulaire d'édition et réafficher la ligne du produit
        editForm.style.display = 'none';
        productRow.style.display = 'table-row';
    }
}
</script>