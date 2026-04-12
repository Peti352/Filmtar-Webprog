<?php
/**
 * kapcsolat.php - Kapcsolat oldal
 *
 * Kapcsolatfelvételi űrlap. A validáció JavaScript és PHP oldalon történik,
 * HTML5 required/pattern attribútumok NÉLKÜL.
 *
 * Elérhető változók:
 *   $dbh      - PDO adatbázis-kapcsolat
 *   $_SESSION - Munkamenet adatok
 *
 * Session változók:
 *   $_SESSION['form_errors'] - Szerver oldali validációs hibák tömbje
 *   $_SESSION['form_data']   - Korábbi űrlapadatok (hibás kitöltés esetén)
 */

// Szerver oldali validációs hibák és korábbi adatok kiolvasása
$formErrors = $_SESSION['form_errors'] ?? [];
$formData   = $_SESSION['form_data'] ?? [];

// Hibák és adatok törlése a session-ből (egyszeri megjelenítés)
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);

// Alapértelmezett értékek meghatározása
// Ha van korábbi adat (hibás kitöltés után), azt használjuk; ha nincs, bejelentkezett felhasználó adatait
$nev    = $formData['nev']    ?? ($_SESSION['user']['csaladi_nev'] ?? '') . ' ' . ($_SESSION['user']['utonev'] ?? '');
$email  = $formData['email']  ?? ($_SESSION['user']['email'] ?? '');
$targy  = $formData['targy']  ?? '';
$uzenet = $formData['uzenet'] ?? '';

// Név mező trimmelése (ha csak szóköz maradt az összefűzésből)
$nev = trim($nev);
?>

<h1>Kapcsolat</h1>

<!-- Szerver oldali validációs hibák megjelenítése -->
<?php if (!empty($formErrors)): ?>
    <div class="form-errors">
        <ul>
            <?php foreach ($formErrors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Kapcsolatfelvételi űrlap -->
<section class="contact-section">
    <form id="contactForm" action="index.php?page=kapcsolat" method="POST" onsubmit="return validateContactForm()">
        <input type="hidden" name="action" value="contact_submit">

        <!-- Név mező -->
        <div class="form-group">
            <label for="nev">Név:</label>
            <input type="text" name="nev" id="nev" value="<?= htmlspecialchars($nev) ?>">
            <span class="error" id="nev-error"></span>
        </div>

        <!-- E-mail mező (type="text" a HTML5 validáció elkerülése érdekében) -->
        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>">
            <span class="error" id="email-error"></span>
        </div>

        <!-- Tárgy mező -->
        <div class="form-group">
            <label for="targy">Tárgy:</label>
            <input type="text" name="targy" id="targy" value="<?= htmlspecialchars($targy) ?>">
            <span class="error" id="targy-error"></span>
        </div>

        <!-- Üzenet mező -->
        <div class="form-group">
            <label for="uzenet">Üzenet:</label>
            <textarea name="uzenet" id="uzenet" rows="6"><?= htmlspecialchars($uzenet) ?></textarea>
            <span class="error" id="uzenet-error"></span>
        </div>

        <!-- Küldés gomb -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Üzenet küldése</button>
        </div>
    </form>
</section>
