<?php
/**
 * config.php - Adatbazis konfiguracio
 *
 * PDO kapcsolat a MySQL adatbazishoz.
 * Helyi fejleszteshez az alapertelmezett ertekek:
 *   host     = localhost
 *   dbname   = filmtar
 *   user     = root
 *   password = ""
 *
 * Eles kornyezetben (tarhelyen) modositsd az alabbi konstansokat
 * a szolgaltato altal megadott adatokra!
 */

// --- Adatbazis beallitasok ---------------------------------------------------
// Modositsd ezeket az ertekeket, ha mas szerverre telepited az alkalmazast.
define('DB_HOST', 'localhost');   // Adatbazis szerver cime
define('DB_NAME', 'filmtar');     // Adatbazis neve
define('DB_USER', 'root');        // Felhasznalonev
define('DB_PASS', '');            // Jelszo (helyi fejlesztesnel ures)
define('DB_CHARSET', 'utf8mb4');  // Karakterkeszlet - UTF-8 tamogatas

// --- PDO kapcsolat letrehozasa -----------------------------------------------
try {
    // DSN (Data Source Name) osszeallitasa
    $dsn = 'mysql:host=' . DB_HOST
         . ';dbname='    . DB_NAME
         . ';charset='   . DB_CHARSET;

    // PDO peldany letrehozasa a megadott adatokkal
    $dbh = new PDO($dsn, DB_USER, DB_PASS);

    // Hibakezelesi mod: kivetel dobasa hiba eseten
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Alapertelmezett fetch mod: asszociativ tomb
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Emulalt prepared statementek kikapcsolasa (biztonsagosabb)
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Hiba eseten leallitjuk az alkalmazast
    die('Adatbazis kapcsolodasi hiba: ' . $e->getMessage());
}
