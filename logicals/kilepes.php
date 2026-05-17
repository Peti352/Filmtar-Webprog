<?php
/**
 * Kijelentkezés feldolgozás
 */

$_SESSION = [];
session_destroy();
session_start();
flash('success', 'Sikeresen kijelentkeztél!');
redirect('fooldal');
