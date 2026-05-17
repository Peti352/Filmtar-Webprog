<?php
/**
 * CRUD műveletek feldolgozás (POST)
 */

$action = $_POST['action'] ?? '';

if ($action === 'crud_create') {
    $cim = trim($_POST['cim'] ?? '');
    $rendezo = trim($_POST['rendezo'] ?? '');
    $ev = (int)($_POST['ev'] ?? 0);
    $mufaj = trim($_POST['mufaj'] ?? '');
    $leiras = trim($_POST['leiras'] ?? '');
    $ertekeles = isset($_POST['ertekeles']) ? (float)$_POST['ertekeles'] : null;

    if ($cim === '' || $rendezo === '' || $ev <= 0) {
        flash('error', 'A cím, rendező és év mezők kitöltése kötelező!');
        redirect('crud?action=uj');
    }

    $stmt = $dbh->prepare('INSERT INTO g_filmek (cim, rendezo, ev, mufaj, leiras, ertekeles) VALUES (:cim, :rendezo, :ev, :mufaj, :leiras, :ertekeles)');
    $stmt->execute([':cim' => $cim, ':rendezo' => $rendezo, ':ev' => $ev, ':mufaj' => $mufaj, ':leiras' => $leiras, ':ertekeles' => $ertekeles]);

    flash('success', 'A film sikeresen hozzáadva!');
    redirect('crud');
}

if ($action === 'crud_update') {
    $id = (int)($_POST['id'] ?? 0);
    $cim = trim($_POST['cim'] ?? '');
    $rendezo = trim($_POST['rendezo'] ?? '');
    $ev = (int)($_POST['ev'] ?? 0);
    $mufaj = trim($_POST['mufaj'] ?? '');
    $leiras = trim($_POST['leiras'] ?? '');
    $ertekeles = isset($_POST['ertekeles']) ? (float)$_POST['ertekeles'] : null;

    if ($id <= 0 || $cim === '' || $rendezo === '' || $ev <= 0) {
        flash('error', 'A cím, rendező és év mezők kitöltése kötelező!');
        redirect('crud?action=szerkeszt&id=' . $id);
    }

    $stmt = $dbh->prepare('UPDATE g_filmek SET cim = :cim, rendezo = :rendezo, ev = :ev, mufaj = :mufaj, leiras = :leiras, ertekeles = :ertekeles WHERE id = :id');
    $stmt->execute([':cim' => $cim, ':rendezo' => $rendezo, ':ev' => $ev, ':mufaj' => $mufaj, ':leiras' => $leiras, ':ertekeles' => $ertekeles, ':id' => $id]);

    flash('success', 'A film adatai sikeresen frissítve!');
    redirect('crud');
}

if ($action === 'crud_delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $dbh->prepare('DELETE FROM g_filmek WHERE id = :id');
        $stmt->execute([':id' => $id]);
        flash('success', 'A film sikeresen törölve!');
    } else {
        flash('error', 'Érvénytelen film azonosító!');
    }
    redirect('crud');
}
