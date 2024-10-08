<?php include '../includes/session.php'; ?>
<?php include '../includes/_db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>BackOffice</title>
</head>

<body>
<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <nav class="mt-4">
            <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900" onclick="loadContent('dashboard')">
                Tableau de bord
            </a>
            <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900" onclick="loadContent('products')">
                Gestion des produits
            </a>
            <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900" onclick="loadContent('categories')">
                Gestion des catégories
            </a>
            <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-gray-900" onclick="loadContent('users')">
                Gestion des utilisateurs
            </a>
        </nav>
    </aside>

    <!-- Contenu principal -->
    <main id="main-content" class="flex-1 p-8">
        <div class="mb-8 border-b pb-4">
            <h1 id="page-title" class="text-3xl font-bold text-gray-800">Tableau de bord</h1>
        </div>
        
        <div id="content-area">
            <!-- Le contenu sera chargé ici dynamiquement -->
        </div>
    </main>
</div>

<script src="../assets/js/.js"></script>
</body>
</html>