<?php
ob_start();
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

?>
<main>

    <div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Bouton retour -->
        <div class="mb-6">
            <a href="./detail.php?id=<?php echo $id_produit; ?>&tab=tab2"
                class="inline-flex items-center text-blue-500 hover:text-blue-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour au produit
            </a>
        </div>

        <h2 class="text-2xl font-bold mb-6">Avis clients (<span id="total-avis">0</span>)</h2>

        <div id="comments-list">
            <!-- Les avis seront chargés dynamiquement ici -->
        </div>

        <!-- Ajout du formulaire de commentaire -->
        <div class="mt-8 mb-4">
            <h3 class="text-xl font-semibold mb-4">Ajouter un commentaire</h3>
            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                <form id="comment-form" class="space-y-4">
                    <!-- Champs cachés -->
                    <input type="hidden" name="id_produit" value="<?php echo htmlspecialchars($id_produit); ?>">
                    <input type="hidden" name="id_utilisateur" value="<?php echo htmlspecialchars($_SESSION['id_utilisateur']); ?>">
                    <input type="hidden" name="note" id="rating-input" value="0">

                    <!-- Zone de commentaire -->
                    <div class="flex flex-col gap-4">
                        <div class="w-full">
                            <label for="commentaire" class="block text-sm font-medium text-gray-700 mb-1">Votre commentaire</label>
                            <textarea
                                id="commentaire"
                                name="commentaire"
                                rows="4"
                                required
                                minlength="10"
                                class="w-full h-25 px-3 py-2 text-gray-700 border rounded-lg focus:outline-none focus:border-blue-500"
                                placeholder="Écrivez votre commentaire ici (minimum 10 caractères)..."></textarea>
                        </div>

                        <!-- Système de notation avec RateYo -->
                        <div class="w-full flex flex-col items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-700">Note* :</span>
                                <div id="rateYo"></div>
                            </div>
                        </div>

                        <!-- Bouton Envoyer -->
                        <button type="submit" class="avis-button w-1/2">
                            <div class="avis-outline"></div>
                            <div class="avis-state avis-state--default">
                                <!-- ... contenu du bouton ... -->
                            </div>
                        </button>
                    </div>

                    <p class="text-sm text-gray-500 mt-2">* Champs obligatoires</p>
                </form>
            <?php else: ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Information :</strong>
                    <span class="block sm:inline"> Vous devez être connecté pour laisser un avis.</span>
                    <a href="/shopping-website/pages/connexion.php" class="underline">Se connecter</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<!-- Initialisation de RateYo -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Initialiser RateYo pour la notation
    $("#rateYo").rateYo({
        rating: 0,
        fullStar: true,
        starWidth: "20px",
        onChange: function (rating, rateYoInstance) {
            $("#rating-input").val(rating);
        }
    });

    // Initialiser tous les RateYo en lecture seule
    $(".rateyo-readonly").each(function() {
        $(this).rateYo({
            rating: $(this).data("rating"),
            readOnly: true,
            starWidth: "20px"
        });
    });
});
</script>

<!-- Inclure avis.js -->
<script src="../assets/js/avis.js"></script>

<!-- Juste avant la fermeture de body -->
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (!productId) {
        console.error("ID du produit non trouvé");
        return;
    }

    // Utiliser la fonction getAvis() existante
    const data = await getAvis(productId);
    if (data.success) {
        // Mettre à jour le compteur
        document.getElementById('total-avis').textContent = data.avis.length;
        
        // Afficher les avis avec la fonction existante
        displayAvis(data.avis);
    }
});
</script>

<?php include '../includes/_scripts.php'; ?>

<?php require_once '../includes/_footer.php'; ?>
