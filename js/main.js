document.addEventListener('DOMContentLoaded', function () {

    // hamburger menu
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mainNav = document.getElementById('main-nav');

    if (hamburgerBtn && mainNav) {
        hamburgerBtn.addEventListener('click', function () {
            hamburgerBtn.classList.toggle('active');
            mainNav.classList.toggle('active');
        });

        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                hamburgerBtn.classList.remove('active');
                mainNav.classList.remove('active');
            });
        });
    }

    // flash messages auto hide
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(function (flash) {
        const closeBtn = flash.querySelector('.flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                flash.style.animation = 'none';
                flash.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(function () {
                    flash.remove();
                }, 400);
            });
        }

        setTimeout(function () {
            if (flash && flash.parentNode) {
                flash.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                flash.style.opacity = '0';
                flash.style.transform = 'translateY(-10px)';
                setTimeout(function () {
                    flash.remove();
                }, 400);
            }
        }, 5000);
    });

    // gallery lightbox
    const galleryItems = document.querySelectorAll('.gallery-item img');

    if (galleryItems.length > 0) {
        const overlay = document.createElement('div');
        overlay.classList.add('lightbox-overlay');
        overlay.id = 'lightbox-overlay';

        const lightboxImg = document.createElement('img');
        lightboxImg.alt = 'Nagyított kép';
        overlay.appendChild(lightboxImg);

        document.body.appendChild(overlay);

        galleryItems.forEach(function (img) {
            img.addEventListener('click', function () {
                lightboxImg.src = img.src;
                overlay.classList.add('visible');
            });
        });

        overlay.addEventListener('click', function () {
            overlay.classList.remove('visible');
        });
    }

});
