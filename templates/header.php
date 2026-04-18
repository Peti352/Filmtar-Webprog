<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
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

        <?php if (isset($_SESSION['user'])): ?>
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

    <nav id="main-nav">
        <ul>
            <?php if (isset($_SESSION['user'])): ?>
                <li><a href="index.php?page=fooldal" class="<?= (isset($page) && $page === 'fooldal') ? 'active' : '' ?>">Főoldal</a></li>
                <li><a href="index.php?page=kepek" class="<?= (isset($page) && $page === 'kepek') ? 'active' : '' ?>">Képek</a></li>
                <li><a href="index.php?page=kapcsolat" class="<?= (isset($page) && $page === 'kapcsolat') ? 'active' : '' ?>">Kapcsolat</a></li>
                <li><a href="index.php?page=crud" class="<?= (isset($page) && $page === 'crud') ? 'active' : '' ?>">CRUD</a></li>
                <li><a href="index.php?page=uzenetek" class="<?= (isset($page) && $page === 'uzenetek') ? 'active' : '' ?>">Üzenetek</a></li>
                <li><a href="index.php?page=kijelentkezes" class="<?= (isset($page) && $page === 'kijelentkezes') ? 'active' : '' ?>">Kijelentkezés</a></li>
            <?php else: ?>
                <li><a href="index.php?page=fooldal" class="<?= (isset($page) && $page === 'fooldal') ? 'active' : '' ?>">Főoldal</a></li>
                <li><a href="index.php?page=kepek" class="<?= (isset($page) && $page === 'kepek') ? 'active' : '' ?>">Képek</a></li>
                <li><a href="index.php?page=kapcsolat" class="<?= (isset($page) && $page === 'kapcsolat') ? 'active' : '' ?>">Kapcsolat</a></li>
                <li><a href="index.php?page=crud" class="<?= (isset($page) && $page === 'crud') ? 'active' : '' ?>">CRUD</a></li>
                <li><a href="index.php?page=belepes" class="<?= (isset($page) && $page === 'belepes') ? 'active' : '' ?>">Bejelentkezés</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
