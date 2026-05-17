<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filmtár</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="header-content">
        <h1 class="site-title">Filmtár</h1>

        <?php if (bejelentkezveVan()): ?>
            <div class="user-info">
                Bejelentkezett: <?= htmlspecialchars($_SESSION['user']['csaladi_nev']) ?> <?= htmlspecialchars($_SESSION['user']['utonev']) ?> (<?= htmlspecialchars($_SESSION['user']['felhasznalonev']) ?>)
            </div>
        <?php endif; ?>

        <button class="hamburger" id="hamburger-btn" aria-label="Menü megnyitása">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>

    <!-- Dinamikus menü az $oldalak tömbből -->
    <nav id="main-nav">
        <ul>
            <?php
            // menun[0] = bejelentkezve látszik, menun[1] = kijelentkezve látszik
            $bejelentkezve = bejelentkezveVan() ? 0 : 1;
            foreach ($oldalak as $kulcs => $oldal):
                if ($oldal['menun'][$bejelentkezve]):
            ?>
                <li><a href="<?= $kulcs ?>" class="<?= ($page === $kulcs) ? 'active' : '' ?>"><?= htmlspecialchars($oldal['cim']) ?></a></li>
            <?php
                endif;
            endforeach;
            ?>
        </ul>
    </nav>
</header>

<main>

<?php
// Flash üzenet megjelenítése
$flash = getFlash();
if ($flash !== null): ?>
    <div class="flash-message flash-<?= htmlspecialchars($flash['tipus']) ?>">
        <?= htmlspecialchars($flash['uzenet']) ?>
    </div>
<?php endif; ?>

<?php
// Oldal tartalom betöltése
$tpl_fajl = __DIR__ . '/pages/' . $page . '.tpl.php';
if (file_exists($tpl_fajl)) {
    require $tpl_fajl;
} else {
    echo '<p class="error-message">Az oldal nem található!</p>';
}
?>

</main>

<footer>
    <p>&copy; 2026 Filmtár. Minden jog fenntartva.</p>
</footer>

<script src="js/main.js"></script>
<script src="js/validation.js"></script>
</body>
</html>
