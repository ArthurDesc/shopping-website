<?php
require_once '../includes/session.php';

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header("Location: connexion.php");
    exit();
}

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

$id_utilisateur = $_SESSION['id_utilisateur'];
$erreurs = [];
$success_message = "";

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT nom, prenom, email, motdepasse FROM utilisateurs WHERE id_utlisateur = ?";
$stmt = $connexion->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Traitement du formulaire de mise à jour
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification que tous les champs sont remplis
    $champs_requis = ['nom', 'prenom', 'email', 'motdepasse_actuel'];
    foreach ($champs_requis as $champ) {
        if (empty($_POST[$champ])) {
            $erreurs[] = "Le champ " . ucfirst(str_replace('_', ' ', $champ)) . " est requis.";
        }
    }

    if (empty($erreurs)) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $motdepasse_actuel = $_POST['motdepasse_actuel'];
        $nouveau_motdepasse = $_POST['nouveau_motdepasse'];
        $confirmer_nouveau_motdepasse = $_POST['confirmer_nouveau_motdepasse'];

        // Vérifier le mot de passe actuel
        if (!password_verify($motdepasse_actuel, $user['motdepasse'])) {
            $erreurs[] = "Le mot de passe actuel est incorrect.";
        } else {
            // Mise à jour des informations de base
            $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?";
            $stmt = $connexion->prepare($sql);
            $stmt->bind_param("sssi", $nom, $prenom, $email, $id_utilisateur);
            $stmt->execute();
            $stmt->close();

            // Mise à jour du mot de passe si fourni
            if (!empty($nouveau_motdepasse)) {
                if ($nouveau_motdepasse !== $confirmer_nouveau_motdepasse) {
                    $erreurs[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
                } else {
                    $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
                    
                    if (!preg_match($regex_mdp, $nouveau_motdepasse)) {
                        $erreurs[] = "Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
                    } else {
                        $motdepasse_hache = password_hash($nouveau_motdepasse, PASSWORD_DEFAULT);
                        $sql = "UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ?";
                        $stmt = $connexion->prepare($sql);
                        $stmt->bind_param("si", $motdepasse_hache, $id_utilisateur);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }

            if (empty($erreurs)) {
                $success_message = "Profil mis à jour avec succès !";
                // Mettre à jour les informations dans la session
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['email'] = $email;
            }
        }
    }
}

$connexion->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>
<body>
    <h2>Profil de <?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h2>
    
    <?php
    if (!empty($erreurs)) {
        foreach ($erreurs as $erreur) {
            echo "<p style='color: red;'>" . htmlspecialchars($erreur) . "</p>";
        }
    }
    if (!empty($success_message)) {
        echo "<p style='color: green;'>" . htmlspecialchars($success_message) . "</p>";
    }
    ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required><br><br>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required><br><br>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

        <label for="motdepasse_actuel">Mot de passe actuel :</label>
        <input type="password" id="motdepasse_actuel" name="motdepasse_actuel" required><br><br>

        <label for="nouveau_motdepasse">Nouveau mot de passe (laissez vide pour ne pas changer) :</label>
        <input type="password" id="nouveau_motdepasse" name="nouveau_motdepasse"><br><br>

        <label for="confirmer_nouveau_motdepasse">Confirmer le nouveau mot de passe :</label>
        <input type="password" id="confirmer_nouveau_motdepasse" name="confirmer_nouveau_motdepasse"><br><br>

        <input type="submit" value="Mettre à jour le profil">
    </form>
</body>
</html>
