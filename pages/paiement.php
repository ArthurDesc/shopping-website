<?php
// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Changement effectué ici
// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <script src="https://js.stripe.com/v3/"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Ajout de Tailwind CSS -->
</head>
<body class="flex items-center justify-center h-screen bg-gradient-to-r from-blue-500 to-purple-500">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Paiement Sécurisé</h1>
        <form action="process_paiement.php" method="POST" id="payment-form">
            <div class="mb-4">
                <label for="name" class="block text-gray-700">Nom</label>
                <input type="text" id="name" name="name" required class="mt-1 p-2 border border-gray-300 rounded w-full" placeholder="Votre nom">
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700">E-mail</label>
                <input type="email" id="email" name="email" required class="mt-1 p-2 border border-gray-300 rounded w-full" placeholder="Votre e-mail">
            </div>
            <div id="card-element" class="mb-4 p-3 border border-gray-300 rounded-md"><!-- Un champ pour le numéro de carte --></div>
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg hover:opacity-80 transition duration-200">Payer</button>
        </form>
        <div id="card-errors" role="alert" class="text-red-500 mt-2 text-center"></div>
    </div>

    <script>
        const stripe = Stripe('pk_test_51Q7Hl1P5XJmDt2UGKTXg2A7p3bt8nsP1POLDv881WalxO2rQzdN7CxuflpPdoft3pCcEMnlLxLfTOxeh58sHpLbN00ITmhtq3O'); // Remplace par ta clé publique
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        // Gestion des erreurs
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    </script>
</body>
</html>
