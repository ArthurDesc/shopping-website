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
                                <div class="avis-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="1em" width="1em">
                                        <g style="filter: url(#shadow)">
                                            <path fill="currentColor" d="M14.2199 21.63C13.0399 21.63 11.3699 20.8 10.0499 16.83L9.32988 14.67L7.16988 13.95C3.20988 12.63 2.37988 10.96 2.37988 9.78001C2.37988 8.61001 3.20988 6.93001 7.16988 5.60001L15.6599 2.77001C17.7799 2.06001 19.5499 2.27001 20.6399 3.35001C21.7299 4.43001 21.9399 6.21001 21.2299 8.33001L18.3999 16.82C17.0699 20.8 15.3999 21.63 14.2199 21.63ZM7.63988 7.03001C4.85988 7.96001 3.86988 9.06001 3.86988 9.78001C3.86988 10.5 4.85988 11.6 7.63988 12.52L10.1599 13.36C10.3799 13.43 10.5599 13.61 10.6299 13.83L11.4699 16.35C12.3899 19.13 13.4999 20.12 14.2199 20.12C14.9399 20.12 16.0399 19.13 16.9699 16.35L19.7999 7.86001C20.3099 6.32001 20.2199 5.06001 19.5699 4.41001C18.9199 3.76001 17.6599 3.68001 16.1299 4.19001L7.63988 7.03001Z"></path>
                                            <path fill="currentColor" d="M10.11 14.4C9.92005 14.4 9.73005 14.33 9.58005 14.18C9.29005 13.89 9.29005 13.41 9.58005 13.12L13.16 9.53C13.45 9.24 13.93 9.24 14.22 9.53C14.51 9.82 14.51 10.3 14.22 10.59L10.64 14.18C10.5 14.33 10.3 14.4 10.11 14.4Z"></path>
                                        </g>
                                        <defs>
                                            <filter id="shadow">
                                                <fedropshadow flood-opacity="0.5" stdDeviation="0.6" dy="1" dx="0"></fedropshadow>
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <p>
                                    <span style="--i:0">E</span>
                                    <span style="--i:1">n</span>
                                    <span style="--i:2">v</span>
                                    <span style="--i:3">o</span>
                                    <span style="--i:4">y</span>
                                    <span style="--i:5">e</span>
                                    <span style="--i:6">r</span>
                                </p>
                            </div>
                            <div class="avis-state avis-state--sent">
                                <div class="avis-icon">
                                    <svg width="1em" height="1em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g style="filter: url(#shadow)">
                                            <path d="M12 22.75C6.07 22.75 1.25 17.93 1.25 12C1.25 6.07 6.07 1.25 12 1.25C17.93 1.25 22.75 6.07 22.75 12C22.75 17.93 17.93 22.75 12 22.75ZM12 2.75C6.9 2.75 2.75 6.9 2.75 12C2.75 17.1 6.9 21.25 12 21.25C17.1 21.25 21.25 17.1 21.25 12C21.25 6.9 17.1 2.75 12 2.75Z" fill="currentColor"></path>
                                            <path d="M10.5795 15.5801C10.3795 15.5801 10.1895 15.5001 10.0495 15.3601L7.21945 12.5301C6.92945 12.2401 6.92945 11.7601 7.21945 11.4701C7.50945 11.1801 7.98945 11.1801 8.27945 11.4701L10.5795 13.7701L15.7195 8.6301C16.0095 8.3401 16.4895 8.3401 16.7795 8.6301C17.0695 8.9201 17.0695 9.4001 16.7795 9.6901L11.1095 15.3601C10.9695 15.5001 10.7795 15.5801 10.5795 15.5801Z" fill="currentColor"></path>
                                        </g>
                                    </svg>
                                </div>
                                <p>
                                    <span style="--i:5">E</span>
                                    <span style="--i:6">n</span>
                                    <span style="--i:7">v</span>
                                    <span style="--i:8">o</span>
                                    <span style="--i:8">y</span>
                                    <span style="--i:8">é</span>
                                </p>
                            </div>
                        </div>
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
