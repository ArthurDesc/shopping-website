<?php
define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet

require_once '../includes/session.php';
require_once '../includes/_db.php';
require_once '../classe/AdminManager.php';

if (!is_logged_in()) {
    header("Location: " . BASE_URL . "pages/auth.php");
    exit();
}

$adminManager = new AdminManager($conn);

$id_utilisateur = $_SESSION['id_utilisateur'];
$erreurs = [];
$success_message = "";

$sql = "SELECT nom, prenom, email, motdepasse FROM utilisateurs WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($adminManager->isAdmin($id_utilisateur)) {
    header("Location: " . BASE_URL . "admin/backoffice.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Le code de traitement du formulaire reste inchangé
    // ...
}

$conn->close();
include '../includes/_header.php';
?>

<main>

    
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-10">
    <div class="p-4">
        <div class="flex justify-center mb-4">
            <div class="relative">
                <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <form id="profile-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-8">
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="motdepasse_actuel" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="motdepasse_actuel" name="motdepasse_actuel" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse domicile</label>
                <input type="text" id="adresse" name="adresse" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>

            <div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-4">
                    Valider les modifications
                </button>
            </div>
        </form>
        
        <!-- Bouton de déconnexion -->
        <div>
            <a href="<?php echo BASE_URL; ?>pages/deconnexion.php" class="block w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center">
                Me déconnecter
            </a>
        </div>
    </div>
</div>
</main>

<?php include '../includes/_footer.php'; ?>

<script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>
</body>
</html>