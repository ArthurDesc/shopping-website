<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitMode - Votre boutique de mode en ligne</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Vos autres styles CSS personnalisés peuvent être ajoutés ici -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/custom.css">
</head>
<body class="bg-gray-100">
    <?php include './includes/_header.php'; ?>
    <?php include './includes/session.php'; ?>

    

    <?php include './includes/_footer.php'; ?>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/script.js" defer></script>
</body>
</html>
