<?php
require_once '../includes/_header.php';
?>

<body class="bg-gray-100 flex flex-col items-center justify-center min-h-screen">
    <div class="text-center p-6 min-h-[60vh] flex flex-col justify-center items-center">
        <img src="<?php echo url('assets/images/icons/blueBag.png'); ?>"
            alt="Aucune commande"
            class="w-48 h-48 mb-6 animate-float" />
        <h2 class="text-2xl font-bold mb-4 text-blue-400">Paiement réussi !</h2>
        <p class="text-gray-700 mb-6">Merci pour votre achat.</p>
        <div class="flex flex-col items-center space-y-4">
            <a href="commandes.php" class="btn btn-small">
                Voir mes commandes
            </a>
        </div>
    </div>

    <?php require_once '../includes/_footer.php'; ?>
</body>

</html>