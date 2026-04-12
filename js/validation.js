/**
 * Filmtár - Kapcsolati űrlap kliens oldali validáció
 * Magyar nyelvű hibaüzenetek
 * NEM használ HTML validációs attribútumokat (required, pattern stb.)
 */

/**
 * Kapcsolati űrlap validálása
 * Az űrlap submit eseményénél hívódik meg
 * @returns {boolean} - true ha minden rendben, false ha van hiba
 */
function validateContactForm() {
    // Előző hibaüzenetek törlése
    clearErrors();

    let isValid = true;

    // --- Név mező ellenőrzése ---
    const nevInput = document.getElementById('nev');
    if (nevInput) {
        const nev = nevInput.value.trim();
        if (nev === '') {
            showError('nev', 'A név megadása kötelező!');
            isValid = false;
        } else if (nev.length < 2) {
            showError('nev', 'A név legalább 2 karakter hosszú legyen!');
            isValid = false;
        }
    }

    // --- E-mail cím ellenőrzése ---
    const emailInput = document.getElementById('email');
    if (emailInput) {
        const email = emailInput.value.trim();
        // E-mail formátum regex
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '') {
            showError('email', 'Az e-mail cím megadása kötelező!');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            showError('email', 'Kérjük, adjon meg egy érvényes e-mail címet!');
            isValid = false;
        }
    }

    // --- Tárgy mező ellenőrzése ---
    const targyInput = document.getElementById('targy');
    if (targyInput) {
        const targy = targyInput.value.trim();
        if (targy === '') {
            showError('targy', 'A tárgy megadása kötelező!');
            isValid = false;
        } else if (targy.length < 3) {
            showError('targy', 'A tárgy legalább 3 karakter hosszú legyen!');
            isValid = false;
        }
    }

    // --- Üzenet mező ellenőrzése ---
    const uzenetInput = document.getElementById('uzenet');
    if (uzenetInput) {
        const uzenet = uzenetInput.value.trim();
        if (uzenet === '') {
            showError('uzenet', 'Az üzenet megadása kötelező!');
            isValid = false;
        } else if (uzenet.length < 10) {
            showError('uzenet', 'Az üzenet legalább 10 karakter hosszú legyen!');
            isValid = false;
        }
    }

    // Ha van hiba, megakadályozzuk az űrlap elküldését
    return isValid;
}

/**
 * Hibaüzenet megjelenítése egy adott mező mellett
 * @param {string} fieldId - A mező id attribútuma
 * @param {string} message - A megjelenítendő hibaüzenet
 */
function showError(fieldId, message) {
    const errorSpan = document.getElementById(fieldId + '-error');
    if (errorSpan) {
        errorSpan.textContent = message;
    }

    // Mező vizuális kiemelése hibánál
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '#dc3545';
    }
}

/**
 * Minden korábbi hibaüzenet törlése
 * Visszaállítja a mezők szegélyét is
 */
function clearErrors() {
    // Összes hibaüzenet span tartalmának törlése
    const errorSpans = document.querySelectorAll('span.error');
    errorSpans.forEach(function (span) {
        span.textContent = '';
    });

    // Mezők szegélyének visszaállítása
    const fields = ['nev', 'email', 'targy', 'uzenet'];
    fields.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.borderColor = '';
        }
    });
}
