<?php
/**
 * Kapcsolat űrlap feldolgozás (POST)
 */

$nev = trim($_POST['nev'] ?? '');
$email = trim($_POST['email'] ?? '');
$targy = trim($_POST['targy'] ?? '');
$uzenet = trim($_POST['uzenet'] ?? '');

$hibak = [];

if ($nev === '') {
    $hibak[] = 'A név megadása kötelező!';
} elseif (strlen($nev) < 2) {
    $hibak[] = 'A név legalább 2 karakter hosszú legyen!';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $hibak[] = 'Érvényes e-mail cím megadása kötelező!';
}

if ($targy === '') {
    $hibak[] = 'A tárgy megadása kötelező!';
} elseif (strlen($targy) < 3) {
    $hibak[] = 'A tárgy legalább 3 karakter hosszú legyen!';
}

if ($uzenet === '') {
    $hibak[] = 'Az üzenet megadása kötelező!';
} elseif (strlen($uzenet) < 10) {
    $hibak[] = 'Az üzenet legalább 10 karakter hosszú legyen!';
}

if (!empty($hibak)) {
    $_SESSION['form_errors'] = $hibak;
    $_SESSION['form_data'] = ['nev' => $nev, 'email' => $email, 'targy' => $targy, 'uzenet' => $uzenet];
    redirect('kapcsolat');
}

$kuldo_id = bejelentkezveVan() ? $_SESSION['user']['id'] : null;
$stmt = $dbh->prepare('INSERT INTO g_uzenetek (nev, email, targy, uzenet, kuldo_id, kuldve) VALUES (:nev, :email, :targy, :uzenet, :kuldo_id, NOW())');
$stmt->execute([':nev' => $nev, ':email' => $email, ':targy' => $targy, ':uzenet' => $uzenet, ':kuldo_id' => $kuldo_id]);

flash('success', 'Üzeneted sikeresen elküldtük! Hamarosan válaszolunk.');
redirect('kapcsolat');
