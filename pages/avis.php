<?php
ob_start();
session_start();
require_once '../includes/_db.php';
require_once '../includes/_header.php';

// Vérification de la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    $_SESSION['error_message'] = "Vous devez être connecté pour voir les avis.";
    header("Location: connexion.php");
    exit();
}

// Récupération de l'ID du produit
$id_produit = isset($_GET['id_produit']) ? intval($_GET['id_produit']) : 0;

// Récupération de TOUS les avis (sans LIMIT)
$sql = "SELECT a.*, u.nom as nom_utilisateur 
        FROM avis a 
        LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
        WHERE a.id_produit = ? 
        ORDER BY a.date_creation DESC"; // Supprimez le LIMIT 5

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_produit);
$stmt->execute();
$avis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Compter le nombre total d'avis
$sql_count = "SELECT COUNT(*) as total FROM avis WHERE id_produit = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $id_produit);
$stmt_count->execute();
$total_avis = $stmt_count->get_result()->fetch_assoc()['total'];

// Si c'est une requête AJAX pour charger tous les avis
if (isset($_GET['action']) && $_GET['action'] === 'load_all') {
    $sql = "SELECT a.*, u.nom as nom_utilisateur 
            FROM avis a 
            LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
            WHERE a.id_produit = ? 
            ORDER BY a.date_creation DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produit);
    $stmt->execute();
    $all_avis = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'avis' => $all_avis]);
    exit();
}
?>

<div id="toast" class="fixed right-4 top-[70px] bg-green-500 text-white py-2 px-4 rounded shadow-lg transition-opacity duration-300 opacity-0 z-50">
</div>

<div class="container mx-auto px-4 py-8">
    <!-- Bouton retour -->
    <div class="mb-6">
        <a href="./detail.php?id=<?php echo $id_produit; ?>&tab=tab2" 
           class="inline-flex items-center text-blue-500 hover:text-blue-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au produit
        </a>
    </div>

    <h2 class="text-2xl font-bold mb-6">Avis clients (<?php echo $total_avis; ?>)</h2>

    <!-- Liste des 5 premiers avis -->
    <div id="avis-container">
        <?php if (empty($avis)): ?>
            <p class="text-gray-500">Aucun avis pour ce produit.</p>
        <?php else: ?>
            <?php foreach ($avis as $review): ?>
                <div class="mb-4 p-4 bg-white shadow-lg rounded" data-avis-id="<?php echo $review['id_avis']; ?>">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="font-semibold"><?php echo htmlspecialchars($review['nom_utilisateur'] ?? 'Anonyme'); ?></div>
                            <div class="flex items-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg class="w-5 h-5 <?php echo $i <= $review['note'] ? 'text-yellow-400' : 'text-gray-300'; ?>" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-sm text-gray-500">
                                <?php 
                                    // Formatage de la date avec l'heure
                                    echo date('d/m/Y à H:i', strtotime($review['date_creation'])); 
                                ?>
                            </div>
                            <?php if (isset($_SESSION['id_utilisateur']) && $_SESSION['id_utilisateur'] == $review['id_utilisateur']): ?>
                                <div class="flex gap-2">
                                    <button onclick="modifierAvis(<?php echo $review['id_avis']; ?>)" 
                                            class="text-blue-600 hover:text-blue-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             fill="none" 
                                             viewBox="0 0 24 24" 
                                             stroke-width="1.5" 
                                             stroke="currentColor" 
                                             class="w-5 h-5">
                                            <path stroke-linecap="round" 
                                                  stroke-linejoin="round" 
                                                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </button>
                                    <button onclick="supprimerAvis(<?php echo $review['id_avis']; ?>)" 
                                            class="text-red-600 hover:text-red-800">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                             fill="none" 
                                             viewBox="0 0 24 24" 
                                             stroke-width="1.5" 
                                             stroke="currentColor" 
                                             class="w-5 h-5">
                                            <path stroke-linecap="round" 
                                                  stroke-linejoin="round" 
                                                  d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="mt-2 text-gray-700"><?php echo nl2br(htmlspecialchars($review['commentaire'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
window.supprimerAvis = function(idAvis) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) {
        fetch('/shopping-website/ajax/delete_avis.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id_avis: idAvis })
        })
        .then(response => response.json())
        .then(data => {
            // Supprimer visuellement l'avis
            const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
            if (avisElement) {
                avisElement.remove();
                
                // Mettre à jour le compteur dans le titre
                const titleElement = document.querySelector('h2');
                const currentCount = parseInt(titleElement.textContent.match(/\((\d+)\)/)[1]);
                titleElement.textContent = `Avis clients (${currentCount - 1})`;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }
};

window.modifierAvis = function(idAvis) {
    const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
    const commentaireElement = avisElement.querySelector('p');
    const commentaire = commentaireElement.textContent;
    const note = avisElement.querySelectorAll('.text-yellow-400').length;

    // Créer le formulaire de modification
    const form = document.createElement('div');
    form.innerHTML = `
        <div class="mt-4">
            <div class="flex gap-2 mb-2">
                ${[1,2,3,4,5].map(n => `
                    <input type="radio" name="rating" value="${n}" id="rating${n}" ${n === note ? 'checked' : ''} class="hidden peer">
                    <label for="rating${n}" class="cursor-pointer">
                        <svg class="w-6 h-6 ${n <= note ? 'text-yellow-400' : 'text-gray-300'}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </label>
                `).join('')}
            </div>
            <textarea class="w-full p-2 border rounded">${commentaire}</textarea>
            <div class="flex justify-end gap-2 mt-2">
                <button onclick="annulerModification(${idAvis})" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Annuler
                </button>
                <button onclick="sauvegarderModification(${idAvis})" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Sauvegarder
                </button>
            </div>
        </div>
    `;
    
    commentaireElement.style.display = 'none';
    commentaireElement.after(form);
};

window.annulerModification = function(idAvis) {
    const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
    const form = avisElement.querySelector('div > div');
    const commentaireElement = avisElement.querySelector('p');
    
    form.remove();
    commentaireElement.style.display = 'block';
};

window.sauvegarderModification = function(idAvis) {
    const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
    const newCommentaire = avisElement.querySelector('textarea').value;
    const newNote = avisElement.querySelector('input[name="rating"]:checked').value;

    fetch('/shopping-website/ajax/update_avis.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id_avis: idAvis,
            commentaire: newCommentaire,
            note: newNote
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Recharger la page pour voir les modifications
        } else {
            alert('Erreur lors de la modification : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue lors de la modification');
    });
};
</script>

<?php include '../includes/_scripts.php'; ?>

<?php require_once '../includes/_footer.php'; ?>
