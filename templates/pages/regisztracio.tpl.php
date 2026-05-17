<h1>Regisztráció</h1>

<section class="register-section">
    <form action="index.php" method="POST" class="register-form">
        <input type="hidden" name="action" value="register">

        <div class="form-group">
            <label for="csaladi_nev">Családi név:</label>
            <input type="text" id="csaladi_nev" name="csaladi_nev">
        </div>

        <div class="form-group">
            <label for="utonev">Utónév:</label>
            <input type="text" id="utonev" name="utonev">
        </div>

        <div class="form-group">
            <label for="login_nev">Felhasználónév:</label>
            <input type="text" id="login_nev" name="login_nev">
        </div>

        <div class="form-group">
            <label for="email">E-mail cím:</label>
            <input type="text" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="jelszo">Jelszó:</label>
            <input type="password" id="jelszo" name="jelszo">
        </div>

        <div class="form-group">
            <label for="jelszo_ujra">Jelszó megerősítése:</label>
            <input type="password" id="jelszo_ujra" name="jelszo_ujra">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-success">Regisztráció</button>
        </div>

        <div class="form-footer">
            <p>Már van fiókod? <a href="belepes">Jelentkezz be itt!</a></p>
        </div>
    </form>
</section>
