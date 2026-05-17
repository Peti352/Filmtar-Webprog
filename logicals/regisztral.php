<?php
/**
 * Regisztráció feldolgozás (POST)
 */

$csaladi_nev = trim($_POST['csaladi_nev'] ?? '');
$utonev = trim($_POST['utonev'] ?? '');
$felhasznalonev = trim($_POST['login_nev'] ?? '');
$email = trim($_POST['email'] ?? '');
$jelszo = $_POST['jelszo'] ?? '';
$jelszo2 = $_POST['jelszo_ujra'] ?? '';

if ($csaladi_nev === '' || $utonev === '' || $felhasznalonev === '' || $email === '' || $jelszo === '') {
    flash('error', 'Minden mező kitöltése kötelező!');
    redirect('regisztracio');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', 'Érvénytelen e-mail cím formátum!');
    redirect('regisztracio');
}

if ($jelszo !== $jelszo2) {
    flash('error', 'A két jelszó nem egyezik!');
    redirect('regisztracio');
}

if (strlen($jelszo) < 6) {
    flash('error', 'A jelszónak legalább 6 karakter hosszúnak kell lennie!');
    redirect('regisztracio');
}

$stmt = $dbh->prepare('SELECT id FROM g_felhasznalok WHERE felhasznalonev = :fnev LIMIT 1');
$stmt->execute([':fnev' => $felhasznalonev]);
if ($stmt->fetch()) {
    flash('error', 'Ez a felhasználónév már foglalt!');
    redirect('regisztracio');
}

$hashelt_jelszo = password_hash($jelszo, PASSWORD_DEFAULT);

$stmt = $dbh->prepare('INSERT INTO g_felhasznalok (csaladi_nev, utonev, felhasznalonev, email, jelszo) VALUES (:cnev, :unev, :fnev, :email, :jelszo)');
$stmt->execute([
    ':cnev' => $csaladi_nev,
    ':unev' => $utonev,
    ':fnev' => $felhasznalonev,
    ':email' => $email,
    ':jelszo' => $hashelt_jelszo,
]);

flash('success', 'Sikeres regisztráció! Most már bejelentkezhetsz.');
redirect('belepes');
