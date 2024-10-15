<?php
// Inclure l'autoloader de Composer
require_once 'stripe-php/init.php';
// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

// Vérifier si le token est présent
if (isset($_POST['stripeToken'])) {
    $token = $_POST['stripeToken'];

    try {
        // Créer une charge
        $charge = \Stripe\Charge::create([
            'amount' => 1000, // Montant en cents (ex: 10,00 €)
            'currency' => 'eur',
            'description' => 'Achat dans le panier',
            'source' => $token,
        ]);
        // Redirection après paiement réussi
        header('Location: confirmation_paiement.php');
        exit;
    } catch (\Stripe\Exception\CardException $e) {
        // Gestion des erreurs de carte
        echo 'Erreur : ' . $e->getError()->message;
    } catch (Exception $e) {
        // Gestion des autres erreurs
        echo 'Erreur générale : ' . $e->getMessage();
    }
} else {
    echo 'Aucune information de paiement reçue.';
}
