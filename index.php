<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitMode</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include './includes/_header.php'; ?>

    <div class="user-actions">
        <a href="connexion.php" class="btn">Connexion</a>
        <a href="inscription.php" class="btn">Inscription</a>
    </div>

    <div class="search-container">
        <div class="search-bar">
            <input type="text" id="search" placeholder="Rechercher...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <div class="sidebar">
        <ul class="menu">
            <li>
                <a href="#" class="menu-item">Tout les articles <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <!-- Sous-catégories si nécessaire -->
                </ul>
            </li>
            <li>
                <a href="#" class="menu-item">Homme <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">T-shirts</a></li>
                    <li><a href="#">Pantalons</a></li>
                    <li><a href="#">Chaussures</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="menu-item">Femme <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">Robes</a></li>
                    <li><a href="#">Jupes</a></li>
                    <li><a href="#">Accessoires</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="menu-item">Enfants <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">Garçons</a></li>
                    <li><a href="#">Filles</a></li>
                    <li><a href="#">Bébés</a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="menu-item">Sports <i class="fas fa-chevron-down"></i></a>
                <ul class="submenu">
                    <li><a href="#">Football</a></li>
                    <li><a href="#">Basketball</a></li>
                    <li><a href="#">Handball</a></li>
                    <li><a href="#">Rugby</a></li>
                </ul>
            </li>
        </ul>
    </div>   

    <main>
        <!-- Contenu principal de votre page -->
    </main>

    <footer>
        <!-- Pied de page -->
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
    <script src="script.js"></script>
</body>
</html>


