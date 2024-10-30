document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const toggleFilters = document.getElementById('toggleFilters');
    const closeFilters = document.getElementById('closeFilters');

    toggleFilters?.addEventListener('click', () => {
        filterForm.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    });

    closeFilters?.addEventListener('click', () => {
        filterForm.classList.remove('is-active');
        document.body.style.overflow = '';
    });
});    