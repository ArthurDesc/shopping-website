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
    <title>Formulaire de Connexion</title>
 
</head>
<body>
 <?php include '../includes/_header.php'; ?> 


<div class="login-form">
    <h2>Connexion</h2>
    <?php
    if (!empty($error_message)) {
        echo "<p style='color: red;'>" . htmlspecialchars($error_message) . "</p>";
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>
