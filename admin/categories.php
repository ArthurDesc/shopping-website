<?php
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

$categoryManager = new CategoryManager($db);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $categoryManager->addCategory($_POST['nom'], $_POST['description']);
                break;
            case 'edit':
                $categoryManager->updateCategory($_POST['id'], $_POST['nom'], $_POST['description']);
                break;
            case 'delete':
                $categoryManager->deleteCategory($_POST['id']);
                break;
        }
    }
}

// Récupération de toutes les catégories
$categories = $categoryManager->getAllCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des catégories</title>
</head>
<body>
    <h1>Gestion des catégories</h1>

    <!-- Formulaire d'ajout de catégorie -->
    <h2>Ajouter une catégorie</h2>
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="nom" placeholder="Nom de la catégorie" required>
        <textarea name="description" placeholder="Description de la catégorie"></textarea>
        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste des catégories -->
    <h2>Liste des catégories</h2>
    <table>
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
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?php echo $category['id_categorie']; ?>">
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($category['nom']); ?>" required>
                        <textarea name="description"><?php echo htmlspecialchars($category['description']); ?></textarea>
                        <button type="submit">Modifier</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $category['id_categorie']; ?>">
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
