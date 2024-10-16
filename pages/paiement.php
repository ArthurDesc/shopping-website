<?php
// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Changement effectué ici
// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

$checkout_session = \Stripe\Checkout\session::create([
    "mode" => "payement",
    "success_url" => "http://localhost/success.php",
    "line_items" => [
        [
            "quantity" => 1,
            "currency" => "eur",
            "unit_amount" => 1000,
            "product_data" =>[
                "name" => "T-shirt"
            ]
        ]
    ]
]);


?>



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
