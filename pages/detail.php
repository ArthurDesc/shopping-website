<?php
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

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

// Définir une image par défaut si image_url est vide
$image_url = !empty($produit['image_url']) ? $produit['image_url'] : '../assets/images/default_product.jpg';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($produit['nom']); ?> - Détails</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

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
        <div class="space-y-2">
            <h2 class="text-xl font-bold"><?php echo htmlspecialchars($produit['nom']); ?></h2>
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
            <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($produit['description']); ?></p>
            <div class="flex justify-between items-center">
                <span class="font-bold text-lg"><?php echo number_format($produit['prix'], 2); ?> €</span>
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
            <p class="text-sm text-gray-600">Marque : <?php echo htmlspecialchars($produit['marque']); ?></p>
            <p class="text-sm text-gray-600">Collection : <?php echo htmlspecialchars($produit['collection']); ?></p>
            <?php if (!empty($categories)): ?>
                <p class="text-sm text-gray-600">Catégories : 
                    <?php 
                    $cat_names = array_map(function($cat) {
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

</body>
</html>