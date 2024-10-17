<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <h1 class="text-3xl font-bold text-green-600">Paiement réussi !</h1>
    <p class="mt-4 text-lg">Merci pour votre achat.</p>
    
    <!-- Ajout du bouton pour retourner au panier -->
    <a href="panier.php" class="mt-6 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
        Retourner au panier
    </a>
  
</body>
</html>
