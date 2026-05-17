<?php
/**
 * Konfiguráció - Oldalak definíció és adatbázis kapcsolat
 * 7a - PHP Front-controller tervezési minta (2. Megoldás)
 */

// === Oldalak tömb ===
// 'menun' => [bejelentkezve_latszik, kijelentkezve_latszik]
$oldalak = [
    'fooldal'       => ['cim' => 'Főoldal',         'menun' => [1, 1]],
    'kepek'         => ['cim' => 'Képek',            'menun' => [1, 1]],
    'kapcsolat'     => ['cim' => 'Kapcsolat',        'menun' => [1, 1]],
    'crud'          => ['cim' => 'CRUD',             'menun' => [1, 1]],
    'uzenetek'      => ['cim' => 'Üzenetek',         'menun' => [1, 0]],
    'belepes'       => ['cim' => 'Bejelentkezés',    'menun' => [0, 1]],
    'regisztracio'  => ['cim' => 'Regisztráció',     'menun' => [0, 0]],
    'kijelentkezes' => ['cim' => 'Kijelentkezés',    'menun' => [1, 0]],
];

// === Adatbázis konfiguráció ===
define('DB_HOST', 'localhost');
define('DB_NAME', 'filmtar');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Adatbázis kapcsolódási hiba: ' . $e->getMessage());
}

// === Segédfüggvények ===
function flash($tipus, $uzenet) {
    $_SESSION['flash'] = ['tipus' => $tipus, 'uzenet' => $uzenet];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function bejelentkezveVan() {
    return isset($_SESSION['user']);
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}
