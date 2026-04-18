<?php
session_start();
require __DIR__ . '/config.php';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        $felhasznalonev = trim($_POST['login_nev'] ?? '');
        $jelszo = $_POST['jelszo'] ?? '';

        if ($felhasznalonev === '' || $jelszo === '') {
            flash('error', 'Kérlek, töltsd ki mindkét mezőt!');
            redirect('index.php?page=belepes');
        }

        $stmt = $dbh->prepare('SELECT id, felhasznalonev, jelszo, csaladi_nev, utonev FROM felhasznalok WHERE felhasznalonev = :fnev LIMIT 1');
        $stmt->execute([':fnev' => $felhasznalonev]);
        $user = $stmt->fetch();

        if ($user && password_verify($jelszo, $user['jelszo'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'felhasznalonev' => $user['felhasznalonev'],
                'csaladi_nev' => $user['csaladi_nev'],
                'utonev' => $user['utonev'],
            ];
            flash('success', 'Sikeres bejelentkezés! Üdvözlünk, ' . htmlspecialchars($user['utonev']) . '!');
            redirect('index.php?page=fooldal');
        } else {
            flash('error', 'Hibás felhasználónév vagy jelszó!');
            redirect('index.php?page=belepes');
        }
    }

    if ($action === 'register') {
        $csaladi_nev = trim($_POST['csaladi_nev'] ?? '');
        $utonev = trim($_POST['utonev'] ?? '');
        $felhasznalonev = trim($_POST['login_nev'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $jelszo = $_POST['jelszo'] ?? '';
        $jelszo2 = $_POST['jelszo_ujra'] ?? '';

        if ($csaladi_nev === '' || $utonev === '' || $felhasznalonev === '' || $email === '' || $jelszo === '') {
            flash('error', 'Minden mező kitöltése kötelező!');
            redirect('index.php?page=regisztracio');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Érvénytelen e-mail cím formátum!');
            redirect('index.php?page=regisztracio');
        }

        if ($jelszo !== $jelszo2) {
            flash('error', 'A két jelszó nem egyezik!');
            redirect('index.php?page=regisztracio');
        }

        if (strlen($jelszo) < 6) {
            flash('error', 'A jelszónak legalább 6 karakter hosszúnak kell lennie!');
            redirect('index.php?page=regisztracio');
        }

        $stmt = $dbh->prepare('SELECT id FROM felhasznalok WHERE felhasznalonev = :fnev LIMIT 1');
        $stmt->execute([':fnev' => $felhasznalonev]);
        if ($stmt->fetch()) {
            flash('error', 'Ez a felhasználónév már foglalt!');
            redirect('index.php?page=regisztracio');
        }

        $hashelt_jelszo = password_hash($jelszo, PASSWORD_DEFAULT);

        $stmt = $dbh->prepare('INSERT INTO felhasznalok (csaladi_nev, utonev, felhasznalonev, email, jelszo) VALUES (:cnev, :unev, :fnev, :email, :jelszo)');
        $stmt->execute([
            ':cnev' => $csaladi_nev,
            ':unev' => $utonev,
            ':fnev' => $felhasznalonev,
            ':email' => $email,
            ':jelszo' => $hashelt_jelszo,
        ]);

        flash('success', 'Sikeres regisztráció! Most már bejelentkezhetsz.');
        redirect('index.php?page=belepes');
    }

    if ($action === 'contact_submit') {
        $nev = trim($_POST['nev'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $targy = trim($_POST['targy'] ?? '');
        $uzenet = trim($_POST['uzenet'] ?? '');

        $hibak = [];
        if ($nev === '') $hibak[] = 'A név megadása kötelező!';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $hibak[] = 'Érvényes e-mail cím megadása kötelező!';
        if ($targy === '') $hibak[] = 'A tárgy megadása kötelező!';
        if ($uzenet === '') $hibak[] = 'Az üzenet megadása kötelező!';

        if (!empty($hibak)) {
            $_SESSION['form_errors'] = $hibak;
            $_SESSION['form_data'] = ['nev' => $nev, 'email' => $email, 'targy' => $targy, 'uzenet' => $uzenet];
            redirect('index.php?page=kapcsolat');
        }

        $kuldo_id = bejelentkezveVan() ? $_SESSION['user']['id'] : null;
        $stmt = $dbh->prepare('INSERT INTO uzenetek (nev, email, targy, uzenet, kuldo_id, kuldve) VALUES (:nev, :email, :targy, :uzenet, :kuldo_id, NOW())');
        $stmt->execute([':nev' => $nev, ':email' => $email, ':targy' => $targy, ':uzenet' => $uzenet, ':kuldo_id' => $kuldo_id]);

        flash('success', 'Üzeneted sikeresen elküldtük! Hamarosan válaszolunk.');
        redirect('index.php?page=kapcsolat');
    }

    if ($action === 'crud_create') {
        $cim = trim($_POST['cim'] ?? '');
        $rendezo = trim($_POST['rendezo'] ?? '');
        $ev = (int)($_POST['ev'] ?? 0);
        $mufaj = trim($_POST['mufaj'] ?? '');
        $leiras = trim($_POST['leiras'] ?? '');
        $ertekeles = isset($_POST['ertekeles']) ? (float)$_POST['ertekeles'] : null;

        if ($cim === '' || $rendezo === '' || $ev <= 0) {
            flash('error', 'A cím, rendező és év mezők kitöltése kötelező!');
            redirect('index.php?page=crud&action=uj');
        }

        $stmt = $dbh->prepare('INSERT INTO filmek (cim, rendezo, ev, mufaj, leiras, ertekeles) VALUES (:cim, :rendezo, :ev, :mufaj, :leiras, :ertekeles)');
        $stmt->execute([':cim' => $cim, ':rendezo' => $rendezo, ':ev' => $ev, ':mufaj' => $mufaj, ':leiras' => $leiras, ':ertekeles' => $ertekeles]);

        flash('success', 'A film sikeresen hozzáadva!');
        redirect('index.php?page=crud');
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
            redirect('index.php?page=crud&action=szerkeszt&id=' . $id);
        }

        $stmt = $dbh->prepare('UPDATE filmek SET cim = :cim, rendezo = :rendezo, ev = :ev, mufaj = :mufaj, leiras = :leiras, ertekeles = :ertekeles WHERE id = :id');
        $stmt->execute([':cim' => $cim, ':rendezo' => $rendezo, ':ev' => $ev, ':mufaj' => $mufaj, ':leiras' => $leiras, ':ertekeles' => $ertekeles, ':id' => $id]);

        flash('success', 'A film adatai sikeresen frissítve!');
        redirect('index.php?page=crud');
    }

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

    if ($action === 'image_upload') {
        if (!bejelentkezveVan()) {
            flash('error', 'Képfeltöltéshez be kell jelentkezned!');
            redirect('index.php?page=belepes');
        }

        if (!isset($_FILES['kep']) || $_FILES['kep']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Hiba történt a fájl feltöltése közben!');
            redirect('index.php?page=kepek');
        }

        $fajl = $_FILES['kep'];
        $engedelyezett_tipusok = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $engedelyezett_kiterjesztesek = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $fajl_tipus = mime_content_type($fajl['tmp_name']);
        $kiterjesztes = strtolower(pathinfo($fajl['name'], PATHINFO_EXTENSION));

        if (!in_array($fajl_tipus, $engedelyezett_tipusok) || !in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {
            flash('error', 'Csak JPG, PNG, GIF és WebP formátumú képek engedélyezettek!');
            redirect('index.php?page=kepek');
        }

        $uj_fajlnev = uniqid('kep_', true) . '.' . $kiterjesztes;
        $cel_utvonal = __DIR__ . '/uploads/' . $uj_fajlnev;

        if (move_uploaded_file($fajl['tmp_name'], $cel_utvonal)) {
            $stmt = $dbh->prepare('INSERT INTO kepek (fajlnev, eredeti_nev, feltolto_id, feltoltve) VALUES (:fajlnev, :eredeti_nev, :feltolto_id, NOW())');
            $stmt->execute([':fajlnev' => $uj_fajlnev, ':eredeti_nev' => $fajl['name'], ':feltolto_id' => $_SESSION['user']['id']]);
            flash('success', 'A kép sikeresen feltöltve!');
        } else {
            flash('error', 'Hiba történt a fájl mentése közben!');
        }
        redirect('index.php?page=kepek');
    }
}

$page = $_GET['page'] ?? 'fooldal';
$engedelyezett_oldalak = ['fooldal', 'kepek', 'kapcsolat', 'uzenetek', 'crud', 'belepes', 'regisztracio', 'kijelentkezes'];

if (!in_array($page, $engedelyezett_oldalak)) {
    flash('error', 'A keresett oldal nem található!');
    redirect('index.php?page=fooldal');
}

if ($page === 'kijelentkezes') {
    $_SESSION = [];
    session_destroy();
    session_start();
    flash('success', 'Sikeresen kijelentkeztél!');
    redirect('index.php?page=fooldal');
}

$flash = getFlash();

require __DIR__ . '/templates/header.php';

if ($flash !== null): ?>
    <div class="flash-message flash-<?= htmlspecialchars($flash['tipus']) ?>">
        <?= htmlspecialchars($flash['uzenet']) ?>
    </div>
<?php endif;

switch ($page) {
    case 'fooldal': require __DIR__ . '/pages/fooldal.php'; break;
    case 'kepek': require __DIR__ . '/pages/kepek.php'; break;
    case 'kapcsolat': require __DIR__ . '/pages/kapcsolat.php'; break;
    case 'uzenetek': require __DIR__ . '/pages/uzenetek.php'; break;
    case 'crud': require __DIR__ . '/pages/crud.php'; break;
    case 'belepes': require __DIR__ . '/pages/belepes.php'; break;
    case 'regisztracio': require __DIR__ . '/pages/regisztracio.php'; break;
    default: require __DIR__ . '/pages/fooldal.php'; break;
}

require __DIR__ . '/templates/footer.php';
