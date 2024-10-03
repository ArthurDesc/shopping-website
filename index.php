<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitMode - Votre boutique de mode en ligne</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Votre fichier CSS personnalisé -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include './includes/_header.php'; ?>
<?php include './includes/session.php'; ?>

    <main class="container-fluid mt-4">
        <!-- Hero Section avec liens d'inscription et de connexion -->
        <section class="hero mb-4 position-relative">
            <div class="user-actions position-absolute top-0 start-0 mt-3 ms-3">
                <a href="inscription.php" class="btn btn-outline-primary btn-sm me-2">Inscription</a>
                <a href="connexion.php" class="btn btn-outline-secondary btn-sm">Connexion</a>
            </div>
            <div class="jumbotron text-center py-5">
                <h1>Bienvenue chez FitMode</h1>
                <p>Découvrez les dernières tendances de la mode</p>
                <a href="#nouveautes" class="btn btn-primary">Voir les nouveautés</a>
            </div>
        </section>

        <!-- Catégories populaires -->
        <section class="categories mb-4">
            <h2>Catégories populaires</h2>
            <div class="row">
                <div class="col-md-3 col-6 mb-3">
                    <a href="#" class="category-link">
                        <img src="images/homme.jpg" alt="Homme" class="img-fluid">
                        <h3>Homme</h3>
                    </a>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <a href="#" class="category-link">
                        <img src="images/femme.jpg" alt="Femme" class="img-fluid">
                        <h3>Femme</h3>
                    </a>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <a href="#" class="category-link">
                        <img src="images/enfants.jpg" alt="Enfants" class="img-fluid">
                        <h3>Enfants</h3>
                    </a>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <a href="#" class="category-link">
                        <img src="images/accessoires.jpg" alt="Accessoires" class="img-fluid">
                        <h3>Accessoires</h3>
                    </a>
                </div>
            </div>
        </section>

        <!-- Nouveautés -->
        <section id="nouveautes" class="mb-4">
            <h2>Nouveautés</h2>
            <div class="row">
                <?php
                $nouveautes = [
                    ['nom' => 'T-shirt tendance', 'prix' => '19.99', 'image' => 'tshirt.jpg'],
                    ['nom' => 'Jean slim', 'prix' => '49.99', 'image' => 'jean.jpg'],
                    ['nom' => 'Robe d\'été', 'prix' => '39.99', 'image' => 'robe.jpg'],
                    ['nom' => 'Sneakers', 'prix' => '59.99', 'image' => 'sneakers.jpg'],
                ];

                foreach ($nouveautes as $produit) {
                    echo '<div class="col-md-3 col-6 mb-3">';
                    echo '<div class="card">';
                    echo '<img src="images/' . $produit['image'] . '" class="card-img-top" alt="' . $produit['nom'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $produit['nom'] . '</h5>';
                    echo '<p class="card-text">' . $produit['prix'] . ' €</p>';
                    echo '<a href="#" class="btn btn-primary">Voir le produit</a>';
                    echo '</div></div></div>';
                }
                ?>
            </div>
        </section>

        <!-- Promotions -->
        <section class="promotions mb-4">
            <h2>Promotions</h2>
            <div class="row">
                <?php
                $promotions = [
                    ['nom' => 'Veste en cuir', 'prix_initial' => '199.99', 'prix_promo' => '149.99', 'image' => 'veste-cuir.jpg', 'reduction' => '-25%'],
                    ['nom' => 'Robe de soirée', 'prix_initial' => '89.99', 'prix_promo' => '69.99', 'image' => 'robe-soiree.jpg', 'reduction' => '-22%'],
                    ['nom' => 'Chaussures de sport', 'prix_initial' => '79.99', 'prix_promo' => '59.99', 'image' => 'chaussures-sport.jpg', 'reduction' => '-25%'],
                    ['nom' => 'Chemise homme', 'prix_initial' => '49.99', 'prix_promo' => '39.99', 'image' => 'chemise-homme.jpg', 'reduction' => '-20%'],
                ];

                foreach ($promotions as $promo) {
                    echo '<div class="col-md-3 col-6 mb-3">';
                    echo '<div class="card">';
                    echo '<div class="promotion-badge">' . $promo['reduction'] . '</div>';
                    echo '<img src="images/' . $promo['image'] . '" class="card-img-top" alt="' . $promo['nom'] . '">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $promo['nom'] . '</h5>';
                    echo '<p class="card-text"><s>' . $promo['prix_initial'] . ' €</s> <strong>' . $promo['prix_promo'] . ' €</strong></p>';
                    echo '<a href="#" class="btn btn-danger">Voir l\'offre</a>';
                    echo '</div></div></div>';
                }
                ?>
            </div>
        </section>

        <!-- Marques populaires -->
        <section class="brands mb-4">
            <h2>Nos marques populaires</h2>
            <div class="row align-items-center text-center">
                <?php
                $marques = [
                    ['nom' => 'Nike', 'logo' => 'nike-logo.png'],
                    ['nom' => 'Adidas', 'logo' => 'adidas-logo.png'],
                    ['nom' => 'Zara', 'logo' => 'zara-logo.png'],
                    ['nom' => 'H&M', 'logo' => 'hm-logo.png'],
                    ['nom' => 'Levi\'s', 'logo' => 'levis-logo.png'],
                    ['nom' => 'Calvin Klein', 'logo' => 'calvin-klein-logo.png'],
                ];

                foreach ($marques as $marque) {
                    echo '<div class="col-4 col-md-2 mb-3">';
                    echo '<img src="images/logos/' . $marque['logo'] . '" alt="' . $marque['nom'] . '" class="img-fluid brand-logo">';
                    echo '</div>';
                }
                ?> <!-- Marques populaires -->
                <!-- Marques populaires -->
            </div>
        </section>
    </main>

    <?php include './includes/_footer.php'; ?>

    <!-- jQuery (nécessaire pour Bootstrap et Materialize) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    
    <!-- Votre fichier JavaScript personnalisé -->
    <script src="js/main.js"></script>
</body>
</html>