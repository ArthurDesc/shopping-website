<?php
require_once '../includes/_db.php';
require_once '../includes/_header.php';

$search = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ?");
$searchTerm = '%' . $search . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-4">Résultats de recherche pour "<?php echo htmlspecialchars($search); ?>"</h1>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="bg-white rounded-lg shadow-md p-4">
        <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($row['nom']); ?></h2>
        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($row['description']); ?></p>
        <p class="text-lg font-bold"><?php echo number_format($row['prix'], 2); ?> €</p>
        <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $row['id_produit']; ?>" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Voir le produit</a>
      </div>
    <?php endwhile; ?>
  </div>
</main>

<?php require_once '../includes/_footer.php'; ?>