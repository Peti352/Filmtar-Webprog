<?php
/**
 * Képfeltöltés feldolgozás (POST)
 */

if (!bejelentkezveVan()) {
    flash('error', 'Képfeltöltéshez be kell jelentkezned!');
    redirect('belepes');
}

if (!isset($_FILES['kep']) || $_FILES['kep']['error'] !== UPLOAD_ERR_OK) {
    flash('error', 'Hiba történt a fájl feltöltése közben!');
    redirect('kepek');
}

$fajl = $_FILES['kep'];
$engedelyezett_tipusok = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$engedelyezett_kiterjesztesek = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$fajl_tipus = mime_content_type($fajl['tmp_name']);
$kiterjesztes = strtolower(pathinfo($fajl['name'], PATHINFO_EXTENSION));

if (!in_array($fajl_tipus, $engedelyezett_tipusok) || !in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {
    flash('error', 'Csak JPG, PNG, GIF és WebP formátumú képek engedélyezettek!');
    redirect('kepek');
}

$uj_fajlnev = uniqid('kep_', true) . '.' . $kiterjesztes;
$cel_utvonal = __DIR__ . '/../uploads/' . $uj_fajlnev;

if (move_uploaded_file($fajl['tmp_name'], $cel_utvonal)) {
    $stmt = $dbh->prepare('INSERT INTO g_kepek (fajlnev, eredeti_nev, feltolto_id, feltoltve) VALUES (:fajlnev, :eredeti_nev, :feltolto_id, NOW())');
    $stmt->execute([':fajlnev' => $uj_fajlnev, ':eredeti_nev' => $fajl['name'], ':feltolto_id' => $_SESSION['user']['id']]);
    flash('success', 'A kép sikeresen feltöltve!');
} else {
    flash('error', 'Hiba történt a fájl mentése közben!');
}
redirect('kepek');
