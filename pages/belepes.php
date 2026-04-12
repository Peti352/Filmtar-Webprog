<?php
/**
 * belepes.php - Bejelentkezesi oldal
 *
 * Ez az oldal jeleníti meg a bejelentkezesi urlapot.
 * A bejelentkezesi logika (felhasznalonev/jelszo ellenorzes)
 * az index.php front controllerben tortenik; ez az oldal
 * csak a megjelenítesert felelos.
 *
 * Hasznalt session valtozok:
 *   - $_SESSION['user']         : Bejelentkezett felhasznalo adatai
 *   - $_SESSION['login_error']  : Hibauzenet sikertelen bejelentkezes eseten
 */

// --- Ha a felhasznalo mar be van jelentkezve ---
if (isset($_SESSION['user'])):
?>
    <section class="auth-section">
        <h1>Bejelentkezés</h1>
        <div class="alert alert-info">
            <p>Már be van jelentkezve!</p>
            <p>Üdvözöljük, <strong><?= htmlspecialchars($_SESSION['user']['csaladi_nev']) ?> <?= htmlspecialchars($_SESSION['user']['utonev']) ?></strong>!</p>
        </div>
        <a href="index.php?page=fooldal" class="btn btn-primary">Vissza a főoldalra</a>
    </section>

<?php
    return; // Ne jelenitsuk meg az urlapot
endif;
?>

<section class="auth-section">
    <h1>Bejelentkezés</h1>

    <!-- Bejelentkezesi urlap -->
    <form action="index.php?page=belepes" method="POST" class="auth-form">
        <!-- Rejtett mezo: a vegrehajtando muvelet azonositoja -->
        <input type="hidden" name="action" value="login">

        <div class="form-group">
            <label for="login_nev">Felhasználónév</label>
            <input type="text" id="login_nev" name="login_nev" required
                   autocomplete="username"
                   placeholder="Adja meg a felhasználónevét">
        </div>

        <div class="form-group">
            <label for="jelszo">Jelszó</label>
            <input type="password" id="jelszo" name="jelszo" required
                   autocomplete="current-password"
                   placeholder="Adja meg a jelszavát">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Bejelentkezés</button>
        </div>
    </form>

    <!-- Regisztraciora mutato link -->
    <p class="auth-link">
        Még nincs fiókja?
        <a href="index.php?page=regisztracio">Regisztráljon!</a>
    </p>
</section>
