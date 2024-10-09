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
    <style>
        .tab-active {
            color: #3490dc;
            border-bottom: 2px solid #3490dc;
        }
    </style>
</head>

<body class="bg-gray-100 flex">
    <!-- Sidebar -->
    <div class="flex flex-col items-center w-16 h-screen overflow-hidden text-gray-700 bg-white border-r">
        <a class="flex items-center justify-center mt-3 w-8 h-8" href="#">
            <img src="<?php echo BASE_URL; ?>assets/images/logoF.png" alt="Logo F" class="w-full h-full object-contain">
        </a>
        <div class="flex flex-col items-center mt-3 border-t border-gray-300">
            <a class="flex items-center justify-center w-12 h-12 mt-2 rounded hover:bg-gray-200" href="#" onclick="loadContent('articles')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>

            </a>
            <a class="flex items-center justify-center w-12 h-12 mt-2 rounded hover:bg-gray-200" href="#" onclick="loadContent('categories')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                </svg>

            </a>
        </div>
        <a class="flex items-center justify-center w-16 h-16 mt-auto bg-gray-200 hover:bg-gray-300" href="#">
            <svg class="w-6 h-6 stroke-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </a>
    </div>

    <!-- Contenu principal -->
    <main class="flex-1 p-4 sm:p-8">
        <div id="content-area" class="max-w-4xl mx-auto">
            <!-- Le contenu sera chargé ici dynamiquement -->
        </div>
    </main>

    <script>
        function loadContent(section) {
            const contentArea = document.getElementById('content-area');
            switch (section) {
                case 'articles':
                    contentArea.innerHTML = `
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400 justify-center">
                            <li class="me-2">
                                <a href="#" onclick="switchTab(this, 'modifier')" class="inline-flex flex-col items-center justify-center p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 group">
                                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                        <path d="M9.625 18.1667H18.25M13.9375 2.35416C14.3187 1.97291 14.8358 1.75873 15.375 1.75873C15.642 1.75873 15.9063 1.81131 16.153 1.91348C16.3996 2.01564 16.6237 2.16538 16.8125 2.35416C17.0013 2.54294 17.151 2.76704 17.2532 3.01369C17.3553 3.26034 17.4079 3.52469 17.4079 3.79166C17.4079 4.05863 17.3553 4.32298 17.2532 4.56963C17.151 4.81628 17.0013 5.04038 16.8125 5.22916L4.83333 17.2083L1 18.1667L1.95833 14.3333L13.9375 2.35416Z" stroke="#007AFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Modifier
                                </a>
                            </li>
                            <li class="me-2">
                                <a href="#" onclick="switchTab(this, 'ajouter')" class="inline-flex flex-col items-center justify-center p-4 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active dark:text-blue-500 dark:border-blue-500 group" aria-current="page">
                                    <svg width="24" height="24" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="mb-2">
                                        <path d="M9 15H11V11H15V9H11V5H9V9H5V11H9V15ZM10 20C8.61667 20 7.31667 19.7417 6.1 19.225C4.88333 18.6917 3.825 17.975 2.925 17.075C2.025 16.175 1.30833 15.1167 0.775 13.9C0.258333 12.6833 0 11.3833 0 10C0 8.61667 0.258333 7.31667 0.775 6.1C1.30833 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.31667 6.1 0.799999C7.31667 0.266666 8.61667 0 10 0C11.3833 0 12.6833 0.266666 13.9 0.799999C15.1167 1.31667 16.175 2.025 17.075 2.925C17.975 3.825 18.6833 4.88333 19.2 6.1C19.7333 7.31667 20 8.61667 20 10C20 11.3833 19.7333 12.6833 19.2 13.9C18.6833 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6917 13.9 19.225C12.6833 19.7417 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="#007AFF"/>
                                    </svg>
                                    Ajouter
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div id="tab-content" class="mt-4">
                        <!-- Le contenu des onglets sera chargé ici -->
                    </div>
                    `;
                    switchTab(document.querySelector('[aria-current="page"]'), 'ajouter');
                    break;
                case 'categories':
                    contentArea.innerHTML = '<h1 class="text-xl sm:text-2xl font-bold">Gestion des catégories</h1>';
                    break;
                default:
                    contentArea.innerHTML = '<p>Sélectionnez une option dans la barre latérale</p>';
            }
        }

        function switchTab(clickedTab, tabId) {
            document.querySelectorAll('.border-b-2').forEach(tab => {
                tab.classList.remove('text-blue-600', 'border-blue-600', 'active', 'dark:text-blue-500', 'dark:border-blue-500');
                tab.classList.add('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
            });
            clickedTab.classList.remove('border-transparent', 'hover:text-gray-600', 'hover:border-gray-300');
            clickedTab.classList.add('text-blue-600', 'border-blue-600', 'active', 'dark:text-blue-500', 'dark:border-blue-500');
            
            const tabContent = document.getElementById('tab-content');
            switch(tabId) {
                case 'modifier':
                    tabContent.innerHTML = '<h2 class="text-lg font-semibold">Profil</h2><p>Contenu du profil...</p>';
                    break;
                case 'ajouter':
                    tabContent.innerHTML = '<h2 class="text-lg font-semibold">Tableau de bord</h2><p>Contenu du tableau de bord...</p>';
                    break;
                default:
                    tabContent.innerHTML = '<p>Contenu non disponible</p>';
            }
        }

        loadContent('articles');
    </script>
</body>

</html>