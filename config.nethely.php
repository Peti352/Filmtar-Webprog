<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'PASTE_NETHELY_DB_NAME_HERE');
define('DB_USER', 'PASTE_NETHELY_DB_USER_HERE');
define('DB_PASS', 'PASTE_NETHELY_DB_PASSWORD_HERE');
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $dbh = new PDO($dsn, DB_USER, DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Adatbazis kapcsolodasi hiba: ' . $e->getMessage());
}
