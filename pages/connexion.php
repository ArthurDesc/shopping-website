<?php
ob_start();
session_start();
include '../includes/_db.php';
include '../includes/session.php';


// Définir BASE_URL seulement s'il n'est pas déjà défini
if (!defined('BASE_URL')) {
    define('BASE_URL', '/php-vanilla/shopping-website/');  // Chemin depuis la racine web
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
    <title>Connexion - Fitmode</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=<?php echo filemtime('../assets/css/main.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">


    <div class="container mx-auto px-4 py-8 flex-grow flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <a href="<?php echo BASE_URL; ?>index.php" class="block mb-8">
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Fitmode" class="w-32 mx-auto">
            </a>
            
            <h1 class="text-2xl font-bold mb-6 text-center">Connectez-vous à votre compte</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="form-input mt-1 block w-full">
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Se connecter
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                <a href="<?php echo BASE_URL; ?>pages/inscription.php" class="text-blue-500 hover:text-blue-600">
                    Pas encore inscrit ? Créez un compte
                </a>
            </p>
        </div>
    </div>
</body>
</html>
