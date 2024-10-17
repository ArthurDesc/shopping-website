<?php
// Message d'annulation
echo '<!DOCTYPE html>';
echo '<html lang="fr">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">';
echo '<title>Annulation de paiement</title>';
echo '</head>';
echo '<body>';
echo '<div class="flex flex-col items-center justify-center min-h-screen bg-gray-100">';
echo '<h1 class="text-3xl font-bold text-red-600 mb-4">Le paiement a été annulé</h1>';
echo '<p class="text-lg text-gray-700 mb-6">Votre commande n\'a pas été traitée. Vous pouvez retourner à votre panier pour continuer vos achats.</p>';
echo '<a href="http://localhost/shopping-website/pages/panier.php" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Retourner au panier</a>';
echo '</div>';
echo '</body>';
echo '</html>';
?>
