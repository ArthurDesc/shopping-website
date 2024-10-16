<?php
session_start();
require_once '../includes/_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Vous devez être connecté pour laisser un avis.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $id_produit = $_POST['id_produit'];
    $id_utilisateur = $_SESSION['user_id'];
    $note = $_POST['note'];
    $commentaire = $_POST['commentaire'];

    $sql = "INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_produit, $id_utilisateur, $note, $commentaire);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Votre avis a été ajouté avec succès.";
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de l'ajout de votre avis.";
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} else {
    header("Location: " . BASE_URL);
    exit();
}