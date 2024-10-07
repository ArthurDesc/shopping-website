document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      new Swiper('.swiper-container', {
        loop: true,
        slidesPerView: 1,
        autoplay: {
          delay: 5000, // Délai de 5 secondes entre chaque transition
          disableOnInteraction: false, // Continue l'autoplay même après interaction de l'utilisateur
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
      });

      new Swiper('.swiper-container-blocks', {
        loop: true,
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 3,
          },
        },
      });

      new Swiper('.swiper-container-nouveautes', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        breakpoints: {
          640: {
            slidesPerView: 2,
          },
          1024: {
            slidesPerView: 3,
          },
        },
      });
    }, 100);
  });