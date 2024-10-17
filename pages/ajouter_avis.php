<?php
session_start();
require_once '../includes/_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['id_utilisateur'])) {
        $_SESSION['error_message'] = "Vous devez être connecté pour laisser un avis.";
        header("Location: connexion.php");
        exit();
    }

    $id_produit = $_POST['id_produit'];
    $id_utilisateur = $_SESSION['id_utilisateur'];
    $note = $_POST['note'];
    $commentaire = $_POST['commentaire'];

    $sql = "INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $id_produit, $id_utilisateur, $note, $commentaire);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Votre avis a été ajouté avec succès.";
        header("Location: avis.php?id_produit=" . $id_produit);
    } else {
        $_SESSION['error_message'] = "Une erreur est survenue lors de l'ajout de votre avis.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
    exit();
} else {
    header("Location: index.php");
    exit();
}
