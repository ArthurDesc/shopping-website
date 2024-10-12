document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menu-toggle');

    // Vérifie si le clic est à l'extérieur de la barre latérale et du bouton de menu
    if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
      sidebar.classList.add('-translate-x-full');
    }
  });

  document.getElementById('menu-toggle').addEventListener('click', function(event) {
    event.stopPropagation(); // Empêche le clic de se propager au document
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('-translate-x-full');
  });

  document.addEventListener('DOMContentLoaded', function() {
    var swiper = new Swiper('.swiper-container', {
      loop: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    });

    // Ajoutez cette nouvelle fonction pour gérer le scroll
    function handleScroll() {
      const header = document.getElementById('main-header');
      let lastScrollTop = 0;

      window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop) {
          // Scroll vers le bas
          header.style.transform = 'translateY(-100%)';
        } else {
          // Scroll vers le haut
          header.style.transform = 'translateY(0)';
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
      }, false);
    }

    // Appelez la fonction au chargement du document
    handleScroll();
  });

  const toggles = ['menu-homme', 'menu-femme', 'menu-enfants', 'menu-sports'];

  toggles.forEach(toggle => {
    document.getElementById(`${toggle}-toggle`).addEventListener('click', function(event) {
      event.preventDefault();
      document.getElementById(toggle).classList.toggle('hidden');
      this.querySelector('svg').classList.toggle('rotate-180');
    });
  });
