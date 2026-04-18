<h1>Regisztráció</h1>

<section class="register-section">
    <form action="index.php" method="POST" class="register-form">
        <input type="hidden" name="action" value="register">

        <div class="form-group">
            <label for="csaladi_nev">Családi név:</label>
            <input type="text" id="csaladi_nev" name="csaladi_nev" required>
        </div>

        <div class="form-group">
            <label for="utonev">Utónév:</label>
            <input type="text" id="utonev" name="utonev" required>
        </div>

        <div class="form-group">
            <label for="login_nev">Felhasználónév:</label>
            <input type="text" id="login_nev" name="login_nev" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail cím:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="jelszo">Jelszó:</label>
            <input type="password" id="jelszo" name="jelszo" required minlength="6">
        </div>

        <div class="form-group">
            <label for="jelszo_ujra">Jelszó megerősítése:</label>
            <input type="password" id="jelszo_ujra" name="jelszo_ujra" required minlength="6">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">Regisztráció</button>
        </div>

        <div class="form-footer">
            <p>Már van fiókod? <a href="index.php?page=belepes">Jelentkezz be itt!</a></p>
        </div>
    </form>
</section>
