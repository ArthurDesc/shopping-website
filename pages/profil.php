<?php
// Vérifier qu'il n'y a aucune sortie avant
ob_start(); // Démarre la mise en tampon de sortie

// Démarrer la session avant tout
session_start();

// Vérifier si l'utilisateur est connecté AVANT d'inclure les autres fichiers
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: ../index.php');
    ob_end_clean(); // Nettoie le tampon de sortie
    exit();
}

// Inclure les fichiers nécessaires APRÈS la vérification
require_once dirname(__FILE__) . '/../includes/_header.php';
require_once dirname(__FILE__) . '/../includes/_db.php';

$erreurs = [];
$success = false;

// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, $id_utilisateur)
{
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
<div class="min-h-screen p-4 relative z-10">
    <!-- En-tête avec boutons -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center">

                Mon Compte
            </h2>
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
                <a href="deconnexion.php" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm">
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
    </div>

    <!-- Conteneur des 3 colonnes -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Colonne 1: Informations personnelles -->
        <div class="bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6">
            <h3 class="text-lg font-medium  mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                Informations personnelles
            </h3>
            <form id="personal-form" class="space-y-6" data-form-type="personal">
                <div class="space-y-2">
                    <label for="nom" class="block text-sm font-medium text-gray-700">Nom</label>
                    <div class="relative">
                        <input type="text" id="nom" name="nom"
                            value="<?php echo htmlspecialchars($user['nom']); ?>"
                            required
                            disabled
                            class="form-input mt-1 block w-full pr-10">
                        <button type="button" onclick="toggleEdit('nom')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom</label>
                    <div class="relative">
                        <input type="text" id="prenom" name="prenom"
                            value="<?php echo htmlspecialchars($user['prenom']); ?>"
                            required
                            disabled
                            class="form-input mt-1 block w-full pr-10">
                        <button type="button" onclick="toggleEdit('prenom')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <input type="email" id="email" name="email"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            required
                            disabled
                            class="form-input mt-1 block w-full pr-10">
                        <button type="button" onclick="toggleEdit('email')" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Colonne 2: Coordonnées -->
        <div class="bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6">
            <h3 class="text-lg font-medium  mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                Coordonnées
            </h3>
            <form id="contact-form" class="space-y-6" data-form-type="contact">
                <div class="space-y-2">
                    <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse postale</label>
                    <div class="relative">
                        <textarea id="adresse" name="adresse"
                            rows="3"
                            <?php echo !empty($user['adresse']) ? 'disabled' : ''; ?>
                            class="form-input mt-1 block w-full pr-10"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                        <?php if (!empty($user['adresse'])): ?>
                            <button type="button" onclick="toggleEdit('adresse')" class="absolute right-2 top-4 text-gray-500 hover:text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="telephone" class="block text-sm font-medium text-gray-700">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone"
                        value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>"
                        class="form-input mt-1 block w-full">
                    <input type="hidden" id="telephone_complet" name="telephone_complet">
                </div>
            </form>
        </div>

        <!-- Colonne 3: Sécurité -->
        <div class="bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6">
            <h3 class="text-lg font-medium mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                Sécurité
            </h3>
            <form id="security-form" class="space-y-6" data-form-type="security">
                <div class="space-y-2">
                    <label for="motdepasse_actuel" class="block text-sm font-medium text-gray-700">
                        Mot de passe actuel
                    </label>
                    <div class="relative">
                        <input type="password" 
                               id="motdepasse_actuel" 
                               name="motdepasse_actuel"
                               disabled
                               value="••••••••"
                               class="form-input mt-1 block w-full pr-10 bg-gray-100">
                        <button type="button" 
                                onclick="toggleEdit('motdepasse_actuel')" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="nouveau_motdepasse" class="block text-sm font-medium text-gray-700 hidden password-field">
                        Nouveau mot de passe
                    </label>
                    <input type="password" id="nouveau_motdepasse" name="nouveau_motdepasse"
                        class="form-input mt-1 block w-full hidden password-field">
                </div>

                <div class="space-y-2">
                    <label for="confirmer_nouveau_motdepasse" class="block text-sm font-medium text-gray-700 hidden password-field">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" id="confirmer_nouveau_motdepasse" name="confirmer_nouveau_motdepasse"
                        class="form-input mt-1 block w-full hidden password-field">
                </div>
            </form>
        </div>
    </div>

    <!-- Bouton de validation (initialement caché) -->
    <div id="submit-button" class="max-w-7xl mx-auto mt-12 flex justify-end hidden">
        <button type="button"
            onclick="submitForms()"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Valider les modifications
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation du téléphone international
        const input = document.querySelector("#telephone");
        const iti = window.intlTelInput(input, {
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js",
            initialCountry: "fr",
            preferredCountries: ["fr", "gb", "us"],
            separateDialCode: true,
            formatOnDisplay: true,
            autoPlaceholder: "polite"
        });

        // Gestion du bouton de validation
        const forms = document.querySelectorAll('form');
        const submitButton = document.getElementById('submit-button');
        let hasChanges = false;

        // Fonction pour vérifier les changements
        function checkForChanges(e) {
            hasChanges = true;
            submitButton.classList.remove('hidden');
        }

        // Ajouter les écouteurs d'événements pour tous les champs
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', checkForChanges);
            });
        });

        // Validation du numéro de téléphone
        input.addEventListener('blur', function() {
            if (input.value.trim()) {
                if (!iti.isValidNumber()) {
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            }
        });

        // Fonction pour soumettre tous les formulaires
        window.submitForms = async function() {
            const motdepasseActuel = document.getElementById('motdepasse_actuel');
            
            if (motdepasseActuel && !motdepasseActuel.disabled && motdepasseActuel.value) {
                try {
                    const formData = new FormData();
                    formData.append('motdepasse_actuel', motdepasseActuel.value);
                    
                    const response = await fetch('../ajax/verify_password.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Afficher les champs de nouveau mot de passe
                        document.querySelectorAll('.password-field').forEach(el => {
                            el.classList.remove('hidden');
                        });
                        return; // Ne pas soumettre le formulaire complet
                    } else {
                        alert('Mot de passe incorrect');
                        return;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                    return;
                }
            }
            
            const formData = new FormData();
            
            forms.forEach(form => {
                const formType = form.dataset.formType;
                const inputs = form.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.value) {
                        if (input.id === 'telephone') {
                            formData.append(`${formType}_${input.name}`, iti.getNumber());
                        } else {
                            formData.append(`${formType}_${input.name}`, input.value);
                        }
                    }
                });
            });

            try {
                const response = await fetch('../ajax/update_profile.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Une erreur est survenue');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Une erreur est survenue');
            }
        };

        // Fonction pour capitaliser la première lettre
        function capitalizeFirstLetter(input) {
            input.value = input.value.charAt(0).toUpperCase() + input.value.slice(1);
        }

        // Appliquer la capitalisation aux champs nom et prnom
        const nomInput = document.getElementById('nom');
        const prenomInput = document.getElementById('prenom');

        nomInput.addEventListener('blur', function() {
            capitalizeFirstLetter(this);
        });

        prenomInput.addEventListener('blur', function() {
            capitalizeFirstLetter(this);
        });

        // Fonction pour activer/désactiver l'édition d'un champ
        window.toggleEdit = function(fieldId) {
            const input = document.getElementById(fieldId);
            const isDisabled = input.disabled;
            
            input.disabled = !isDisabled;
            
            if (!isDisabled) {
                input.classList.add('bg-gray-50');
                if (fieldId === 'motdepasse_actuel') {
                    input.value = '';
                    input.placeholder = 'Entrez votre mot de passe actuel';
                    input.focus();
                } else {
                    requestAnimationFrame(() => {
                        input.focus();
                        const currentValue = input.value;
                        input.value = '';
                        input.value = currentValue;
                    });
                }
            } else {
                input.classList.remove('bg-gray-50');
                if (fieldId === 'motdepasse_actuel') {
                    input.value = '••••••••';
                    // Cacher les champs de nouveau mot de passe
                    document.querySelectorAll('.password-field').forEach(el => {
                        el.classList.add('hidden');
                    });
                    // Vider les champs de mot de passe
                    document.getElementById('nouveau_motdepasse').value = '';
                    document.getElementById('confirmer_nouveau_motdepasse').value = '';
                }
            }
        };
    });
</script>