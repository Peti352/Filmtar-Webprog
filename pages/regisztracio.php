<?php
/**
 * regisztracio.php - Regisztracios oldal
 *
 * Ez az oldal jeleníti meg a regisztracios urlapot.
 * A regisztracios logika (adatok mentese, validacio)
 * az index.php front controllerben tortenik; ez az oldal
 * csak a megjelenítesert felelos.
 *
 * Hasznalt session valtozok:
 *   - $_SESSION['user']             : Bejelentkezett felhasznalo adatai
 *   - $_SESSION['register_errors']  : Validacios hibak tombje
 *   - $_SESSION['form_data']        : Korabban megadott urlapadatok
 */

// --- Ha a felhasznalo mar be van jelentkezve ---
if (isset($_SESSION['user'])):
?>
    <section class="auth-section">
        <h1>Regisztráció</h1>
        <div class="alert alert-info">
            <p>Már be van jelentkezve!</p>
            <p>Üdvözöljük, <strong><?= htmlspecialchars($_SESSION['user']['csaladi_nev']) ?> <?= htmlspecialchars($_SESSION['user']['utonev']) ?></strong>!</p>
        </div>
        <a href="index.php?page=fooldal" class="btn btn-primary">Vissza a főoldalra</a>
    </section>

<?php
    return; // Ne jelenitsuk meg az urlapot
endif;

// Korabbi urlapadatok betoltese (validacios hiba utan)
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);
?>

<section class="auth-section">
    <h1>Regisztráció</h1>

    <!-- Regisztracios urlap -->
    <form action="index.php?page=regisztracio" method="POST" class="auth-form">
        <!-- Rejtett mezo: a vegrehajtando muvelet azonositoja -->
        <input type="hidden" name="action" value="register">

        <div class="form-group">
            <label for="csaladi_nev">Családi név <span class="required">*</span></label>
            <input type="text" id="csaladi_nev" name="csaladi_nev" required
                   value="<?= htmlspecialchars($formData['csaladi_nev'] ?? '') ?>"
                   placeholder="Pl. Kovács">
        </div>

        <div class="form-group">
            <label for="utonev">Utónév <span class="required">*</span></label>
            <input type="text" id="utonev" name="utonev" required
                   value="<?= htmlspecialchars($formData['utonev'] ?? '') ?>"
                   placeholder="Pl. János">
        </div>

        <div class="form-group">
            <label for="login_nev">Felhasználónév <span class="required">*</span></label>
            <input type="text" id="login_nev" name="login_nev" required
                   autocomplete="username"
                   value="<?= htmlspecialchars($formData['login_nev'] ?? '') ?>"
                   placeholder="Pl. kovacs.janos">
        </div>

        <div class="form-group">
            <label for="email">E-mail <span class="required">*</span></label>
            <input type="email" id="email" name="email" required
                   autocomplete="email"
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                   placeholder="Pl. kovacs.janos@pelda.hu">
        </div>

        <div class="form-group">
            <label for="jelszo">Jelszó <span class="required">*</span></label>
            <input type="password" id="jelszo" name="jelszo" required
                   autocomplete="new-password"
                   placeholder="Legalább 6 karakter">
        </div>

        <div class="form-group">
            <label for="jelszo_ujra">Jelszó újra <span class="required">*</span></label>
            <input type="password" id="jelszo_ujra" name="jelszo_ujra" required
                   autocomplete="new-password"
                   placeholder="Jelszó megerősítése">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Regisztráció</button>
        </div>
    </form>

    <!-- Bejelentkezesre mutato link -->
    <p class="auth-link">
        Már van fiókja?
        <a href="index.php?page=belepes">Jelentkezzen be!</a>
    </p>
</section>
