<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_db.php';

// La connexion est déjà vérifiée dans _db.php, pas besoin de le refaire ici

$erreurs = [];
$inscription_reussie = false;

// Traitement du formulaire d'inscription
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification que tous les champs sont remplis
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

        // Vérifier si l'email existe déjà
        $sql_check_email = "SELECT * FROM utilisateurs WHERE email = ?";
        $stmt_check_email = $conn->prepare($sql_check_email);
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            $erreurs[] = "Cet email est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            // Validation du mot de passe
            $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            
            if (!preg_match($regex_mdp, $motdepasse)) {
                $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
            } elseif ($motdepasse !== $confirmer_motdepasse) {
                $erreurs[] = "Les mots de passe ne correspondent pas.";
            } else {
                // Hachage du mot de passe
                $motdepasse_hache = password_hash($motdepasse, PASSWORD_DEFAULT);

                // Préparation de la requête SQL
                $sql = "INSERT INTO utilisateurs (nom, prenom, email, motdepasse) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $nom, $prenom, $email, $motdepasse_hache);

                // Exécution de la requête
                if ($stmt->execute()) {
                    $inscription_reussie = true;
                    $_SESSION['message'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                    // Voici la redirection
                    header("Location: connexion.php");
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

// Ne fermez pas la connexion ici, car le fichier _db.php s'en charge déjà
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    
    <?php
    if (!empty($erreurs)) {
        foreach ($erreurs as $erreur) {
            echo "<p style='color: red;'>" . htmlspecialchars($erreur) . "</p>";
        }
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required><br><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="motdepasse">Mot de passe :</label>
        <input type="password" id="motdepasse" name="motdepasse" required><br><br>

        <label for="confirmer_motdepasse">Confirmer le mot de passe :</label>
        <input type="password" id="confirmer_motdepasse" name="confirmer_motdepasse" required><br><br>

        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>
