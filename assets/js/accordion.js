// accordion.js

document.addEventListener('DOMContentLoaded', function () {
    initializeAccordion();
});

function initializeAccordion() {
    const accordionElement = document.querySelector('[data-accordion="categories-accordion"]');
    if (!accordionElement) return;

    const accordionOptions = {
        alwaysOpen: false,
        activeClasses: 'bg-indigo-50',
        inactiveClasses: 'bg-[#007AFF]',
        onOpen: (item) => {
            const icon = item.el.querySelector('svg');
            icon.classList.add('rotate-180');
        },
        onClose: (item) => {
            const icon = item.el.querySelector('svg');
            icon.classList.remove('rotate-180');
        },
        onToggle: (item) => {
            console.log('accordion item has been toggled');
            console.log(item);
        },
    };

    new Accordion(accordionElement, accordionOptions);
}