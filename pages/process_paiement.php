<?php
session_start(); // Assurez-vous que la session est démarrée

// Inclure l'autoloader de Composer
require_once '../vendor/autoload.php'; // Inclure l'autoloader de Composer

// Inclure le fichier de connexion à la base de données
require_once '../includes/_db.php'; // Assurez-vous que ce chemin est correct

// Configuration de Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q7Hl1P5XJmDt2UG2j3o2mIobvzMWo0XoZ8Md4YeqakLP682h9aEuYczQfUzjEMEdt6SyLUENnbgTmZPNotX2rEa00cMDNxsLs'); // Remplace par ta clé secrète

// Vérifier si le montant est présent
$total_amount = 0; // Montant total en cents
$line_items = []; // Initialiser un tableau pour les lignes de produits

// Vérifier si le panier est utilisé
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    var_dump($_SESSION['panier']); // Debug: Afficher le contenu du panier
    foreach ($_SESSION['panier'] as $id_produit => $quantity) {
        // Récupérer le prix du produit
        $query = "SELECT prix, nom FROM produits WHERE id_produit = ?"; // Ajout du nom du produit
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            $total_amount += $product['prix'] * $quantity; // Calculer le montant total
            // Ajouter les informations du produit pour la session de checkout
            $line_items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $product['nom'], // Utiliser le nom du produit
                    ],
                    'unit_amount' => $product['prix'] * 100, // Montant en cents
                ],
                'quantity' => $quantity,
            ];
        }
    }
}

if (empty($line_items)) {
    // Rediriger ou afficher un message d'erreur si le panier est vide
    echo 'Erreur : Aucun produit dans le panier.';
    exit; // Arrêter l'exécution du script
}

try {
    // Créer une session de checkout
    $checkout_session = \Stripe\Checkout\Session::create([
        'line_items' => $line_items, // Utiliser les lignes de produits récupérées
        'mode' => 'payment',
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
        const stripe = Stripe('pk_test_51Q7Hl1P5XJmDt2UGKTXg2A7p3bt8nsP1POLDv881WalxO2rQzdN7CxuflpPdoft3pCcEMnlLxLfTOxeh58sHpLbN00ITmhtq3O'); // Remplacez par votre clé publique Stripe
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
