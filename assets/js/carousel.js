function handleParallaxAnimation() {
    const slides = document.querySelectorAll('.swiper-slide');
    
    slides.forEach(slide => {
        const foreground = slide.querySelector('.slide-foreground');
        
        if (foreground) {
            // Animation continue avec GSAP
            gsap.to(foreground, {
                x: 15, // Augmenté de 5 à 15px
                y: 8, // Augmenté de 3 à 8px
                duration: 4, // Augmenté de 3 à 4 secondes pour un mouvement plus fluide
                ease: "sine.inOut",
                yoyo: true,
                repeat: -1,
                delay: Math.random() * 2 // Délai aléatoire plus important
            });

            // Ajout d'une seconde animation pour plus de dynamisme
            gsap.to(foreground, {
                scale: 1.02, // Léger effet de zoom
                duration: 5,
                ease: "sine.inOut",
                yoyo: true,
                repeat: -1,
                delay: Math.random()
            });
        }
    });
}

const swiper = new Swiper('.swiper-container', {
    speed: 1000,
    effect: 'fade',
    loop: true,
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    on: {
        init: function() {
            handleParallaxAnimation();
        }
    }
}); 