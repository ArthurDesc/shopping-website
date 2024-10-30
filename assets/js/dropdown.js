
function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('.filter-dropdown');
    const button = dropdown.previousElementSibling;
    const arrow = button.querySelector('svg');
    
    // Fermer tous les autres dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== dropdownId && !d.classList.contains('hidden')) {
            d.classList.add('hidden');
            const otherArrow = d.previousElementSibling.querySelector('svg');
            otherArrow.style.transform = 'rotate(0deg)';
        }
    });
    
    // Toggle le dropdown actuel
    dropdown.classList.toggle('hidden');
    
    // Rotation de la flèche
    if (dropdown.classList.contains('hidden')) {
        arrow.style.transform = 'rotate(0deg)';
    } else {
        arrow.style.transform = 'rotate(180deg)';
    }
}

// Fermer les dropdowns si on clique en dehors
document.addEventListener('click', function(event) {
    const dropdowns = document.querySelectorAll('.filter-dropdown');
    const buttons = document.querySelectorAll('.filter-section button');
    
    let clickedOutside = true;
    
    buttons.forEach(button => {
        if (button.contains(event.target)) {
            clickedOutside = false;
        }
    });
    
    dropdowns.forEach(dropdown => {
        if (dropdown.contains(event.target)) {
            clickedOutside = false;
        }
    });
    
    if (clickedOutside) {
        dropdowns.forEach(dropdown => {
            dropdown.classList.add('hidden');
            const arrow = dropdown.previousElementSibling.querySelector('svg');
            arrow.style.transform = 'rotate(0deg)';
        });
    }
});
