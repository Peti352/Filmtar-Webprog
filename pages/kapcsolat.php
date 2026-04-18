<?php
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);

$nev = $formData['nev'] ?? ($_SESSION['user']['csaladi_nev'] ?? '') . ' ' . ($_SESSION['user']['utonev'] ?? '');
$email = $formData['email'] ?? ($_SESSION['user']['email'] ?? '');
$targy = $formData['targy'] ?? '';
$uzenet = $formData['uzenet'] ?? '';
$nev = trim($nev);
?>

<h1>Kapcsolat</h1>

<?php if (!empty($formErrors)): ?>
    <div class="form-errors">
        <ul>
            <?php foreach ($formErrors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="contact-section">
    <form id="contactForm" action="index.php?page=kapcsolat" method="POST" onsubmit="return validateContactForm()">
        <input type="hidden" name="action" value="contact_submit">

        <div class="form-group">
            <label for="nev">Név:</label>
            <input type="text" name="nev" id="nev" value="<?= htmlspecialchars($nev) ?>">
            <span class="error" id="nev-error"></span>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>">
            <span class="error" id="email-error"></span>
        </div>

        <div class="form-group">
            <label for="targy">Tárgy:</label>
            <input type="text" name="targy" id="targy" value="<?= htmlspecialchars($targy) ?>">
            <span class="error" id="targy-error"></span>
        </div>

        <div class="form-group">
            <label for="uzenet">Üzenet:</label>
            <textarea name="uzenet" id="uzenet" rows="6"><?= htmlspecialchars($uzenet) ?></textarea>
            <span class="error" id="uzenet-error"></span>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Üzenet küldése</button>
        </div>
    </form>
</section>
