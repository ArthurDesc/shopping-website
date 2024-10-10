<?php include '../includes/session.php'; ?>
<?php include '../includes/_db.php'; ?>
<?php require_once '../classe/produit.php'; ?>
<?php require_once '../classe/ArticleManager.php';

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');  // Ajustez selon le nom de votre dossier de projet
  }?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_article') {
    header('Content-Type: application/json');
    
    // Votre logique de traitement ici
    
    echo json_encode(['success' => true, 'message' => 'Article ajouté avec succès']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Alata&display=swap" rel="stylesheet">
    <title>BackOffice</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
</head>

<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="sidebar bg-white shadow-md">
        <div class="flex justify-between items-center sm:flex-col h-full px-4 sm:px-0">
            <a id="articles-link" class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16 hover:bg-gray-100" href="#" onclick="loadContent('articles')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>


                <span class="text-[10px] sm:text-xs">Articles</span>
            </a>
            <a class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16" href="../index.php">
                <img src="<?php echo BASE_URL; ?>assets/images/logoF.png" alt="Logo F" class="w-6 h-6 sm:w-8 sm:h-8 object-contain mb-1 sm:mb-0">
                <span class="text-[10px] sm:text-xs">Accueil</span>
            </a>
            <a id="categories-link" class="flex-1 sm:flex-auto flex flex-col items-center justify-center p-2 sm:p-4 sm:w-16 sm:h-16 hover:bg-gray-100" href="#" onclick="loadContent('categories')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6 mb-1 sm:mb-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                </svg>
                <span class="text-[10px] sm:text-xs">Catégories</span>
            </a>
        </div>
    </div>

    <!-- Contenu principal -->
    <main class="main-content p-4">
        <div id="content-area" class="max-w-5xl mx-auto">
            <!-- Le contenu sera chargé ici dynamiquement -->
        </div>
    </main>
    <script>
               const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>assets/js/backoffice/tabManager.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/backoffice/articleManager.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/backoffice/categoryManager.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/backoffice/accordion.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/backoffice/adminMain.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé');
        const form = document.querySelector('form');
        if (form) {
            console.log('Formulaire trouvé');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Formulaire soumis');
                const formData = new FormData(form);
                formData.append('action', 'add_article');
                console.log('FormData créé:', Object.fromEntries(formData));

                fetch('backofficeV2.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    console.log('Réponse reçue:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);
                    if (data.success) {
                        form.reset();
                    } else {
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            });
        } else {
            console.log('Formulaire non trouvé');
        }
    });
    
    </script>
</body>

</html>