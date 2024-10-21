<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_db.php';

$erreurs = [];
$inscription_reussie = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $champs_requis = ['nom', 'prenom', 'email', 'motdepasse', 'confirmer_motdepasse'];

    foreach ($champs_requis as $champ) {
        if (empty($_POST[$champ])) {
            $erreurs[] = "Le champ " . ucfirst($champ) . " est requis.";
        }
    }

    if (empty($erreurs)) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $motdepasse = $_POST['motdepasse'];
        $confirmer_motdepasse = $_POST['confirmer_motdepasse'];

        $sql_check_email = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            $erreurs[] = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            
            if (!preg_match($regex_mdp, $motdepasse)) {
                $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
            } elseif ($motdepasse !== $confirmer_motdepasse) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            } else {
                $motdepasse_hache = password_hash($motdepasse, PASSWORD_DEFAULT);

                $sql = "INSERT INTO utilisateurs (nom, prenom, email, motdepasse) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $nom, $prenom, $email, $motdepasse_hache);

                if ($stmt->execute()) {
                    $inscription_reussie = true;
                    $_SESSION['message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    header("Location: profil.php");
                    exit();
                } else {
                    $erreurs[] = "Erreur lors de l'inscription : " . $stmt->error;
                }

                $stmt->close();
            }
        }
        $stmt_check_email->close();
    }
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitmode - Inscription</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css?v=<?php echo filemtime('assets/css/main.css'); ?>">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@heroicons/react/outline" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="container mx-auto px-4 py-8 flex-grow flex flex-col items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <a href="<?php echo BASE_URL; ?>index.php" class="block mb-8">
                <img src="<?php echo BASE_URL; ?>assets/images/logo.png" alt="Fitmode" class="w-32 mx-auto">
            </a>
            
            <h1 class="text-2xl font-bold mb-6 text-center">Entre tes coordonnées pour nous rejoindre.</h1>
            
            <?php if (!empty($erreurs)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <?php foreach ($erreurs as $erreur): ?>
                        <p><?php echo htmlspecialchars($erreur); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="space-y-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="nom" name="nom" required class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="motdepasse" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" id="motdepasse" name="motdepasse" required class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="confirmer_motdepasse" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                    <input type="password" id="confirmer_motdepasse" name="confirmer_motdepasse" required class="form-input mt-1 block w-full">
                </div>

                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Valider
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                <a href="<?php echo BASE_URL; ?>pages/connexion.php" class="text-blue-500 hover:text-blue-600">
                    Vous êtes déjà inscrit ? Cliquez ici
                </a>
            </p>
        </div>
    </div>
</body>
</html>
