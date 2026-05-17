<?php
/**
 * CRUD műveletek feldolgozás (POST)
 */

$action = $_POST['action'] ?? '';

if ($action === 'crud_create') {
    $cim = trim($_POST['cim'] ?? '');
    $rendezo = trim($_POST['rendezo'] ?? '');
    $evRaw = trim($_POST['ev'] ?? '');
    $mufaj = trim($_POST['mufaj'] ?? '');
    $leiras = trim($_POST['leiras'] ?? '');
    $ertekelesRaw = trim($_POST['ertekeles'] ?? '');

    $hibak = [];
    if ($cim === '') $hibak[] = 'A cím megadása kötelező!';
    if ($rendezo === '') $hibak[] = 'A rendező megadása kötelező!';
    if ($mufaj === '') $hibak[] = 'A műfaj megadása kötelező!';
    if ($evRaw === '' || !ctype_digit($evRaw) || (int)$evRaw < 1888 || (int)$evRaw > (int)date('Y') + 5) {
        $hibak[] = 'Az év csak 1888 és ' . ((int)date('Y') + 5) . ' közötti szám lehet!';
    }
    if ($ertekelesRaw !== '' && (!is_numeric($ertekelesRaw) || (float)$ertekelesRaw < 0 || (float)$ertekelesRaw > 10)) {
        $hibak[] = 'Az értékelés csak 0 és 10 közötti szám lehet!';
    }

    if (!empty($hibak)) {
        $_SESSION['form_errors'] = $hibak;
        $_SESSION['form_data'] = $_POST;
        flash('error', 'Kérlek, javítsd a megadott adatokat!');
        redirect('crud?action=uj');
    }

    $ev = (int)$evRaw;
    $ertekeles = $ertekelesRaw === '' ? null : (float)$ertekelesRaw;

    $stmt = $dbh->prepare('INSERT INTO g_filmek (cim, rendezo, ev, mufaj, leiras, ertekeles) VALUES (:cim, :rendezo, :ev, :mufaj, :leiras, :ertekeles)');
    $stmt->execute([':cim' => $cim, ':rendezo' => $rendezo, ':ev' => $ev, ':mufaj' => $mufaj, ':leiras' => $leiras, ':ertekeles' => $ertekeles]);

    flash('success', 'A film sikeresen hozzáadva!');
    redirect('crud');
}

if ($action === 'crud_update') {
    $id = (int)($_POST['id'] ?? 0);
    $cim = trim($_POST['cim'] ?? '');
    $rendezo = trim($_POST['rendezo'] ?? '');
    $evRaw = trim($_POST['ev'] ?? '');
    $mufaj = trim($_POST['mufaj'] ?? '');
    $leiras = trim($_POST['leiras'] ?? '');
    $ertekelesRaw = trim($_POST['ertekeles'] ?? '');

    $hibak = [];
    if ($id <= 0) $hibak[] = 'Érvénytelen film azonosító!';
    if ($cim === '') $hibak[] = 'A cím megadása kötelező!';
    if ($rendezo === '') $hibak[] = 'A rendező megadása kötelező!';
    if ($mufaj === '') $hibak[] = 'A műfaj megadása kötelező!';
    if ($evRaw === '' || !ctype_digit($evRaw) || (int)$evRaw < 1888 || (int)$evRaw > (int)date('Y') + 5) {
        $hibak[] = 'Az év csak 1888 és ' . ((int)date('Y') + 5) . ' közötti szám lehet!';
    }
    if ($ertekelesRaw !== '' && (!is_numeric($ertekelesRaw) || (float)$ertekelesRaw < 0 || (float)$ertekelesRaw > 10)) {
        $hibak[] = 'Az értékelés csak 0 és 10 közötti szám lehet!';
    }

    if (!empty($hibak)) {
        $_SESSION['form_errors'] = $hibak;
        $_SESSION['form_data'] = $_POST;
        flash('error', 'Kérlek, javítsd a megadott adatokat!');
        redirect('crud?action=szerkeszt&id=' . $id);
    }

    $ev = (int)$evRaw;
    $ertekeles = $ertekelesRaw === '' ? null : (float)$ertekelesRaw;

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
