<?php
session_start();
require_once '../includes/session.php';
require_once '../includes/_db.php';
require_once '../classe/AdminManager.php'; // Ajoutez cette ligne

// Vérifier si l'utilisateur est connecté
if (!is_logged_in()) {
    header("Location: " . BASE_URL . "pages/auth.php");
    exit();
}

// Créer une instance de AdminManager
$adminManager = new AdminManager($conn);


// Inclure le header seulement après la vérification de connexion

$id_utilisateur = $_SESSION['id_utilisateur'];
$erreurs = [];
$success_message = "";

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT nom, prenom, email, motdepasse FROM utilisateurs WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


// Vérifier si l'utilisateur est un admin
if ($adminManager->isAdmin($id_utilisateur)) {
    // Rediriger vers la page backoffice.php
    header("Location: " . BASE_URL . "admin/backoffice.php");
    exit();
}

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
            $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $nom, $prenom, $email, $id_utilisateur);
            $stmt->execute();
            $stmt->close();

            // Mise à jour du mot de passe si fourni
            if (!empty($nouveau_motdepasse)) {
                if ($nouveau_motdepasse !== $confirmer_nouveau_motdepasse) {
                    $erreurs[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
                } else {
                    $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._-,])[A-Za-z\d@$!%*?&._-,]{8,}$/";
                    
                    if (!preg_match($regex_mdp, $nouveau_motdepasse)) {
                        $erreurs[] = "Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
                    } else {
                        $motdepasse_hache = password_hash($nouveau_motdepasse, PASSWORD_DEFAULT);
                        $sql = "UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ?";
                        $sql = "UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ?";
                        $stmt = $conn->prepare($sql);
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
                $_SESSION['motdepasse'] = $nouveau_motdepasse;
                $_SESSION['motdepasse'] = $confirmer_nouveau_motdepasse;
            }
        }
    }
}


$conn->close();
include '../includes/_header.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout des liens CDN pour Materialize CSS et JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<body class="bg-gray-100">
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
                    <button type="button" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-4 modal-trigger" data-target="modal-confirmation">
                        Valider les modifications
                    </button>
                </div>
            </form>
            
            <!-- Bouton de déconnexion -->
            <div>
                <a href="#modal-deconnexion" class="block w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline text-center modal-trigger">
                    Me déconnecter
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation des modifications -->
    <div id="modal-confirmation" class="modal">
        <div class="modal-content">
            <h4>Confirmation des modifications</h4>
            <p>Êtes-vous sûr de vouloir valider ces modifications ?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annuler</a>
            <a href="#!" class="modal-close waves-effect waves-blue btn-flat" onclick="document.getElementById('profile-form').submit();">Confirmer</a>
        </div>
    </div>

    <!-- Modal de confirmation de déconnexion -->
    <div id="modal-deconnexion" class="modal">
        <div class="modal-content">
            <h4>Confirmation de déconnexion</h4>
            <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Annuler</a>
            <a href="<?php echo BASE_URL; ?>pages/deconnexion.php" class="modal-close waves-effect waves-red btn-flat">Confirmer</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var elems = document.querySelectorAll('.modal');
            var instances = M.Modal.init(elems);
        });
    </script>
</body>
</html>