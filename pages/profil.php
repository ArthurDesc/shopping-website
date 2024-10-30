<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_header.php';
require_once dirname(__FILE__) . '/../includes/_db.php';

// Vérification de connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: connexion.php');
    exit();
}

// Au début du fichier, après session_start()
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
            <!-- Formulaire de profil -->
            <form id="profileForm" class="bg-white/90 backdrop-blur-md shadow-xl rounded-lg p-6">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Informations personnelles</h2>
                
                <div class="space-y-6">
                    <!-- Nom et Prénom -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nom" class="block text-sm font-medium text-gray-700">Nom*</label>
                            <input type="text" id="nom" name="nom" 
                                   value="<?php echo htmlspecialchars($user['nom']); ?>" 
                                   required
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="prenom" class="block text-sm font-medium text-gray-700">Prénom*</label>
                            <input type="text" id="prenom" name="prenom" 
                                   value="<?php echo htmlspecialchars($user['prenom']); ?>" 
                                   required
                                   class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email*</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" 
                               required
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="telephone" class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <input type="tel" id="telephone" name="telephone" 
                               value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>" 
                               placeholder="Ex: 06 12 34 56 78"
                               class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Adresse -->
                    <div>
                        <label for="adresse" class="block text-sm font-medium text-gray-700">Adresse</label>
                        <textarea id="adresse" name="adresse" rows="3" 
                                  class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Votre adresse complète"><?php echo htmlspecialchars($user['adresse'] ?? ''); ?></textarea>
                    </div>

                    <!-- Message d'information -->
                    <div class="text-sm text-gray-600">
                        <p>* Champs obligatoires</p>
                    </div>

                    <!-- Bouton de soumission -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5">
                            Mettre à jour le profil
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

<?php include dirname(__FILE__) . '/../includes/_footer.php'; ?>
  
</body>
</html>

