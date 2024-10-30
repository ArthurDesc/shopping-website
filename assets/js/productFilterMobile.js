document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const toggleFilters = document.getElementById('toggleFilters');
    const closeFilters = document.getElementById('closeFilters');
    const applyFilters = document.getElementById('applyFilters');

    toggleFilters?.addEventListener('click', () => {
        filterForm.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    });

    const closeFilterForm = () => {
        filterForm.classList.remove('is-active');
        document.body.style.overflow = '';
    };

    closeFilters?.addEventListener('click', closeFilterForm);
    applyFilters?.addEventListener('click', closeFilterForm);
});    