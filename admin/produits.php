<?php
require_once 'classe/admin.php';
require_once 'config/database.php'; // Assurez-vous que ce fichier existe et configure la connexion à la base de données

$admin = new Admin($conn);

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                $admin->ajouterProduit($_POST['nom'], $_POST['description'], $_POST['prix'], $_POST['stock'], $_POST['categorie_id']);
                break;
            case 'modifier':
                $admin->modifierProduit($_POST['id'], $_POST['nom'], $_POST['description'], $_POST['prix'], $_POST['stock'], $_POST['categorie_id']);
                break;
            case 'supprimer':
                $admin->supprimerProduit($_POST['id']);
                break;
        }
    }
}

// Récupération de la liste des produits
$produits = $admin->listerProduits();
$categories = $admin->listerCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des produits</title>
</head>
<body>
    <h1>Gestion des produits</h1>

    <!-- Formulaire d'ajout de produit -->
    <h2>Ajouter un produit</h2>
    <form method="POST">
        <input type="hidden" name="action" value="ajouter">
        <input type="text" name="nom" placeholder="Nom du produit" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="prix" step="0.01" placeholder="Prix" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <select name="categorie_id" required>
            <?php foreach ($categories as $categorie): ?>
                <option value="<?php echo $categorie['id_categorie']; ?>"><?php echo $categorie['nom']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Ajouter</button>
    </form>

    <!-- Liste des produits avec options de modification et suppression -->
    <h2>Liste des produits</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Description</th>
            <th>Prix</th>
            <th>Stock</th>
            <th>Catégorie</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($produits as $produit): ?>
            <tr>
                <td><?php echo $produit['nom']; ?></td>
                <td><?php echo $produit['description']; ?></td>
                <td><?php echo $produit['prix']; ?></td>
                <td><?php echo $produit['stock']; ?></td>
                <td><?php echo $produit['categorie_id']; ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?php echo $produit['id_produit']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                    <!-- Ici, vous pouvez ajouter un bouton ou un lien pour modifier le produit -->
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
