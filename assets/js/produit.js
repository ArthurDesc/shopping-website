document.addEventListener('DOMContentLoaded', function() {
    const categoriesFilter = document.getElementById('categories-filter');
    const toggleButton = document.getElementById('categories-toggle');
    const filterContent = document.getElementById('categories-content');
    const searchInput = document.getElementById('categories-search');
    const categoriesList = document.getElementById('categories-list');

    // Toggle du dropdown
    toggleButton.addEventListener('click', function() {
        if (filterContent.style.display === 'none' || filterContent.style.display === '') {
            filterContent.style.display = 'block';
            toggleButton.querySelector('svg').classList.add('rotate-180');
        } else {
            filterContent.style.display = 'none';
            toggleButton.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Fonction de recherche
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const categories = categoriesList.querySelectorAll('.mb-2');

        categories.forEach(category => {
            const categoryName = category.querySelector('label').textContent.toLowerCase();
            const subCategories = category.querySelectorAll('.ml-4 .flex');
            let shouldShow = categoryName.includes(searchTerm);

            subCategories.forEach(subCategory => {
                const subCategoryName = subCategory.querySelector('label').textContent.toLowerCase();
                if (subCategoryName.includes(searchTerm)) {
                    shouldShow = true;
                    subCategory.style.display = 'flex';
                } else {
                    subCategory.style.display = 'none';
                }
            });

            category.style.display = shouldShow ? 'block' : 'none';
        });
    });

    // Nouvelle partie pour les marques
    const marquesFilter = document.getElementById('marques-filter');
    const marquesToggle = document.getElementById('marques-toggle');
    const marquesContent = document.getElementById('marques-content');
    const marquesSearch = document.getElementById('marques-search');
    const marquesList = document.getElementById('marques-list');

    // Toggle du dropdown des marques
    marquesToggle.addEventListener('click', function() {
        if (marquesContent.style.display === 'none' || marquesContent.style.display === '') {
            marquesContent.style.display = 'block';
            marquesToggle.querySelector('svg').classList.add('rotate-180');
        } else {
            marquesContent.style.display = 'none';
            marquesToggle.querySelector('svg').classList.remove('rotate-180');
        }
    });

    // Fonction de recherche pour les marques
    marquesSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const marques = marquesList.querySelectorAll('.flex');

        marques.forEach(marque => {
            const marqueName = marque.querySelector('label').textContent.toLowerCase();
            if (marqueName.includes(searchTerm)) {
                marque.style.display = 'flex';
            } else {
                marque.style.display = 'none';
            }
        });
    });

    // Ajouter après la gestion du dropdown des catégories existant
    const sportsFilter = document.getElementById('sports-filter');
    const sportsToggle = document.getElementById('sports-toggle');
    const sportsContent = document.getElementById('sports-content');

    sportsToggle.addEventListener('click', function() {
        if (sportsContent.style.display === 'none' || sportsContent.style.display === '') {
            sportsContent.style.display = 'block';
            sportsToggle.querySelector('svg').classList.add('rotate-180');
        } else {
            sportsContent.style.display = 'none';
            sportsToggle.querySelector('svg').classList.remove('rotate-180');
        }
    });
});
