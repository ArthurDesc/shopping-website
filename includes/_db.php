<?php
// db.php

$servername = "mysql";
$username = "root";
$password = "root";
$dbname = "boutique";

// Créer la connexion
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Vérifier la connexion
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error());
}

// Supprimez ou commentez cette ligne
// echo json_encode(['status' => 'connected']);
?>