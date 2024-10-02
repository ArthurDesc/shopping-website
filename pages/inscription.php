<?php
// Paramètres de connexion à la base de données
$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$basededonnees = "boutique";

// Connexion à la base de données
$connexion = new mysqli($serveur, $utilisateur, $motdepasse, $basededonnees);

// Vérification de la connexion
if ($connexion->connect_error) {
    die("La connexion a échoué : " . $connexion->connect_error);
}

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
            $stmt = $connexion->prepare($sql);
            $stmt->bind_param("ssss", $nom, $prenom, $email, $motdepasse_hache);

            // Exécution de la requête
            if ($stmt->execute()) {
                $inscription_reussie = true;
            } else {
                $erreurs[] = "Erreur lors de l'inscription : " . $stmt->error;
            }

            $stmt->close();
        }
    }

    // Assurez-vous de renvoyer une réponse JSON
    header('Content-Type: application/json');
    if ($inscription_reussie) {
        echo json_encode(['status' => 'success', 'message' => 'Inscription réussie']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'inscription', 'errors' => $erreurs]);
    }
    exit;
}

// Si ce n'est pas une requête POST, affichez le formulaire HTML normalement
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
