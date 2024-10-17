<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/_db.php';
require_once '../includes/_header.php';

// Fonction pour nettoyer les clés du panier
function cleanCartKey($key) {
    $parts = explode('_', $key);
    return intval($parts[0]);
}

// Nettoyage du panier
if (isset($_SESSION['panier']) && is_array($_SESSION['panier'])) {
    $cleanedCart = array();
    foreach ($_SESSION['panier'] as $key => $value) {
        $cleanKey = cleanCartKey($key);
        if (is_array($value)) {
            $cleanedCart[$cleanKey] = $value;
        } else {
            $cleanedCart[$cleanKey] = ['quantite' => $value, 'taille' => null];
        }
    }
    $_SESSION['panier'] = $cleanedCart;
}

// Affichage du panier
echo "<div class='container mx-auto px-4 py-8'>";
echo "<h2 class='text-2xl font-bold mb-4'>Votre panier</h2>";

if (empty($_SESSION['panier'])) {
    echo "<p>Votre panier est vide.</p>";
} else {
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full text-sm text-left text-gray-500'>";
    echo "<thead class='text-xs text-gray-700 uppercase bg-gray-50'>";
    echo "<tr>";
    echo "<th scope='col' class='px-6 py-3'>Produit</th>";
    echo "<th scope='col' class='px-6 py-3'>Prix</th>";
    echo "<th scope='col' class='px-6 py-3'>Quantité</th>";
    echo "<th scope='col' class='px-6 py-3'>Total</th>";
    echo "<th scope='col' class='px-6 py-3'>Action</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $total = 0;

    foreach ($_SESSION['panier'] as $product_id => $item) {
        $product_id = intval($product_id);
        $quantite = $item['quantite'];
        $taille = $item['taille'] ?? null;

        $sql = "SELECT * FROM produits WHERE id_produit = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            echo "<tr><td colspan='5' class='text-red-500'>Erreur de préparation de la requête pour le produit ID $product_id</td></tr>";
            continue;
        }

        $stmt->bind_param("i", $product_id);
        
        if (!$stmt->execute()) {
            echo "<tr><td colspan='5' class='text-red-500'>Erreur d'exécution de la requête pour le produit ID $product_id</td></tr>";
            continue;
        }

        $result = $stmt->get_result();
        $produit = $result->fetch_assoc();

        if ($produit) {
            $prix_total = $produit['prix'] * $quantite;
            $total += $prix_total;

            echo "<tr class='bg-white border-b'>";
            echo "<td class='px-6 py-4 font-medium text-gray-900 whitespace-nowrap'>";
            echo "<div class='flex items-center'>";
            if (!empty($produit['image'])) {
                echo "<img class='w-10 h-10 object-cover mr-3' src='" . htmlspecialchars($produit['image']) . "' alt='" . htmlspecialchars($produit['nom']) . "'>";
            }
            echo htmlspecialchars($produit['nom']);
            if ($taille) {
                echo " <span class='text-sm text-gray-500'>(Taille : " . htmlspecialchars($taille) . ")</span>";
            }
            echo "</div>";
            echo "</td>";
            echo "<td class='px-6 py-4'>" . number_format($produit['prix'], 2, ',', ' ') . " €</td>";
            echo "<td class='px-6 py-4'>" . htmlspecialchars($quantite) . "</td>";
            echo "<td class='px-6 py-4'>" . number_format($prix_total, 2, ',', ' ') . " €</td>";
            echo "<td class='px-6 py-4'>";
            echo "<a href='supprimer_produit.php?id=" . $product_id . "' class='font-medium text-red-600 hover:underline'>Supprimer</a>";
            echo "</td>";
            echo "</tr>";
        } else {
            echo "<tr><td colspan='5' class='text-red-500'>Produit non trouvé pour l'ID : $product_id</td></tr>";
        }

        $stmt->close();
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";

    echo "<div class='mt-8 flex justify-between items-center'>";
    echo "<div>";
    echo "<p class='text-xl font-bold'>Total du panier : " . number_format($total, 2, ',', ' ') . " €</p>";
    echo "</div>";
    echo "<div class='space-x-4'>";
    echo "<a href='passer_commande.php' class='inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>Passer la commande</a>";
    echo "<button onclick='showPaymentOptions()' class='bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600'>Payer maintenant</button>";
    echo "</div>";
    echo "</div>";

    // Options de paiement (initialement cachées)
    echo "<div id='paymentOptions' class='hidden mt-8 p-4 bg-gray-100 rounded'>";
    echo "<h3 class='text-lg font-semibold mb-4'>Choisissez votre méthode de paiement :</h3>";
    echo "<div class='space-y-2'>";
    echo "<button onclick='processPayment(\"carte\")' class='w-full bg-white border border-gray-300 px-4 py-2 rounded hover:bg-gray-50'>Carte bancaire</button>";
    echo "<button onclick='processPayment(\"paypal\")' class='w-full bg-white border border-gray-300 px-4 py-2 rounded hover:bg-gray-50'>PayPal</button>";
    echo "<button onclick='processPayment(\"virement\")' class='w-full bg-white border border-gray-300 px-4 py-2 rounded hover:bg-gray-50'>Virement bancaire</button>";
    echo "</div>";
    echo "</div>";
}

echo "</div>";

// JavaScript pour gérer l'affichage des options de paiement
echo "<script>
function showPaymentOptions() {
    document.getElementById('paymentOptions').classList.remove('hidden');
}

function processPayment(method) {
    alert('Paiement par ' + method + ' en cours de traitement...');
    // Ici, vous pouvez rediriger vers une page de paiement ou effectuer une action AJAX
}
</script>";

require_once '../includes/_footer.php';
?>
