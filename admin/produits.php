<?php
require_once 'classe/ArticleManager.php';
require_once 'classe/CategoryManager.php';
require_once 'classe/AdminManager.php';
require_once 'config/database.php'; // Assurez-vous que ce fichier configure la connexion à la base de données

session_start();

// Vérifier si l'utilisateur est connecté et est un admin
$adminManager = new AdminManager($db);
if (!isset($_SESSION['user_id']) || !$adminManager->isAdmin($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$articleManager = new ArticleManager($db);
$categoryManager = new CategoryManager($db);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
                $articleManager->addArticle(
                    $_POST['nom'], 
                    $_POST['description'], 
                    $_POST['prix'], 
                    $_POST['stock'], 
                    $_POST['taille'], 
                    $_POST['marque'], 
                    $_POST['collection'],
                    $categories
                );
                break;
            case 'edit':
                $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
                $articleManager->updateArticle(
                    $_POST['id'], 
                    $_POST['nom'], 
                    $_POST['description'], 
                    $_POST['prix'], 
                    $_POST['stock'], 
                    $_POST['taille'], 
                    $_POST['marque'], 
                    $_POST['collection'],
                    $categories
                );
                break;
            case 'delete':
                $articleManager->deleteArticle($_POST['id']);
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
    <title>Gestion des produits</title>
</head>
<body>
    <h1>Gestion des produits</h1>

    <!-- Formulaire d'ajout de produit -->
    <h2>Ajouter un produit</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="nom" placeholder="Nom du produit" required>
        <textarea name="description" placeholder="Description du produit"></textarea>
        <input type="number" name="prix" step="0.01" placeholder="Prix" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <input type="text" name="taille" placeholder="Taille">
        <input type="text" name="marque" placeholder="Marque">
        <input type="text" name="collection" placeholder="Collection">
        
        <!-- Sélection des catégories -->
        <fieldset>
            <legend>Catégories</legend>
            <?php foreach ($categories as $category): ?>
                <label>
                    <input type="checkbox" name="categories[]" value="<?php echo $category['id_categorie']; ?>">
                    <?php echo htmlspecialchars($category['nom']); ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>

        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste des produits -->
    <h2>Liste des produits</h2>
    <table>
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
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($produit['nom']); ?>" required>
                        <textarea name="description"><?php echo htmlspecialchars($produit['description']); ?></textarea>
                        <input type="number" name="prix" step="0.01" value="<?php echo htmlspecialchars($produit['prix']); ?>" required>
                        <input type="number" name="stock" value="<?php echo htmlspecialchars($produit['stock']); ?>" required>
                        <input type="text" name="taille" value="<?php echo htmlspecialchars($produit['taille']); ?>">
                        <input type="text" name="marque" value="<?php echo htmlspecialchars($produit['marque']); ?>">
                        <input type="text" name="collection" value="<?php echo htmlspecialchars($produit['collection']); ?>">
                        
                        <!-- Sélection des catégories pour la modification -->
                        <fieldset>
                            <legend>Catégories</legend>
                            <?php 
                            $produitCategories = $articleManager->getArticleCategories($produit['id_produit']);
                            foreach ($categories as $category): 
                                $isChecked = in_array($category['id_categorie'], array_column($produitCategories, 'id_categorie'));
                            ?>
                                <label>
                                    <input type="checkbox" name="categories[]" value="<?php echo $category['id_categorie']; ?>" <?php echo $isChecked ? 'checked' : ''; ?>>
                                    <?php echo htmlspecialchars($category['nom']); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </fieldset>

                        <button type="submit">Modifier</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
