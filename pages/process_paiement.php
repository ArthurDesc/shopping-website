<?php
// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Inclure l'autoloader de Composer
// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

// Vérifier si le montant est présent
$total_amount = 0; // Montant total en cents

// Vérifier si le panier est utilisé
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $id_produit => $quantity) {
        // Récupérer le prix du produit
        $query = "SELECT prix FROM produits WHERE id_produit = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $total_amount += $product['prix'] * $quantity; // Calculer le montant total
        }
    }
}

try {
    // Créer une session de checkout
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Achat dans le panier', // Vous pouvez personnaliser cela
                ],
                'unit_amount' => $total_amount * 100, // Montant en cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'payment_method_types' => ['card'], // Corrected this line
        'success_url' => 'http://localhost/shopping-website/pages/confirmation_paiement.php',
        'cancel_url' => 'http://localhost/shopping-website/pages/cancel.php',
    ]);

    // Redirection vers la session de checkout
    header("Location: " . $checkout_session->url);
    exit;
} catch (\Stripe\Exception\CardException $e) {
    // Gestion des erreurs de carte
    echo 'Erreur : ' . $e->getError()->message;
} catch (Exception $e) {
    // Gestion des autres erreurs
    echo 'Erreur générale : ' . $e->getMessage();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form id="payment-form" action="process_paiement.php" method="POST" class="mt-6">
        <div id="card-element" class="border p-4 rounded"></div>
        <button id="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Payer</button>
        <div id="payment-result" class="mt-4"></div>
    </form>

    <script>
        const stripe = Stripe('YOUR_PUBLIC_STRIPE_KEY'); // Remplacez par votre clé publique Stripe
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
            });

            if (error) {
                document.getElementById('payment-result').innerText = error.message;
            } else {
                // Envoyer le paymentMethod.id à votre serveur pour le traitement
                const formData = new FormData();
                formData.append('paymentMethodId', paymentMethod.id);
                
                // Envoyer les données au fichier process_paiement.php
                const response = await fetch('process_paiement.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();
                document.getElementById('payment-result').innerText = result.message;
            }
        });
    </script>
</body>
</html>
