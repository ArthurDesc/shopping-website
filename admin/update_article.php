<?php
require_once '../includes/session.php';
require_once '../includes/_db.php';
require_once '../classe/AdminManager.php';

// Vérification de l'authentification et des droits admin
$adminManager = new AdminManager($conn);
if (!isset($_SESSION['id_utilisateur']) || !$adminManager->isAdmin($_SESSION['id_utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

// Traitement de l'upload d'image
if (isset($_POST['action']) && $_POST['action'] === 'update_image') {
    $id_produit = $_POST['id_produit'];
    $image = $_FILES['image'] ?? null;

    if (!$image) {
        echo json_encode(['success' => false, 'message' => 'Aucune image fournie']);
        exit;
    }

    // Vérifications de sécurité pour l'image
    $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "png" => "image/png", "gif" => "image/gif"];
    $filename = $image["name"];
    $filetype = $image["type"];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!array_key_exists($ext, $allowed) || !in_array($filetype, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Format de fichier non autorisé']);
        exit;
    }

    // Générer un nom unique pour l'image
    $new_filename = uniqid() . '.' . $ext;
    $upload_path = '../assets/images/produits/' . $new_filename;
    $image_url = '/shopping-website/assets/images/produits/' . $new_filename;

    if (move_uploaded_file($image["tmp_name"], $upload_path)) {
        // Mise à jour de l'URL dans la base de données
        $stmt = $conn->prepare("UPDATE produits SET image_url = ? WHERE id_produit = ?");
        $stmt->bind_param("si", $new_filename, $id_produit);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'new_image_url' => $image_url]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour en base de données']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement de l\'image']);
    }
    exit;
}

// Traitement des autres mises à jour
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$id_produit = $data['id_produit'];
$field = $data['field'];
$new_value = $data['new_value'];

// Traitement spécial pour les catégories
if ($field === 'categories') {
    try {
        // Supprimer les anciennes catégories
        $stmt = $conn->prepare("DELETE FROM produit_categorie WHERE id_produit = ?");
        $stmt->execute([$id_produit]);

        // Ajouter les nouvelles catégories
        $stmt = $conn->prepare("INSERT INTO produit_categorie (id_produit, id_categorie) VALUES (?, ?)");
        foreach ($new_value as $categoryId) {
            $stmt->execute([$id_produit, $categoryId]);
        }
        
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Liste des champs autorisés pour les autres mises à jour
$allowed_fields = ['nom', 'description', 'prix', 'marque', 'collection', 'stock', 'categories'];

if (!in_array($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'message' => 'Champ non autorisé']);
    exit;
}

// Préparation de la requête SQL pour les autres champs
$sql = "UPDATE produits SET $field = ? WHERE id_produit = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_value, $id_produit);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
