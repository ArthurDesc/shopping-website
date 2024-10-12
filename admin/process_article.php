<?php
// Désactiver l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fonction pour gérer les erreurs
function handleError($errno, $errstr, $errfile, $errline) {
    global $output;
    $output = [
        'success' => false,
        'message' => "Erreur PHP: $errstr dans $errfile à la ligne $errline"
    ];
    echo json_encode($output);
    exit;
}

// Définir le gestionnaire d'erreurs
set_error_handler("handleError");

try {
    require_once '../includes/_db.php';
    require_once '../classe/ArticleManager.php';

    error_log(print_r($_POST, true)); // Au début du fichier
    error_log(print_r($_FILES, true)); // Pour vérifier les fichiers uploadés

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_article') {
        $errors = [];

        // Validation du titre
        $titre = trim($_POST['titre'] ?? '');
        if (empty($titre) || strlen($titre) > 255) {
            $errors[] = "Le titre est requis et ne doit pas dépasser 255 caractères.";
        }

        // Validation de la description
        $description = trim($_POST['description'] ?? '');
        if (empty($description) || strlen($description) > 1000) {
            $errors[] = "La description est requise et ne doit pas dépasser 1000 caractères.";
        }

        // Validation du prix
        $prix = filter_var($_POST['prix'] ?? '', FILTER_VALIDATE_FLOAT);
        if ($prix === false || $prix < 0 || $prix > 999999.99) {
            $errors[] = "Le prix doit être un nombre valide entre 0 et 999999.99.";
        }

        // Validation du stock
        $stock = filter_var($_POST['stock'] ?? '', FILTER_VALIDATE_INT);
        if ($stock === false || $stock < 0 || $stock > 999999) {
            $errors[] = "Le stock doit être un nombre entier valide entre 0 et 999999.";
        }

        // Validation de l'image (si nécessaire)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "jfif" => "image/jfif", "avif" => "image/avif", "webp" => "image/webp");
            $filename = $_FILES["image"]["name"];
            $filetype = $_FILES["image"]["type"];
            $filesize = $_FILES["image"]["size"];

            // Vérifiez l'extension du fichier
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) {
                $errors[] = "Veuillez sélectionner un format de fichier valide.";
            }

            // Vérifiez la taille du fichier - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) {
                $errors[] = "La taille du fichier dépasse la limite autorisée.";
            }
        }

        // Traitement du nom de l'image
        $imageName = $_POST['image_name'] ?? '';
        if (empty($imageName)) {
            $errors[] = "Le nom de l'image est requis.";
        }

        // Si aucune erreur, procédez à l'ajout de l'article
        if (empty($errors)) {
            $articleManager = new ArticleManager($conn);

            // Récupérer et valider les données du formulaire
            $nom = $_POST['titre'] ?? '';
            $description = $_POST['description'] ?? '';
            $prix = floatval($_POST['prix'] ?? 0);
            $stock = intval($_POST['stock'] ?? 0);
            $taille = $_POST['taille'] ?? '';
            $marque = $_POST['marque'] ?? '';
            $collection = $_POST['collection'] ?? '';
            $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
            if (!is_array($categories)) {
                $categories = explode(',', $categories);
            }

            $result = $articleManager->addArticle($nom, $description, $prix, $stock, $taille, $marque, $collection, $imageName, $categories);

            if ($result) {
                $output = ['success' => true, 'message' => 'Article ajouté avec succès'];
            } else {
                $output = ['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'article'];
            }
        } else {
            $output = ['success' => false, 'message' => implode('<br>', $errors)];
        }

        // À la fin du fichier
        echo json_encode($output);
        exit;
    } else {
        throw new Exception('Requête invalide');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Définir le type de contenu comme JSON
header('Content-Type: application/json');

?>