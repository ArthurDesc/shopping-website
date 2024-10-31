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
    <!-- En-tête avec les boutons de navigation -->
    <div class="fixed top-4 w-full px-4 z-50">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <!-- Bouton retour modifié -->
            <a href="<?php echo url('index.php'); ?>" class="button-retour flex items-center gap-2 transition-all duration-300 -ml-2">
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 74 74" height="34" width="34">
                        <circle class="stroke-current" stroke-width="3" stroke="currentColor" r="35.5" cy="37" cx="37"></circle>
                        <path class="fill-current" fill="currentColor" d="M49 35.5C49.8284 35.5 50.5 36.1716 50.5 37C50.5 37.8284 49.8284 38.5 49 38.5V35.5ZM24.9393 38.0607C24.3536 37.4749 24.3536 36.5251 24.9393 35.9393L34.4853 26.3934C35.0711 25.8076 36.0208 25.8076 36.6066 26.3934C37.1924 26.9792 37.1924 27.9289 36.6066 28.5147L28.1213 37L36.6066 45.4853C37.1924 46.0711 37.1924 47.0208 36.6066 47.6066C36.0208 48.1924 35.0711 48.1924 34.4853 47.6066L24.9393 38.0607ZM49 38.5L26 38.5V35.5L49 35.5V38.5Z"></path>
                    </svg>
                    <img src="<?php echo url('assets/images/logoBleu.png'); ?>" alt="Fitmode" class="logo-img" height="200">
                </button>
            </a>

            <!-- Bouton déconnexion -->
            <button class="Btn" id="btn-deconnexion">
                <div class="sign">
                    <svg viewBox="0 0 512 512">
                        <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                    </svg>
                </div>
                <div class="text">Déconnexion</div>
            </button>
        </div>
    </div>

    <main class="container mx-auto px-4 py-8">

        <!-- Contenu principal dans un conteneur centré -->
        <div class="max-w-5xl mx-auto"> <!-- Conteneur principal à 80% -->

            <!-- En-tête avec les tabs -->
            <div>
                <!-- En-tête avec titre et bouton déconnexion -->
                <div class="flex justify-between items-center mb-3">
                    <h1 class="text-3xl font-bold text-white">Mon Compte</h1>

                </div>

                <!-- Navigation des tabs et contenu dans un seul conteneur -->
                <div class="bg-white shadow-lg shadow-blue-100/50 rounded-t-xl">
                    <nav class="flex space-x-8 px-6 border-b border-gray-200" aria-label="Tabs">
                        <button class="tab-btn active whitespace-nowrap py-4 px-1 border-b-2 font-medium text-blue-600 border-blue-600 hover:text-blue-700 flex items-center gap-2"
                            data-tab="profile">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            Informations personnelles
                        </button>
                        <button class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 flex items-center gap-2"
                            data-tab="orders">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                            Historique des commandes
                        </button>
                        <button class="tab-btn whitespace-nowrap py-4 px-1 border-b-2 font-medium text-gray-500 border-transparent hover:text-gray-700 hover:border-gray-300 flex items-center gap-2"
                            data-tab="security">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>

                            Sécurité
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Contenu des tabs -->
            <div class="tab-content bg-white rounded-b-xl shadow-lg">
                <!-- Tab Profil -->
                <div id="profile" class="tab-pane active p-8">
                    <!-- Contenu actuel du profil -->
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
                </div>

                <!-- Tab Commandes -->
                <div id="orders" class="tab-pane hidden p-8">
                    <?php
                    $is_included_in_profile = true;
                    include 'commandes.php';
                    ?>
                </div>

                <!-- Tab Sécurité -->
                <div id="security" class="tab-pane hidden p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">Sécurité du compte</h2>
                    <div class="space-y-6">
                        <!-- Changement de mot de passe -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Mot de passe</h3>
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
            </div>
        </div>
    </main>



    <!-- Modal changement mot de passe -->
    <div id="password_modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Changer le mot de passe</h3>

            <!-- Message d'erreur -->
            <div id="password-error" class="hidden mb-4 p-3 bg-red-100 text-red-700 rounded-lg"></div>

            <!-- Formulaire -->
            <div class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Mot de passe actuel
                    </label>
                    <input type="password"
                        id="current_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Entrez votre mot de passe actuel">
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Nouveau mot de passe
                    </label>
                    <input type="password"
                        id="new_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Entrez votre nouveau mot de passe">
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input type="password"
                        id="confirm_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Confirmez votre nouveau mot de passe">
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-3 mt-6">
                <button id="cancel_password"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button id="change_password"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Changer le mot de passe
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de déconnexion -->
    <div id="modal-deconnexion" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0 hidden">
        <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Confirmation de déconnexion</h2>
                <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir vous déconnecter ?</p>
                <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                    <button id="cancel-deconnexion" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                        Annuler
                    </button>
                    <a href="<?php echo url('pages/deconnexion.php'); ?>" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-center">
                        Se déconnecter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour la gestion du modal de déconnexion -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalDeconnexion = document.getElementById('modal-deconnexion');
            const btnDeconnexion = document.getElementById('btn-deconnexion');
            const btnCancelDeconnexion = document.getElementById('cancel-deconnexion');

            if (btnDeconnexion && modalDeconnexion && btnCancelDeconnexion) {
                // Ouvrir le modal
                btnDeconnexion.addEventListener('click', function(e) {
                    e.preventDefault();
                    modalDeconnexion.classList.remove('hidden');
                });

                // Fermer le modal
                btnCancelDeconnexion.addEventListener('click', function() {
                    modalDeconnexion.classList.add('hidden');
                });

                // Fermer en cliquant en dehors
                modalDeconnexion.addEventListener('click', function(e) {
                    if (e.target === modalDeconnexion) {
                        modalDeconnexion.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js" defer></script>
    <script src="<?php echo url('assets/js/scripts.js'); ?>" defer></script>
    <script src="<?php echo url('assets/js/navbar.js'); ?>" defer></script>
    <script src="<?php echo url('assets/js/header.js'); ?>" defer></script>
    <script src="<?php echo url('assets/js/autocomplete.js'); ?>" defer></script>
    <script src="<?php echo url('assets/js/toast.js'); ?>" defer></script>
    <script src="https://kit.fontawesome.com/5ea815c1d0.js" crossorigin="anonymous"></script>

    <?php if (strpos($_SERVER['REQUEST_URI'], 'profil.php') !== false): ?>
        <script src="<?php echo BASE_URL; ?>assets/js/profil.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php endif; ?>

</body>

</html>