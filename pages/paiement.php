<?php
// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Inclure l'autoloader de Composer
// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

// Si le panier est utilisé, rediriger vers process_paiement.php
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    header("Location: process_paiement.php");
    exit;
} elseif (isset($_GET['id_produit'])) { // Si un produit est directement acheté
    $id_produit = $_GET['id_produit'];
    header("Location: process_paiement.php?id_produit=" . $id_produit);
    exit;
}
?>
