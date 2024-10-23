<?php
session_start(); // Assurez-vous que la session est démarrée

// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Inclure l'autoloader de Composer

// Inclure le fichier de connexion à la base de données
require_once '../includes/_db.php'; // Assurez-vous que ce chemin est correct

// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

// Calculer le montant total
$total_amount = 0;
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    foreach ($_SESSION['panier'] as $id_produit => $quantity) {
        $query = "SELECT prix FROM produits WHERE id_produit = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $total_amount += $product['prix'] * $quantity;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Créer d'abord l'intention de paiement
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $total_amount * 100, // Montant en cents
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // Si l'utilisateur est connecté, enregistrer dans la base de données
        if (isset($_SESSION['id_utilisateur'])) {
            try {
                // Insérer dans la table paiements
                $stmt = $conn->prepare("INSERT INTO paiements (
                    montant, 
                    date_paiement, 
                    methode_paiement, 
                    statut_paiement, 
                    transaction_id, 
                    id_utilisateur
                ) VALUES (?, NOW(), 'carte', 'en_attente', ?, ?)");
                
                $stmt->bind_param(
                    "dsi",
                    $total_amount,
                    $paymentIntent->id,
                    $_SESSION['id_utilisateur']
                );
                $stmt->execute();
            } catch (Exception $e) {
                // Log l'erreur mais continuer le processus de paiement
                error_log("Erreur SQL: " . $e->getMessage());
            }
        }

        echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo json_encode(['message' => 'Erreur : ' . $e->getMessage()]);
    }
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <form id="payment-form" class="mt-6">
        <div id="card-element" class="border p-4 rounded"></div>
        <button id="submit" class="mt-4 bg-blue-500 text-white py-2 px-4 rounded">Payer</button>
        <div id="payment-result" class="mt-4"></div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('pk_test_51Q7Hl1P5XJmDt2UGKTXg2A7p3bt8nsP1POLDv881WalxO2rQzdN7CxuflpPdoft3pCcEMnlLxLfTOxeh58sHpLbN00ITmhtq3O');
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
                    console.error('Error creating payment method:', error);
                    document.getElementById('payment-result').innerText = error.message;
                } else {
                    const formData = new FormData();
                    formData.append('paymentMethodId', paymentMethod.id);

                    const response = await fetch('process_paiement.php', {
                        method: 'POST',
                        body: formData,
                    });

                    const result = await response.json();
                    console.log('Server response:', result);
                    if (result.clientSecret) {
                        const { error: confirmError } = await stripe.confirmCardPayment(
                            result.clientSecret,
                            {
                                payment_method: paymentMethod.id,
                            }
                        );

                        if (confirmError) {
                            document.getElementById('payment-result').innerText = confirmError.message;
                        } else {
                            document.getElementById('payment-result').innerText = 'Paiement réussi !';
                            // Vider le panier en appelant une page PHP
                            await fetch('vider_panier.php');
                            // Redirection vers la page de confirmation
                            window.location.href = 'confirmation_paiement.php';
                        }
                    } else {
                        throw new Error(result.message || 'Erreur inconnue');
                    }
                }
            });
        });
    </script>
</body>
</html>
