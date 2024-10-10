<?php
require_once '../includes/_db.php';
require_once '../classe/ArticleManager.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_article') {
    $articleManager = new ArticleManager($conn);

    $nom = $_POST['titre'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $taille = $_POST['taille'] ?? ''; // Assurez-vous que ce champ existe dans votre formulaire
    $marque = $_POST['marque'];
    $collection = $_POST['collection'];
    $categories = !empty($_POST['categories']) ? explode(',', $_POST['categories']) : [];

    // Gestion de l'image
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["image"]["name"];
        $filetype = $_FILES["image"]["type"];
        $filesize = $_FILES["image"]["size"];
    
        // Vérifiez l'extension du fichier
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");
    
        // Vérifiez la taille du fichier - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if ($filesize > $maxsize) die("Erreur: La taille du fichier est supérieure à la limite autorisée.");
    
        // Vérifiez le type MIME du fichier
        if (in_array($filetype, $allowed)) {
            // Vérifiez si le fichier existe avant de le télécharger.
            if (file_exists("../uploads/" . $filename)) {
                echo $filename . " existe déjà.";
            } else {
                move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/" . $filename);
                $image = $filename;
            }
        } else {
            echo "Erreur: Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer."; 
        }
    }

    try {
        $result = $articleManager->addArticle($nom, $description, $prix, $stock, $taille, $marque, $collection, $image, $categories);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Article ajouté avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'article']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}
