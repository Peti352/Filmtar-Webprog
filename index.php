<?php
/**
 * index.php - Front Controller (Fo vezerlo)
 *
 * Az alkalmazas egyetlen belepesi pontja.
 * Minden kerest ez a fajl kezel:
 *   - POST muveletek feldolgozasa (bejelentkezes, regisztracio, kapcsolat, CRUD, kepfeltoltes)
 *   - Oldal utvonalvalasztas $_GET['page'] alapjan
 *   - Header es footer sablon betoltese
 *
 * Tema: Filmtar (Movie Database)
 * Megjelenites nyelve: magyar
 */

// === Munkamenet (session) inditas ============================================
session_start();

// === Konfiguracio betoltese (adatbazis kapcsolat: $dbh) ======================
require __DIR__ . '/config.php';

// === Segedfuggvenyek =========================================================

/**
 * Flash uzenet tarolasa a munkamenetben.
 * Az uzenet a kovetkezo oldalletolteskor jelenik meg, majd torlodik.
 *
 * @param string $tipus  Az uzenet tipusa: 'success' vagy 'error'
 * @param string $uzenet Az uzenet szovege
 */
function flash(string $tipus, string $uzenet): void
{
    $_SESSION['flash'] = [
        'tipus'  => $tipus,
        'uzenet' => $uzenet,
    ];
}

/**
 * Visszaadja es torli a tarolt flash uzenetet.
 *
 * @return array|null  ['tipus' => ..., 'uzenet' => ...] vagy null
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Ellenorzi, hogy a felhasznalo be van-e jelentkezve.
 *
 * @return bool
 */
