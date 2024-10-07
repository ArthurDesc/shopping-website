<?php
// Commentez temporairement ces lignes pour le débogage
ob_start();
session_start();
include '../includes/_db.php';
include '../includes/session.php';


// Définir BASE_URL seulement s'il n'est pas déjà défini
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $user = htmlspecialchars(trim($_POST['email']));
        $pass = $_POST['password'];

        $sql = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['motdepasse'])) {
                $_SESSION['id_utilisateur'] = $row['id_utilisateur'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['nom'] = $row['nom'];
                $_SESSION['prenom'] = $row['prenom'];

                // Nettoyage du buffer de sortie
                ob_end_clean();

                // Redirection vers la page de profil
                header('Location: ' . BASE_URL . 'index.php');
                exit();
            } else {
                $error_message = "Mot de passe incorrect.";
            }
        } else {
            $error_message = "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}

// Fermer la connexion
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitmode</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/@heroicons/react/outline" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="flex-grow flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-sm bg-white rounded-lg shadow-md p-6 sm:p-8">
            <a href="<?php echo BASE_URL; ?>index.php">
                <img src="../assets/images/logo.png" alt="Logo" class="mx-auto h-10 sm:h-12 mb-6 cursor-pointer">
            </a>
            <h2 class="text-2xl sm:text-3xl text-center mb-6 font-bold">Connectez-vous à votre compte</h2>
            
            <?php
            if (!empty($error_message)) {
                echo "<p class='text-red-500 text-center mb-4'>" . htmlspecialchars($error_message) . "</p>";
            }
            ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="space-y-6">
                <div>
                    <input type="email" name="email" placeholder="Email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Mot de passe" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" 
                        class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-300">
                    Se connecter
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="#" class="text-blue-500 hover:underline">Vous n'êtes pas encore inscrit ? Cliquez ici</a>
            </div>
        </div>
    </div>
</body>
</html>
