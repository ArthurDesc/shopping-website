<?php
// Déplacer session_start() et la vérification tout en haut, avant toute sortie
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexion.php");
    exit();
}

require_once dirname(__FILE__) . '/../includes/_header.php';
require_once dirname(__FILE__) . '/../includes/_db.php';

// Ajouter après session_start()
$erreurs = [];
$success = false;

// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, $id_utilisateur) {
    $stmt = $conn->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $id_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Récupérer les informations actuelles de l'utilisateur
$user = getUserInfo($conn, $_SESSION['id_utilisateur']);

// Au début du fichier, après la vérification de connexion
// Récupérer le rôle de l'utilisateur
$stmt = $conn->prepare("SELECT role FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->bind_param("i", $_SESSION['id_utilisateur']);
$stmt->execute();
$result = $stmt->get_result();

// Avant la ligne 31, ajoutons une vérification
$user_role = null;
if ($result && $result->num_rows > 0) {
    $user_role = $result->fetch_assoc()['role'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier le mot de passe actuel
    $stmt = $conn->prepare("SELECT motdepasse FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $_SESSION['id_utilisateur']);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user = $result->fetch_assoc();

    if (!password_verify($_POST['motdepasse_actuel'], $current_user['motdepasse'])) {
        $erreurs[] = "Le mot de passe actuel est incorrect.";
    } else {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $nouveau_motdepasse = $_POST['nouveau_motdepasse'] ?? '';
        $confirmer_nouveau_motdepasse = $_POST['confirmer_nouveau_motdepasse'] ?? '';

        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        $stmt = $conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
        $stmt->bind_param("si", $email, $_SESSION['id_utilisateur']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $erreurs[] = "Cet email est déjà utilisé par un autre utilisateur.";
        }

        // Vérification du nouveau mot de passe si fourni
        if (!empty($nouveau_motdepasse)) {
            $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            
            if (!preg_match($regex_mdp, $nouveau_motdepasse)) {
                $erreurs[] = "Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
            } elseif ($nouveau_motdepasse !== $confirmer_nouveau_motdepasse) {
                $erreurs[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }

        if (empty($erreurs)) {
            // Construire la requête de mise à jour
            $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?";
            $params = [$nom, $prenom, $email];
            $types = "sss";

            if (!empty($nouveau_motdepasse)) {
                $sql .= ", motdepasse = ?";
                $params[] = password_hash($nouveau_motdepasse, PASSWORD_DEFAULT);
                $types .= "s";
            }

            $sql .= " WHERE id_utilisateur = ?";
            $params[] = $_SESSION['id_utilisateur'];
            $types .= "i";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success = true;
                // Mettre à jour les informations de session
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['email'] = $email;
                
                // Récupérer les informations mises à jour
                $user = getUserInfo($conn, $_SESSION['id_utilisateur']);
                
                // Message de succès qui disparaîtra après 3 secondes
                $success_message = "Profil mis à jour avec succès !";
            } else {
                $erreurs[] = "Erreur lors de la mise à jour du profil.";
            }
        }
    }
}
?>

<!-- HTML du formulaire -->
<div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-10">
    <div class="p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Mon Profil</h2>
            <div class="flex space-x-4">
                <?php if (isset($user_role) && $user_role === 'admin'): ?>
                    <a href="../admin/backofficeV2.php" 
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        Administration
                    </a>
                <?php endif; ?>
                
                <a href="deconnexion.php" 
                   class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3zm11.707 4.707a1 1 0 0 0-1.414-1.414L10 9.586 6.707 6.293a1 1 0 0 0-1.414 1.414L8.586 11l-3.293 3.293a1 1 0 1 0 1.414 1.414L10 12.414l3.293 3.293a1 1 0 0 0 1.414-1.414L11.414 11l3.293-3.293z" clip-rule="evenodd"/>
                    </svg>
                    Déconnexion
                </a>
            </div>
        </div>

        <?php if ($success): ?>
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <p><?php echo $success_message; ?></p>
            </div>
            <script>
                // Faire disparaître le message après 3 secondes
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php foreach ($erreurs as $erreur): ?>
                    <p><?php echo htmlspecialchars($erreur); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <div>
                <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                <input type="text" id="nom" name="nom" 
                       value="<?php echo htmlspecialchars($user['nom']); ?>" 
                       required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                <input type="text" id="prenom" name="prenom" 
                       value="<?php echo htmlspecialchars($user['prenom']); ?>" 
                       required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                       required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div class="border-t pt-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Changer le mot de passe</h3>
                
                <div class="space-y-4">
                    <div>
                        <label for="motdepasse_actuel" class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                        <input type="password" id="motdepasse_actuel" name="motdepasse_actuel" 
                               required 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label for="nouveau_motdepasse" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                        <input type="password" id="nouveau_motdepasse" name="nouveau_motdepasse" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <p class="mt-1 text-sm text-gray-500">
                            8 caractères minimum, avec majuscule, chiffre et caractère spécial
                        </p>
                    </div>

                    <div>
                        <label for="confirmer_nouveau_motdepasse" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="confirmer_nouveau_motdepasse" name="confirmer_nouveau_motdepasse" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="submit" 
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Mettre à jour le profil
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Ajouter une animation pour le message de succès
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        successMessage.style.transition = 'opacity 0.5s ease-in-out';
        setTimeout(() => {
            successMessage.style.opacity = '0';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 500);
        }, 2500);
    }
});
</script>




