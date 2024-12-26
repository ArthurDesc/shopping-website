<?php
// db.php

$servername = "localhost";
$username = "u521981000_derroce";
$password = "Nalamaman210'";
$dbname = "u521981000_boutique";

// Créer la connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error());
}

// Supprimez ou commentez cette ligne
// echo json_encode(['status' => 'connected']);
?>