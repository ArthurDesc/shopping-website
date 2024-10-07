document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
      new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
          delay: 3000,
          disableOnInteraction: false,
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
      });
    }, 100);
  });