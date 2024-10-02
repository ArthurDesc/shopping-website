<?php
// includes/session.php

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/shopping-website/');

// Fonction pour vérifier si l'utilisateur est connecté
function is_logged_in() {
    return isset($_SESSION['id_utilisateur']) && isset($_SESSION['email']);
}

// Fonction pour obtenir l'ID de l'utilisateur connecté
function get_user_id() {
    return $_SESSION['id_utilisateur'] ?? null;
}

// Fonction pour obtenir l'email de l'utilisateur connecté
function get_user_email() {
    return $_SESSION['email'] ?? null;
}

// Fonction pour obtenir toutes les informations de l'utilisateur
function get_user_info() {
    return [
        'id' => $_SESSION['id_utilisateur'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'nom' => $_SESSION['nom'] ?? null,
        'prenom' => $_SESSION['prenom'] ?? null,
    ];
}

// Fonction pour définir les informations de l'utilisateur lors de la connexion
function set_user_session($user_data) {
    $_SESSION['id_utilisateur'] = $user_data['id'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['nom'] = $user_data['nom'];
    $_SESSION['prenom'] = $user_data['prenom'];
}

// Fonction pour déconnecter l'utilisateur
function logout() {
    session_unset();
    session_destroy();
}

// Fonction pour rediriger si l'utilisateur n'est pas connecté
function require_login() {
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "pages/connexion.php");
        exit();
    }
}

// Vous pouvez ajouter d'autres fonctions liées à la session ici
