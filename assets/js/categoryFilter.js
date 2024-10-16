document.addEventListener('DOMContentLoaded', function() {
  const filterInput = document.getElementById('categoryFilter');
  const categoryLinks = document.querySelectorAll('.category-link');

  filterInput.addEventListener('input', function() {
    const filterValue = this.value.toLowerCase();

    categoryLinks.forEach(link => {
      const category = link.getAttribute('data-category');
      if (category.includes(filterValue)) {
        link.style.display = '';
      } else {
        link.style.display = 'none';
      }
    });
  });
});
