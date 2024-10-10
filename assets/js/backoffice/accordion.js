// accordion.js

document.addEventListener('DOMContentLoaded', function () {
    const accordionElement = document.querySelector('[data-accordion="categories-accordion"]');
    const accordionOptions = {
        // Options de l'accordéon
    };
    const accordion = new Accordion(accordionElement, accordionOptions);
});