function validateContactForm() {
    clearErrors();
    let isValid = true;

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

    const emailInput = document.getElementById('email');
    if (emailInput) {
        const email = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email === '') {
            showError('email', 'Az e-mail cím megadása kötelező!');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            showError('email', 'Kérjük, adjon meg egy érvényes e-mail címet!');
            isValid = false;
        }
    }

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

    return isValid;
}

function showError(fieldId, message) {
    const errorSpan = document.getElementById(fieldId + '-error');
    if (errorSpan) {
        errorSpan.textContent = message;
    }
    const field = document.getElementById(fieldId);
    if (field) {
        field.style.borderColor = '#dc3545';
    }
}

function clearErrors() {
    const errorSpans = document.querySelectorAll('span.error');
    errorSpans.forEach(function (span) {
        span.textContent = '';
    });

    const fields = ['nev', 'email', 'targy', 'uzenet'];
    fields.forEach(function (fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.borderColor = '';
        }
    });
}
