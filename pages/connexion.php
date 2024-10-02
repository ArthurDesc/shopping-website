<?php
session_start(); // Ajout du point-virgule manquant
include '../includes/_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérifier que les champs 'email' et 'password' existent
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Récupérer les données du formulaire et les sécuriser
        $user = htmlspecialchars(trim($_POST['email'])); // Échapper les caractères spéciaux
        $pass = $_POST['password']; // Garder le mot de passe tel quel

        $sql = "SELECT * FROM utilisateurs WHERE email = ?"; // Correction de la requête SQL
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si l'utilisateur est trouvé
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Vérifier le mot de passe haché avec password_verify
            if (password_verify($pass, $row['password'])) {
                // Connexion réussie
                echo "Connexion réussie ! Bienvenue, " . $row['email'];

                // Stocker les informations de l'utilisateur dans la session
                $_SESSION['id_utilisateur'] = $row['id'];
                $_SESSION['email'] = $row['email'];

                // Rediriger vers la page d'accueil
                header('Location: ../index.php');
                exit(); // Arrêter l'exécution du script après la redirection
            } else {
                echo "Mot de passe incorrect.";
            }
        } else {
            echo "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        echo "Veuillez remplir tous les champs.";
    }
} else {
    echo "Formulaire non soumis.";
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-form h2 {
            margin-bottom: 20px;
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-form button {
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="login-form">
    <h2>Connexion</h2>
    <form action="/connexion.php" method="POST"> <!-- Correction du formulaire -->
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>
