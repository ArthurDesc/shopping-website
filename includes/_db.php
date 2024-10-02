<?php
// db.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// On retourne une réponse pour valider que la connexion est réussie
echo json_encode(['status' => 'connected']);
?>
