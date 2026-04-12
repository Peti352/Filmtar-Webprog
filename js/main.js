/**
 * Filmtár - Fő JavaScript fájl
 * Hamburger menü, flash üzenetek, lightbox galéria
 */

document.addEventListener('DOMContentLoaded', function () {

    /* ============================================
       HAMBURGER MENÜ KEZELÉS
       ============================================ */
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mainNav = document.getElementById('main-nav');

    if (hamburgerBtn && mainNav) {
        hamburgerBtn.addEventListener('click', function () {
            // Navigáció és hamburger gomb aktív állapot váltása
            hamburgerBtn.classList.toggle('active');
            mainNav.classList.toggle('active');
        });

        // Menüpont kattintásra bezárjuk a mobilmenüt
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                hamburgerBtn.classList.remove('active');
                mainNav.classList.remove('active');
            });
        });
    }

    /* ============================================
       FLASH ÜZENETEK AUTOMATIKUS ELREJTÉSE
       ============================================ */
    const flashMessages = document.querySelectorAll('.flash-message');

    flashMessages.forEach(function (flash) {
        // Bezárás gomb kezelése
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

        // Automatikus elrejtés 5 másodperc után
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

    /* ============================================
       KÉPGALÉRIA LIGHTBOX
       ============================================ */

    // Lightbox overlay létrehozása, ha van galéria az oldalon
    const galleryItems = document.querySelectorAll('.gallery-item img');

    if (galleryItems.length > 0) {
        // Overlay elem létrehozása
        const overlay = document.createElement('div');
        overlay.classList.add('lightbox-overlay');
        overlay.id = 'lightbox-overlay';

        // Nagyított kép elem
        const lightboxImg = document.createElement('img');
        lightboxImg.alt = 'Nagyított kép';
        overlay.appendChild(lightboxImg);

        document.body.appendChild(overlay);

        // Galéria képekre kattintás: lightbox megnyitása
        galleryItems.forEach(function (img) {
            img.addEventListener('click', function () {
                lightboxImg.src = img.src;
                lightboxImg.alt = img.alt || 'Nagyított kép';
                overlay.classList.add('active');
            });
        });

        // Overlay-re kattintás: lightbox bezárása
        overlay.addEventListener('click', function () {
            overlay.classList.remove('active');
        });

        // ESC billentyű: lightbox bezárása
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                overlay.classList.remove('active');
            }
        });
    }

});
