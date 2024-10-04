<?php
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
                $articleManager->updateArticle($_POST['id'], $_POST['nom'], $_POST['description'], $_POST['prix'], $_POST['stock'], $_POST['taille'], $_POST['marque'], $_POST['collection'], $categories);
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
    </style>
</head>
<body>
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
                <th>Actions</th>
            </tr>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['id_produit']); ?></td>
                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td><?php echo htmlspecialchars($produit['description']); ?></td>
                    <td><?php echo htmlspecialchars($produit['prix']); ?></td>
                    <td><?php echo htmlspecialchars($produit['stock']); ?></td>
                    <td><?php echo htmlspecialchars($produit['taille']); ?></td>
                    <td><?php echo htmlspecialchars($produit['marque']); ?></td>
                    <td><?php echo htmlspecialchars($produit['collection']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
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

                            <button type="submit">Modifier</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete_product">
                            <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
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
                    <td><?php echo htmlspecialchars($category['nom']); ?></td>
                    <td><?php echo htmlspecialchars($category['description']); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="edit_category">
                            <input type="hidden" name="id" value="<?php echo $category['id_categorie']; ?>">
                            <input type="text" name="nom" value="<?php echo htmlspecialchars($category['nom']); ?>" required>
                            <textarea name="description"><?php echo htmlspecialchars($category['description']); ?></textarea>
                            <button type="submit">Modifier</button>
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
