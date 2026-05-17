<?php
/**
 * Bejelentkezés feldolgozás (POST)
 */

$felhasznalonev = trim($_POST['login_nev'] ?? '');
$jelszo = $_POST['jelszo'] ?? '';

if ($felhasznalonev === '' || $jelszo === '') {
    flash('error', 'Kérlek, töltsd ki mindkét mezőt!');
    redirect('belepes');
}

$stmt = $dbh->prepare('SELECT id, felhasznalonev, jelszo, csaladi_nev, utonev, email FROM g_felhasznalok WHERE felhasznalonev = :fnev LIMIT 1');
$stmt->execute([':fnev' => $felhasznalonev]);
$user = $stmt->fetch();

if ($user && password_verify($jelszo, $user['jelszo'])) {
    $_SESSION['user'] = [
        'id' => $user['id'],
        'felhasznalonev' => $user['felhasznalonev'],
        'csaladi_nev' => $user['csaladi_nev'],
        'utonev' => $user['utonev'],
        'email' => $user['email'],
    ];
    flash('success', 'Sikeres bejelentkezés! Üdvözlünk, ' . htmlspecialchars($user['utonev']) . '!');
    redirect('fooldal');
} else {
    flash('error', 'Hibás felhasználónév vagy jelszó!');
    redirect('belepes');
}
