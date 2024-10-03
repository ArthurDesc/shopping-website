<?php
require_once 'classe/admin.php';
require_once 'config/database.php'; // Assurez-vous que ce fichier existe et configure la connexion à la base de données

$admin = new Admin($conn);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $admin->ajouterCategorie($_POST['nom']);
                break;
            case 'modifier':
                $admin->modifierCategorie($_POST['id'], $_POST['nom']);
                break;
            case 'supprimer':
                $admin->supprimerCategorie($_POST['id']);
                break;
        }
    }
}

// Récupération de la liste des catégories
$categories = $admin->listerCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des catégories</title>
</head>
<body>
    <h1>Gestion des catégories</h1>

    <!-- Formulaire d'ajout de catégorie -->
    <h2>Ajouter une catégorie</h2>
    <form method="POST">
        <input type="hidden" name="action" value="ajouter">
        <input type="text" name="nom" placeholder="Nom de la catégorie" required>
        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste des catégories avec options de modification et suppression -->
    <h2>Liste des catégories</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($categories as $categorie): ?>
            <tr>
                <td><?php echo $categorie['nom']; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id" value="<?php echo $categorie['id_categorie']; ?>">
                        <input type="text" name="nom" value="<?php echo $categorie['nom']; ?>" required>
                        <button type="submit">Modifier</button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?php echo $categorie['id_categorie']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
