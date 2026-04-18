<h1>Bejelentkezés</h1>

<section class="login-section">
    <form action="index.php" method="POST" class="login-form">
        <input type="hidden" name="action" value="login">

        <div class="form-group">
            <label for="login_nev">Felhasználónév:</label>
            <input type="text" id="login_nev" name="login_nev" required>
        </div>

        <div class="form-group">
            <label for="jelszo">Jelszó:</label>
            <input type="password" id="jelszo" name="jelszo" required>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Bejelentkezés</button>
        </div>

        <div class="form-footer">
            <p>Még nincs fiókod? <a href="index.php?page=regisztracio">Regisztrálj itt!</a></p>
        </div>
    </form>
</section>
