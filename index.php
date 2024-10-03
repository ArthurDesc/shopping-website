<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitMode - Votre boutique de mode en ligne</title>
    <!-- Lien vers Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Lien vers Materialize CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <!-- Lien vers Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Lien vers Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include './includes/_header.php'; ?>
    <?php include './includes/session.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                Tous les articles <i class="fas fa-chevron-down"></i>
                            </a>
                            <!-- Sous-menu si nécessaire -->
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Homme <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="nav flex-column ml-3">
                                <li class="nav-item"><a class="nav-link" href="#">T-shirts</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Pantalons</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Chaussures</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Femme <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="nav flex-column ml-3">
                                <li class="nav-item"><a class="nav-link" href="#">Robes</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Jupes</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Accessoires</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Enfants <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="nav flex-column ml-3">
                                <li class="nav-item"><a class="nav-link" href="#">Garçons</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Filles</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Bébés</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Sports <i class="fas fa-chevron-down"></i>
                            </a>
                            <ul class="nav flex-column ml-3">
                                <li class="nav-item"><a class="nav-link" href="#">Football</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Basketball</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Handball</a></li>
                                <li class="nav-item"><a class="nav-link" href="#">Rugby</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Contenu principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Bienvenue chez FitMode</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="pages/connexion.php" class="btn btn-sm btn-outline-secondary">Connexion</a>
                            <a href="pages/inscription.php" class="btn btn-sm btn-outline-secondary">Inscription</a>
                            <a href="pages/profil.php" class="btn btn-sm btn-outline-primary">Profil</a>
                        </div>
                    </div>
                </div>

                <!-- Barre de recherche -->
                <div class="search-container mb-4">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Rechercher..." aria-label="Search">
                        <button class="btn btn-outline-success" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Contenu de la page d'accueil -->
                <section class="featured-products">
                    <h2>Nos produits vedettes</h2>
                    <div class="row row-cols-1 row-cols-md-3 g-4">
                        <!-- Ajoutez ici vos produits vedettes -->
                    </div>
                </section>

                <section class="categories mt-5">
                    <h2>Explorez nos catégories</h2>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="#" class="category-link">
                                <img src="path/to/homme-image.jpg" alt="Homme" class="img-fluid">
                                <h3>Homme</h3>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="category-link">
                                <img src="path/to/femme-image.jpg" alt="Femme" class="img-fluid">
                                <h3>Femme</h3>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="category-link">
                                <img src="path/to/enfants-image.jpg" alt="Enfants" class="img-fluid">
                                <h3>Enfants</h3>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#" class="category-link">
                                <img src="path/to/sports-image.jpg" alt="Sports" class="img-fluid">
                                <h3>Sports</h3>
                            </a>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <?php include './includes/_footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="script.js"></script>
</body>
</html>