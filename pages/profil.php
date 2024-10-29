<?php
// Déplacer session_start() et la vérification tout en haut, avant toute sortie
session_start();
require_once dirname(__FILE__) . '/../includes/_header.php';

require_once dirname(__FILE__) . '/../includes/_db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

$erreurs = [];
$success = false;

// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, $id_utilisateur) {
    $stmt = $conn->prepare("SELECT nom, prenom, email, adresse, telephone FROM utilisateurs WHERE id_utilisateur = ?");
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
$user_role = $result->fetch_assoc()['role'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $erreurs = [];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $nouveau_motdepasse = $_POST['nouveau_motdepasse'] ?? '';
    $confirmer_nouveau_motdepasse = $_POST['confirmer_nouveau_motdepasse'] ?? '';
    $motdepasse_actuel = $_POST['motdepasse_actuel'] ?? '';

    // Vérifier si l'utilisateur tente de changer le mot de passe
    $changement_motdepasse = !empty($nouveau_motdepasse) || !empty($confirmer_nouveau_motdepasse);

    // Vérifier le mot de passe actuel uniquement si changement de mot de passe
    if ($changement_motdepasse) {
        if (empty($motdepasse_actuel)) {
            $erreurs[] = "Le mot de passe actuel est requis pour changer le mot de passe.";
        } else {
            $stmt = $conn->prepare("SELECT motdepasse FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt->bind_param("i", $_SESSION['id_utilisateur']);
            $stmt->execute();
            $result = $stmt->get_result();
            $current_user = $result->fetch_assoc();

            if (!password_verify($motdepasse_actuel, $current_user['motdepasse'])) {
                $erreurs[] = "Le mot de passe actuel est incorrect.";
            }
        }

        // Vérification du nouveau mot de passe
        if (!empty($nouveau_motdepasse)) {
            $regex_mdp = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
            
            if (!preg_match($regex_mdp, $nouveau_motdepasse)) {
                $erreurs[] = "Le nouveau mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.";
            } elseif ($nouveau_motdepasse !== $confirmer_nouveau_motdepasse) {
                $erreurs[] = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }
    }

    // Vérifier si l'email est déjà utilisé par un autre utilisateur
    $stmt = $conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
    $stmt->bind_param("si", $email, $_SESSION['id_utilisateur']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $erreurs[] = "Cet email est déjà utilisé par un autre utilisateur.";
    }

    if (empty($erreurs)) {
        // Construire la requête de mise à jour
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, adresse = ?, telephone = ?";
        $params = [$nom, $prenom, $email, $adresse, $telephone];
        $types = "sssss";

        if ($changement_motdepasse && !empty($nouveau_motdepasse)) {
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
?>

<div class="gradient-container fixed inset-0 -z-10">
    <div class="gradient"></div>
</div>

<!-- Wrapper principal avec centrage -->
<div class="min-h-screen flex items-center justify-center p-4 relative z-10">
    <!-- Conteneur du formulaire -->
    <div class="w-full max-w-md bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6 space-y-6 transform transition-all duration-300 hover:-translate-y-1">
        <!-- En-tête avec boutons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Mon Profil</h2>
            <div class="flex flex-wrap gap-3">
                <?php if ($user_role === 'admin'): ?>
                    <a href="../admin/backofficeV2.php" 
                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors duration-200 text-sm">
                       <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                        </svg>
                        Administration
                    </a>
                <?php endif; ?>
                
                <a href="deconnexion.php" 
                   class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                    </svg>
                    Déconnexion
                </a>
            </div>
        </div>

        <!-- Messages de succès et d'erreur -->
        <?php if ($success): ?>
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($erreurs)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <?php foreach ($erreurs as $erreur): ?>
                    <p><?php echo htmlspecialchars($erreur); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="post" class="space-y-6">
            <!-- Champs du formulaire avec style amélioré -->
            <div class="space-y-4">
                <div>
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="nom" name="nom" 
                           value="<?php echo htmlspecialchars($user['nom']); ?>" 
                           required 
                           class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <input type="text" id="prenom" name="prenom" 
                           value="<?php echo htmlspecialchars($user['prenom']); ?>" 
                           required 
                           class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           required 
                           class="form-input mt-1 block w-full">
                </div>

                <div>
                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                    <textarea id="adresse" name="adresse" 
                              rows="3" 
                              class="form-input mt-1 block w-full"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                </div>

                <div>
                    <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" 
                           value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>" 
                           class="form-input mt-1 block w-full">
                </div>

                <!-- Section mot de passe -->
                <div class="border-t pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Changer le mot de passe</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="motdepasse_actuel" class="block text-sm font-medium text-gray-700">
                                Mot de passe actuel
                            </label>
                            <input type="password" id="motdepasse_actuel" name="motdepasse_actuel" 
                                   class="form-input mt-1 block w-full">
                        </div>

                        <div>
                            <label for="nouveau_motdepasse" class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                            <input type="password" id="nouveau_motdepasse" name="nouveau_motdepasse" 
                                   class="form-input mt-1 block w-full">
                        </div>

                        <div>
                            <label for="confirmer_nouveau_motdepasse" class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                            <input type="password" id="confirmer_nouveau_motdepasse" name="confirmer_nouveau_motdepasse" 
                                   class="form-input mt-1 block w-full">
                        </div>
                    </div>
                </div>

                <!-- Bouton de soumission -->
                <div class="flex justify-end pt-6">
                    <button type="submit" 
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Mettre à jour le profil
                    </button>
                </div>
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

document.addEventListener('DOMContentLoaded', function() {
    const nouveauMotdepasse = document.getElementById('nouveau_motdepasse');
    const confirmerMotdepasse = document.getElementById('confirmer_nouveau_motdepasse');
    const motdepasseActuel = document.getElementById('motdepasse_actuel');

    function updateMotdepasseActuelRequired() {
        const isChangingPassword = nouveauMotdepasse.value !== '' || confirmerMotdepasse.value !== '';
        motdepasseActuel.required = isChangingPassword;
    }

    nouveauMotdepasse.addEventListener('input', updateMotdepasseActuelRequired);
    confirmerMotdepasse.addEventListener('input', updateMotdepasseActuelRequired);
});
</script>
