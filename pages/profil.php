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
function getUserInfo($conn, $id_utilisateur) {
    $stmt = $conn->prepare("SELECT nom, prenom, email, adresse, telephone FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $id_utilisateur);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Récupérer les informations de l'utilisateur
$user = getUserInfo($conn, $_SESSION['id_utilisateur']);
?>


  

<body>
    <div class="gradient-container fixed inset-0 -z-10">
        <div class="gradient"></div>
    </div>

    <!-- Sidebar -->
    <div class="hidden sm:flex sidebar bg-white shadow-md fixed h-full left-0 top-0 z-20 flex-col w-64">
        <!-- ... votre code sidebar existant ... -->
    </div>

    <!-- Contenu principal -->
    <main class="sm:ml-64 p-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6">
                <!-- Déplacer l'input dans un form ou une div avec un ID -->
         
                
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Informations personnelles</h2>
                
                <div class="space-y-6">
                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nom*</label>
                            <div id="nom" 
                                 class="editable mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg"
                                 data-type="text"
                                 data-url="../ajax/update_profile.php"
                                 data-name="nom"
                                 data-required="true">
                                <?php echo htmlspecialchars($user['nom']); ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Prénom*</label>
                            <div id="prenom" 
                                 class="editable mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg"
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
                        <label class="block text-sm font-medium text-gray-700">Email*</label>
                        <div id="email" 
                             class="editable mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg"
                             data-type="email"
                             data-url="../ajax/update_profile.php"
                             data-name="email"
                             data-required="true">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <div id="telephone" 
                             class="editable mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg"
                             data-type="tel"
                             data-url="../ajax/update_profile.php"
                             data-name="telephone">
                            <?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné'); ?>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                        <div id="adresse" 
                             class="editable mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg"
                             data-type="textarea"
                             data-url="../ajax/update_profile.php"
                             data-name="adresse">
                            <?php echo htmlspecialchars($user['adresse'] ?? 'Non renseignée'); ?>
                        </div>
                    </div>

                    <!-- Message d'information -->
                    <div class="text-sm text-gray-600">
                        <p>* Champs obligatoires</p>
                        <p>Cliquez sur un champ pour le modifier</p>
                    </div>
                </div>

                <!-- Bouton pour ouvrir le modal -->
                <div class="mt-8">
                    <button type="button" 
                            id="toggle_password_modal" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                        Changer le mot de passe
                    </button>
                </div>

                <!-- Modal de changement de mot de passe -->
                <div id="password_modal" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0 hidden">
                    <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold mb-4">Modification du mot de passe</h2>
                            
                            <!-- Message d'erreur caché par défaut -->
                            <div id="password-error" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mot de passe actuel</label>
                                    <input type="password" 
                                           id="current_password" 
                                           class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           name="current_password">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nouveau mot de passe</label>
                                    <input type="password" 
                                           id="new_password" 
                                           class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           name="new_password">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Confirmer le nouveau mot de passe</label>
                                    <input type="password" 
                                           id="confirm_password" 
                                           class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           name="confirm_password">
                                </div>

                                <div class="flex flex-col-reverse sm:flex-row sm:space-x-4 mt-6">
                                    <button type="button" 
                                            id="cancel_password" 
                                            class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                                        Annuler
                                    </button>
                                    <button type="button" 
                                            id="change_password" 
                                            class="button-shadow w-full sm:flex-1 px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Modifier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php include dirname(__FILE__) . '/../includes/_footer.php'; ?>
  
</body>
</html>

