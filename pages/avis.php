<?php
ob_start();
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

$id_produit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id_produit) {
    header('Location: index.php');
    exit();
}
?>
<main>
    <div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
    </div>

    <!-- Ajouter l'input caché pour l'ID utilisateur -->
    <input type="hidden" name="id_utilisateur" value="<?php echo isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : ''; ?>">

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
    </div>
</main>

<!-- Initialisation de RateYo -->
<script>
document.addEventListener("DOMContentLoaded", function() {
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
const CURRENT_USER_ID = <?php echo isset($_SESSION['id_utilisateur']) ? $_SESSION['id_utilisateur'] : 'null'; ?>;

document.addEventListener('DOMContentLoaded', async function() {
    const productId = <?php echo $id_produit; ?>;
    
    try {
        const response = await fetch(`/shopping-website/ajax/get_avis.php?id_produit=${productId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.success) {
            // Mettre à jour le compteur
            document.getElementById('total-avis').textContent = data.avis.length;
            
            // Afficher les avis
            displayAvis(data.avis);
            
            // Réinitialiser RateYo
            $(".rateyo-readonly").each(function() {
                $(this).rateYo({
                    rating: $(this).data("rating"),
                    readOnly: true,
                    starWidth: "20px"
                });
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des avis:', error);
    }
});
</script>

<?php include '../includes/_scripts.php'; ?>
<?php require_once '../includes/_footer.php'; ?>

<?php include '../includes/_scripts.php'; ?>
<?php require_once '../includes/_footer.php'; ?>
