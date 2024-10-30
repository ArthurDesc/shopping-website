<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_header.php';
require_once dirname(__FILE__) . '/../includes/_db.php';

// Vérification de connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}



// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, $id_utilisateur)
{
    $stmt = $conn->prepare("SELECT nom, prenom, email, adresse, telephone FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $id_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Récupérer les informations de l'utilisateur
$user = getUserInfo($conn, $_SESSION['id_utilisateur']);
?>




<body class="bg-gray-50 min-h-screen">

    <main class="container mx-auto px-4 py-8">

        <!-- Contenu principal dans un conteneur centré -->
        <div class="max-w-5xl mx-auto"> <!-- Conteneur principal à 80% -->

            <!-- En-tête de la page -->
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Mon Compte</h1>
                <div class="flex space-x-4">
                    <a href="<?php echo url('pages/commandes.php'); ?>"
                        class="flex items-center px-4 py-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mes commandes
                    </a>
                    <button id="sidebar-btn-deconnexion"
                        class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1 0 12.728 0M12 3v9" />
                        </svg>
                        Déconnexion
                    </button>
                </div>
            </div>

            <!-- Bloc du formulaire avec ombre -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Informations personnelles</h2>

                <div class="space-y-6">
                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom*</label>
                            <div id="nom"
                                class="editable block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:border-blue-500 transition-colors"
                                data-type="text"
                                data-url="../ajax/update_profile.php"
                                data-name="nom"
                                data-required="true">
                                <?php echo htmlspecialchars($user['nom']); ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prénom*</label>
                            <div id="prenom"
                                class="editable block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:border-blue-500 transition-colors"
                                data-type="text"
                                data-url="../ajax/update_profile.php"
                                data-name="prenom"
                                data-required="true">
                                <?php echo htmlspecialchars($user['prenom']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email*</label>
                        <div id="email"
                            class="editable block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:border-blue-500 transition-colors"
                            data-type="email"
                            data-url="../ajax/update_profile.php"
                            data-name="email"
                            data-required="true">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <div id="telephone"
                            class="editable block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:border-blue-500 transition-colors"
                            data-type="tel"
                            data-url="../ajax/update_profile.php"
                            data-name="telephone">
                            <?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné'); ?>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                        <div id="adresse"
                            class="editable block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg hover:border-blue-500 transition-colors"
                            data-type="textarea"
                            data-url="../ajax/update_profile.php"
                            data-name="adresse">
                            <?php echo htmlspecialchars($user['adresse'] ?? 'Non renseignée'); ?>
                        </div>
                    </div>

                    <!-- Message d'information -->
                    <div class="text-sm text-gray-500 mt-4">
                        <p>* Champs obligatoires</p>
                        <p>Cliquez sur un champ pour le modifier</p>
                    </div>
                </div>

                <!-- Bouton pour changer le mot de passe -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <button type="button"
                        id="toggle_password_modal"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Changer le mot de passe
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Garder les modals existants -->
        <!-- ... Le reste du code pour les modals ... -->

        <?php include dirname(__FILE__) . '/../includes/_footer.php'; ?>
</body>

</html>