function bejelentkezveVan(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Biztonsagos atiranyitas (PRG minta), majd leallitas.
 *
 * @param string $url Az atiranyitas celcime
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// =============================================================================
// POST MUVELETEK FELDOLGOZASA
// A POST kereseket az oldal megjelenites elott dolgozzuk fel (PRG minta).
// Feldolgozas utan atiranyitunk, igy az ujratoltesnel nem kuldodik ujra az urlap.
// =============================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $action = $_POST['action'];

    // -------------------------------------------------------------------------
    // BEJELENTKEZES
    // -------------------------------------------------------------------------
    if ($action === 'login') {
        $felhasznalonev = trim($_POST['login_nev'] ?? '');
        $jelszo         = $_POST['jelszo'] ?? '';

        // Validacio: mindket mezo kitoltese kotelezo
        if ($felhasznalonev === '' || $jelszo === '') {
            flash('error', 'Kérlek, töltsd ki mindkét mezőt!');
            redirect('index.php?page=belepes');
        }

        // Felhasznalo keresese az adatbazisban
        $stmt = $dbh->prepare(
            'SELECT id, felhasznalonev, jelszo, csaladi_nev, utonev
             FROM felhasznalok
             WHERE felhasznalonev = :fnev
             LIMIT 1'
        );
        $stmt->execute([':fnev' => $felhasznalonev]);
        $user = $stmt->fetch();

        // Jelszo ellenorzese
        if ($user && password_verify($jelszo, $user['jelszo'])) {
            // Munkamenet beallitasa - jelszot NEM tarolunk!
            $_SESSION['user'] = [
                'id'             => $user['id'],
                'felhasznalonev' => $user['felhasznalonev'],
                'csaladi_nev'    => $user['csaladi_nev'],
                'utonev'         => $user['utonev'],
            ];
            flash('success', 'Sikeres bejelentkezés! Üdvözlünk, ' . htmlspecialchars($user['utonev']) . '!');
            redirect('index.php?page=fooldal');
        } else {
            flash('error', 'Hibás felhasználónév vagy jelszó!');
            redirect('index.php?page=belepes');
        }
    }

    // -------------------------------------------------------------------------
    // REGISZTRACIO
    // -------------------------------------------------------------------------
    if ($action === 'register') {
        $csaladi_nev    = trim($_POST['csaladi_nev'] ?? '');
        $utonev         = trim($_POST['utonev'] ?? '');
        $felhasznalonev = trim($_POST['login_nev'] ?? '');
        $email          = trim($_POST['email'] ?? '');
        $jelszo         = $_POST['jelszo'] ?? '';
        $jelszo2        = $_POST['jelszo_ujra'] ?? '';

        // Validacio
        if ($csaladi_nev === '' || $utonev === '' || $felhasznalonev === '' || $email === '' || $jelszo === '') {
            flash('error', 'Minden mező kitöltése kötelező!');
            redirect('index.php?page=regisztracio');
        }

        // E-mail formatum ellenorzese
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Érvénytelen e-mail cím formátum!');
            redirect('index.php?page=regisztracio');
        }

        // Jelszavak egyezesenek ellenorzese
        if ($jelszo !== $jelszo2) {
            flash('error', 'A két jelszó nem egyezik!');
            redirect('index.php?page=regisztracio');
        }

        // Jelszo minimalis hossz ellenorzese
        if (strlen($jelszo) < 6) {
            flash('error', 'A jelszónak legalább 6 karakter hosszúnak kell lennie!');
            redirect('index.php?page=regisztracio');
        }

        // Ellenorizzuk, hogy letezik-e mar a felhasznalonev
        $stmt = $dbh->prepare(
            'SELECT id FROM felhasznalok WHERE felhasznalonev = :fnev LIMIT 1'
        );
        $stmt->execute([':fnev' => $felhasznalonev]);
        if ($stmt->fetch()) {
            flash('error', 'Ez a felhasználónév már foglalt!');
            redirect('index.php?page=regisztracio');
        }

        // Jelszo hashelese
        $hashelt_jelszo = password_hash($jelszo, PASSWORD_DEFAULT);

        // Beszuras az adatbazisba
        $stmt = $dbh->prepare(
            'INSERT INTO felhasznalok (csaladi_nev, utonev, felhasznalonev, email, jelszo)
             VALUES (:cnev, :unev, :fnev, :email, :jelszo)'
        );
        $stmt->execute([
            ':cnev'  => $csaladi_nev,
            ':unev'  => $utonev,
            ':fnev'  => $felhasznalonev,
            ':email' => $email,
            ':jelszo' => $hashelt_jelszo,
        ]);

        flash('success', 'Sikeres regisztráció! Most már bejelentkezhetsz.');
        redirect('index.php?page=belepes');
    }

    // -------------------------------------------------------------------------
    // KAPCSOLAT URLAP KULDESE
    // -------------------------------------------------------------------------
    if ($action === 'contact_submit') {
        $nev    = trim($_POST['nev'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $targy  = trim($_POST['targy'] ?? '');
        $uzenet = trim($_POST['uzenet'] ?? '');

        // Szerver oldali validacio - minden mezo kotelezo
        $hibak = [];

        if ($nev === '') {
            $hibak[] = 'A név megadása kötelező!';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $hibak[] = 'Érvényes e-mail cím megadása kötelező!';
        }
        if ($targy === '') {
            $hibak[] = 'A tárgy megadása kötelező!';
        }
        if ($uzenet === '') {
            $hibak[] = 'Az üzenet megadása kötelező!';
        }

        // Ha van hiba, visszairanyitunk az urlapadatokkal egyutt
        if (!empty($hibak)) {
            $_SESSION['form_errors'] = $hibak;
            $_SESSION['form_data'] = [
                'nev'    => $nev,
                'email'  => $email,
                'targy'  => $targy,
                'uzenet' => $uzenet,
            ];
            redirect('index.php?page=kapcsolat');
        }

        // Mentes az uzenetek tablaba
        $kuldo_id = bejelentkezveVan() ? $_SESSION['user']['id'] : null;

        $stmt = $dbh->prepare(
            'INSERT INTO uzenetek (nev, email, targy, uzenet, kuldo_id, kuldve)
             VALUES (:nev, :email, :targy, :uzenet, :kuldo_id, NOW())'
        );
        $stmt->execute([
            ':nev'      => $nev,
            ':email'    => $email,
            ':targy'    => $targy,
            ':uzenet'   => $uzenet,
            ':kuldo_id' => $kuldo_id,
        ]);

        flash('success', 'Üzeneted sikeresen elküldtük! Hamarosan válaszolunk.');
        redirect('index.php?page=kapcsolat');
    }

    // -------------------------------------------------------------------------
    // CRUD - UJ FILM LETREHOZASA
    // -------------------------------------------------------------------------
    if ($action === 'crud_create') {
        $cim       = trim($_POST['cim'] ?? '');
        $rendezo   = trim($_POST['rendezo'] ?? '');
        $ev        = (int)($_POST['ev'] ?? 0);
        $mufaj     = trim($_POST['mufaj'] ?? '');
        $leiras    = trim($_POST['leiras'] ?? '');
        $ertekeles = isset($_POST['ertekeles']) ? (float)$_POST['ertekeles'] : null;

        // Validacio
        if ($cim === '' || $rendezo === '' || $ev <= 0) {
            flash('error', 'A cím, rendező és év mezők kitöltése kötelező!');
            redirect('index.php?page=crud&action=uj');
        }

        // Beszuras a filmek tablaba
        $stmt = $dbh->prepare(
            'INSERT INTO filmek (cim, rendezo, ev, mufaj, leiras, ertekeles)
             VALUES (:cim, :rendezo, :ev, :mufaj, :leiras, :ertekeles)'
        );
        $stmt->execute([
            ':cim'       => $cim,
            ':rendezo'   => $rendezo,
            ':ev'        => $ev,
            ':mufaj'     => $mufaj,
            ':leiras'    => $leiras,
            ':ertekeles' => $ertekeles,
        ]);

        flash('success', 'A film sikeresen hozzáadva!');
        redirect('index.php?page=crud');
    }

    // -------------------------------------------------------------------------
    // CRUD - FILM FRISSITESE (SZERKESZTES)
    // -------------------------------------------------------------------------
    if ($action === 'crud_update') {
        $id        = (int)($_POST['id'] ?? 0);
        $cim       = trim($_POST['cim'] ?? '');
        $rendezo   = trim($_POST['rendezo'] ?? '');
        $ev        = (int)($_POST['ev'] ?? 0);
        $mufaj     = trim($_POST['mufaj'] ?? '');
        $leiras    = trim($_POST['leiras'] ?? '');
        $ertekeles = isset($_POST['ertekeles']) ? (float)$_POST['ertekeles'] : null;

        // Validacio
        if ($id <= 0 || $cim === '' || $rendezo === '' || $ev <= 0) {
            flash('error', 'A cím, rendező és év mezők kitöltése kötelező!');
            redirect('index.php?page=crud&action=szerkeszt&id=' . $id);
        }

        // Frissites a filmek tablaban
        $stmt = $dbh->prepare(
            'UPDATE filmek
             SET cim = :cim, rendezo = :rendezo, ev = :ev,
                 mufaj = :mufaj, leiras = :leiras, ertekeles = :ertekeles
             WHERE id = :id'
        );
        $stmt->execute([
            ':cim'       => $cim,
            ':rendezo'   => $rendezo,
            ':ev'        => $ev,
            ':mufaj'     => $mufaj,
            ':leiras'    => $leiras,
            ':ertekeles' => $ertekeles,
            ':id'        => $id,
        ]);

        flash('success', 'A film adatai sikeresen frissítve!');
        redirect('index.php?page=crud');
    }

    // -------------------------------------------------------------------------
    // CRUD - FILM TORLESE
    // -------------------------------------------------------------------------
    if ($action === 'crud_delete') {
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $dbh->prepare('DELETE FROM filmek WHERE id = :id');
            $stmt->execute([':id' => $id]);
            flash('success', 'A film sikeresen törölve!');
        } else {
            flash('error', 'Érvénytelen film azonosító!');
        }

        redirect('index.php?page=crud');
    }

    // -------------------------------------------------------------------------
    // KEP FELTOLTES
    // -------------------------------------------------------------------------
    if ($action === 'image_upload') {
        // Bejelentkezes ellenorzese
        if (!bejelentkezveVan()) {
            flash('error', 'Képfeltöltéshez be kell jelentkezned!');
            redirect('index.php?page=belepes');
        }

        // Ellenorizzuk, hogy erkezett-e fajl
        if (!isset($_FILES['kep']) || $_FILES['kep']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Hiba történt a fájl feltöltése közben!');
            redirect('index.php?page=kepek');
        }

        $fajl = $_FILES['kep'];

        // Engedelyezett fajltipusok
        $engedelyezett_tipusok = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $engedelyezett_kiterjesztesek = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Fajltipus ellenorzese
        $fajl_tipus = mime_content_type($fajl['tmp_name']);
        $kiterjesztes = strtolower(pathinfo($fajl['name'], PATHINFO_EXTENSION));

        if (!in_array($fajl_tipus, $engedelyezett_tipusok) || !in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {
            flash('error', 'Csak JPG, PNG, GIF és WebP formátumú képek engedélyezettek!');
            redirect('index.php?page=kepek');
        }

        // Egyedi fajlnev generalasa az utkozesek elkerulesere
        $uj_fajlnev = uniqid('kep_', true) . '.' . $kiterjesztes;
        $cel_utvonal = __DIR__ . '/uploads/' . $uj_fajlnev;

        // Fajl athelyezese az uploads mappaba
        if (move_uploaded_file($fajl['tmp_name'], $cel_utvonal)) {
            // Mentes az adatbazisba
            $stmt = $dbh->prepare(
                'INSERT INTO kepek (fajlnev, eredeti_nev, feltolto_id, feltoltve)
                 VALUES (:fajlnev, :eredeti_nev, :feltolto_id, NOW())'
            );
            $stmt->execute([
                ':fajlnev'       => $uj_fajlnev,
                ':eredeti_nev'   => $fajl['name'],
                ':feltolto_id'   => $_SESSION['user']['id'],
            ]);

            flash('success', 'A kép sikeresen feltöltve!');
        } else {
            flash('error', 'Hiba történt a fájl mentése közben!');
        }

        redirect('index.php?page=kepek');
    }
}

// =============================================================================
// UTVONALVALASZTAS (ROUTING)
// A $_GET['page'] parameter alapjan valasztjuk ki a megjelenitheto oldalt.
// =============================================================================

// Az aktualis oldal meghatarozasa (alapertelmezetten: fooldal)
$page = $_GET['page'] ?? 'fooldal';

// Engedelyezett oldalak listaja (biztonsagi okokbol)
$engedelyezett_oldalak = [
    'fooldal',
    'kepek',
    'kapcsolat',
    'uzenetek',
    'crud',
    'belepes',
    'regisztracio',
    'kijelentkezes',
];

// Ha nem engedelyezett oldalt kertek, visszairanyitunk a fooldalra
if (!in_array($page, $engedelyezett_oldalak)) {
    flash('error', 'A keresett oldal nem található!');
    redirect('index.php?page=fooldal');
}

// --- Kijelentkezes kezelese --------------------------------------------------
// Nem kell kulon oldal, csak toroljuk a munkamenetet es atiranyitunk
if ($page === 'kijelentkezes') {
    $_SESSION = [];
    session_destroy();
    // Uj munkamenet inditas a flash uzenethez
    session_start();
    flash('success', 'Sikeresen kijelentkeztél!');
    redirect('index.php?page=fooldal');
}

// =============================================================================
// OLDAL MEGJELENITES
// Header sablon -> oldal tartalom -> footer sablon
// =============================================================================

// Flash uzenet kiolvasasa (ha van)
$flash = getFlash();

// Header sablon betoltese (navigacio, HTML fejlec)
require __DIR__ . '/templates/header.php';

// --- Flash uzenet megjelenitese ----------------------------------------------
if ($flash !== null): ?>
    <div class="flash-message flash-<?= htmlspecialchars($flash['tipus']) ?>">
        <?= htmlspecialchars($flash['uzenet']) ?>
    </div>
<?php endif;

// --- Oldal tartalom betoltese ------------------------------------------------
switch ($page) {
    case 'fooldal':
        require __DIR__ . '/pages/fooldal.php';
        break;

    case 'kepek':
        require __DIR__ . '/pages/kepek.php';
        break;

    case 'kapcsolat':
        require __DIR__ . '/pages/kapcsolat.php';
        break;

    case 'uzenetek':
        require __DIR__ . '/pages/uzenetek.php';
        break;

    case 'crud':
        // CRUD oldal - egyetlen fajl kezeli az osszes muveletet (list, uj, szerkeszt, torol)
        require __DIR__ . '/pages/crud.php';
        break;

    case 'belepes':
        require __DIR__ . '/pages/belepes.php';
        break;

    case 'regisztracio':
        require __DIR__ . '/pages/regisztracio.php';
        break;

    default:
        // Ismeretlen oldal - fooldalra iranyitunk (ide normalis esetben nem jutunk)
        require __DIR__ . '/pages/fooldal.php';
        break;
}

// Footer sablon betoltese (lablec, HTML zaro)
require __DIR__ . '/templates/footer.php';
