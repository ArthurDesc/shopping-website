<?php
ob_start();
session_start();
require_once __DIR__ . '/../includes/_db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../functions/url.php';

// Définir BASE_URL seulement s'il n'est pas déjà défini
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
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
                header('Location: ' . url('index.php'));
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
    
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Ressources locales -->
    <link rel="icon" type="image/png" href="<?php echo url('assets/images/favicon.png'); ?>">
    <?php include __DIR__ . '/../includes/_fonts.php'; ?>
    
    <!-- CSS principaux -->
    <link rel="stylesheet" href="<?php echo url('assets/css/main.css?v=' . filemtime(__DIR__ . '/../assets/css/main.css')); ?>">
    <link rel="stylesheet" href="<?php echo url('assets/css/responsive.css?v=' . filemtime(__DIR__ . '/../assets/css/responsive.css')); ?>">
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="container mx-auto px-4 py-8 flex-grow flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <a href="<?php echo url('index.php'); ?>" class="block mb-8">
                <img src="<?php echo url('assets/images/logo.png'); ?>" alt="Fitmode" class="w-32 mx-auto">
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
                    <input type="email" id="email" name="email" required class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" id="password" name="password" required class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                    Se connecter
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                <a href="<?php echo url('pages/inscription.php'); ?>" class="text-blue-500 hover:text-blue-600">
                    Pas encore inscrit ? Créez un compte
                </a>
            </p>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/_scripts.php'; ?>
</body>
</html>
