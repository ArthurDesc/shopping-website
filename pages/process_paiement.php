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

// Récupérer les informations de l'utilisateur
$stmt = $conn->prepare("SELECT adresse, telephone FROM utilisateurs WHERE id_utilisateur = ?");
$stmt->bind_param("i", $_SESSION['id_utilisateur']);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Mettre à jour les informations de l'utilisateur si elles ont changé
        $stmt = $conn->prepare("UPDATE utilisateurs SET adresse = ?, telephone = ? WHERE id_utilisateur = ?");
        $stmt->bind_param("ssi", $_POST['adresse'], $_POST['telephone'], $_SESSION['id_utilisateur']);
        $stmt->execute();

        // Démarrer une transaction
        $conn->begin_transaction();

        // Créer l'intention de paiement Stripe
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => (int)($total_amount * 100),
            'currency' => 'eur',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        // 1. Créer la commande
        $stmt = $conn->prepare("INSERT INTO commandes (date_commande, montant_total, id_utilisateur, statut) VALUES (NOW(), ?, ?, 'validé')");
        $stmt->bind_param("di", $total_amount, $_SESSION['id_utilisateur']);
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création de la commande");
        }
        $id_commande = $conn->insert_id;

        // 2. Créer le paiement
        $stmt = $conn->prepare("INSERT INTO paiements (
            montant, 
            date_paiement, 
            methode_paiement, 
            statut_paiement, 
            transaction_id, 
            id_commande, 
            id_utilisateur
        ) VALUES (?, NOW(), 'carte', 'réussi', ?, ?, ?)");
        
        $stmt->bind_param("dsii", 
            $total_amount, 
            $paymentIntent->id,
            $id_commande,
            $_SESSION['id_utilisateur']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de l'enregistrement du paiement");
        }

        // 3. Créer les lignes de commande et mettre à jour les stocks
        foreach ($_SESSION['panier'] as $id_produit => $quantity) {
            // Vérifier le stock disponible
            $stmt = $conn->prepare("SELECT prix, stock FROM produits WHERE id_produit = ?");
            $stmt->bind_param("i", $id_produit);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            if (!$product || $product['stock'] < $quantity) {
                throw new Exception("Stock insuffisant pour le produit ID: " . $id_produit);
            }

            // Vérifier si le produit est déjà dans la commande
            $stmt = $conn->prepare("SELECT quantite FROM commande_produit WHERE id_commande = ? AND id_produit = ?");
            $stmt->bind_param("ii", $id_commande, $id_produit);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Mettre à jour la quantité si le produit existe déjà
                $stmt = $conn->prepare("UPDATE commande_produit SET quantite = quantite + ? WHERE id_commande = ? AND id_produit = ?");
                $stmt->bind_param("iii", $quantity, $id_commande, $id_produit);
            } else {
                // Insérer la ligne de commande
                $stmt = $conn->prepare("INSERT INTO commande_produit (id_commande, id_produit, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiid", $id_commande, $id_produit, $quantity, $product['prix']);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'enregistrement des produits commandés");
            }

            // Mettre à jour le stock
            $stmt = $conn->prepare("UPDATE produits SET stock = stock - ? WHERE id_produit = ?");
            $stmt->bind_param("ii", $quantity, $id_produit);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour du stock");
            }
        }

        // Si tout s'est bien passé, valider la transaction
        $conn->commit();

        // Retourner le client secret pour Stripe
        echo json_encode(['clientSecret' => $paymentIntent->client_secret]);

    } catch (Exception $e) {
        // En cas d'erreur, annuler toutes les modifications
        $conn->rollback();
        
        error_log("Erreur lors du traitement du paiement: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['message' => 'Une erreur est survenue: ' . $e->getMessage()]);
    }
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Ajout de Tailwind CSS -->
    <script src="https://js.stripe.com/v3/"></script> <!-- Ajout de Stripe.js -->
   <link rel="stylesheet" href="../assets/css/process_paiement.css">
    <style>
        body {
            background-image: url('../assets/images/background.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            margin: 2rem auto;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen py-8 relative">
    <!-- Ajout du bouton retour -->
    <a href="javascript:history.back()" 
       class="absolute top-4 left-4 p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 transition-colors duration-200">
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-6 w-6 text-blue-600" 
             fill="none" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" 
                  stroke-linejoin="round" 
                  stroke-width="2" 
                  d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
    </a>

    <form id="payment-form" class="form-container">
        <h2 class="form-title">Paiement Sécurisé</h2>
        
        <!-- Informations de livraison -->
        <div class="mb-6 border-b pb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Informations de livraison</h3>
            
            <div class="mb-4">
                <label for="adresse" class="input-label">Adresse de livraison</label>
                <textarea 
                    id="adresse" 
                    name="adresse" 
                    rows="3" 
                    class="input-field h-24"
                    required
                ><?php echo htmlspecialchars($user_info['adresse'] ?? ''); ?></textarea>
            </div>

            <div class="mb-4">
                <label for="telephone" class="input-label">Numéro de téléphone</label>
                <input 
                    type="tel" 
                    id="telephone" 
                    name="telephone" 
                    value="<?php echo htmlspecialchars($user_info['telephone'] ?? ''); ?>"
                    class="input-field"
                    required
                >
            </div>
        </div>

        <!-- Informations de paiement -->
        <div class="mb-6">
            <label for="card-holder-name" class="input-label">Nom du titulaire de la carte</label>
            <input type="text" id="card-holder-name" class="input-field" placeholder="John Doe" required>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-semibold mb-2">Informations de carte</label>
            <div id="card-element" class="form-input"></div>
            <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
        </div>

        <!-- Total de la commande -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <span class="text-gray-700 text-sm font-semibold">Total à payer :</span>
                <span class="font-bold text-lg text-blue-600"><?php echo number_format($total_amount, 2, ',', ' '); ?> €</span>
            </div>
        </div>

        <button id="submit" class="w-full bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Procéder au paiement
        </button>
        
        <div id="payment-result" class="mt-4 text-center font-bold text-green-600"></div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('pk_test_51Q7Hl1P5XJmDt2UGKTXg2A7p3bt8nsP1POLDv881WalxO2rQzdN7CxuflpPdoft3pCcEMnlLxLfTOxeh58sHpLbN00ITmhtq3O');
            const elements = stripe.elements();
            const cardElement = elements.create('card', {
                style: {
                    base: {
                        color: '#32325d',
                        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        lineHeight: '24px',
                        padding: '2px',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a',
                        iconColor: '#fa755a'
                    }
                }
            });
            cardElement.mount('#card-element');

            const form = document.getElementById('payment-form');
            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const cardHolderName = document.getElementById('card-holder-name').value;
                const adresse = document.getElementById('adresse').value;
                const telephone = document.getElementById('telephone').value;

                // Vérifier que les champs sont remplis
                if (!adresse || !telephone) {
                    document.getElementById('card-errors').innerText = 'Veuillez remplir tous les champs obligatoires';
                    return;
                }

                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement,
                    billing_details: {
                        name: cardHolderName,
                        phone: telephone,
                        address: {
                            line1: adresse
                        }
                    },
                });

                if (error) {
                    document.getElementById('card-errors').innerText = error.message;
                } else {
                    document.getElementById('card-errors').innerText = '';
                    const formData = new FormData();
                    formData.append('paymentMethodId', paymentMethod.id);
                    formData.append('adresse', adresse);
                    formData.append('telephone', telephone);

                    const response = await fetch('process_paiement.php', {
                        method: 'POST',
                        body: formData,
                    });

                    const result = await response.json();
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
                        document.getElementById('payment-result').innerText = result.message || 'Erreur inconnue';
                    }
                }
            });
        });
    </script>
</body>
</html>
