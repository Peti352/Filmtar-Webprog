<?php
/**
 * Front Controller - 7a PHP Front-controller tervezési minta (2. Megoldás)
 * Minden kérés ide fut be, az URL rewrite segítségével.
 * URL példa: /kepek → index.php?kepek
 */

session_start();
require __DIR__ . '/includes/config.inc.php';

// === Aktuális oldal meghatározása a QUERY_STRING-ből ===
// URL rewrite: /kepek → index.php?kepek (a query string maga az oldal neve)
$qs = $_SERVER['QUERY_STRING'] ?? '';
// Ha van & jel, csak az első részt vesszük (pl. "crud&action=uj" → "crud")
$page = explode('&', $qs)[0];
// Ha üres, alapértelmezett a főoldal
if ($page === '' || !isset($oldalak[$page])) {
    $page = 'fooldal';
}

// === POST kérések feldolgozása (logicals/) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'login':
            require __DIR__ . '/logicals/belep.php';
            break;
        case 'register':
            require __DIR__ . '/logicals/regisztral.php';
            break;
        case 'contact_submit':
            require __DIR__ . '/logicals/kapcsolat.php';
            break;
        case 'crud_create':
        case 'crud_update':
        case 'crud_delete':
            require __DIR__ . '/logicals/crud.php';
            break;
        case 'image_upload':
            require __DIR__ . '/logicals/kepfeltoltes.php';
            break;
    }
}

// === Kijelentkezés kezelése ===
if ($page === 'kijelentkezes') {
    require __DIR__ . '/logicals/kilepes.php';
}

// === Sablon megjelenítése ===
require __DIR__ . '/templates/index.tpl.php';
