document.addEventListener('DOMContentLoaded', function() {
    // Sélectionner tous les dropdowns
    const dropdownContainers = document.querySelectorAll('.filter-section');
    let activeDropdown = null;

    dropdownContainers.forEach(container => {
        const toggle = container.querySelector('[id$="-toggle"]');
        const content = container.querySelector('[id$="-content"]');
        
        if (!toggle || !content) return;

        // Initialiser tous les dropdowns comme fermés
        content.style.display = 'none';
        
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Si ce dropdown est déjà actif, le fermer
            if (activeDropdown === content) {
                content.style.display = 'none';
                toggle.querySelector('svg').classList.remove('rotate-180');
                activeDropdown = null;
                return;
            }
            
            // Fermer le dropdown actif précédent
            if (activeDropdown) {
                activeDropdown.style.display = 'none';
                activeDropdown.previousElementSibling.querySelector('svg').classList.remove('rotate-180');
            }
            
            // Ouvrir le nouveau dropdown
            content.style.display = 'block';
            toggle.querySelector('svg').classList.add('rotate-180');
            activeDropdown = content;
        });
    });

    // Fermer le dropdown actif si on clique en dehors
    document.addEventListener('click', function(e) {
        if (activeDropdown && !e.target.closest('.filter-section')) {
            const toggle = activeDropdown.previousElementSibling;
            activeDropdown.style.display = 'none';
            toggle.querySelector('svg').classList.remove('rotate-180');
            activeDropdown = null;
        }
    });
});
