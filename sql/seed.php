<?php

$isCli = (php_sapi_name() === 'cli');
if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html lang="hu"><head><meta charset="utf-8">';
    echo '<title>Filmtár - Adatbázis inicializálás</title>';
    echo '<style>body{font-family:monospace;padding:20px;background:
       . '.ok{color:
       . 'h1{color:
    echo '</head><body><h1>Filmtár - Adatbázis inicializálás</h1><pre>';
}

function msg(string $szoveg, string $tipus = 'info'): void
{
    global $isCli;
    if ($isCli) {
        $prefix = match ($tipus) {
            'ok'   => "\033[32m[OK]\033[0m ",
            'hiba' => "\033[31m[HIBA]\033[0m ",
            default => "\033[33m[INFO]\033[0m ",
        };
        echo $prefix . $szoveg . PHP_EOL;
    } else {
        echo '<span class="' . $tipus . '">[' . strtoupper($tipus) . ']</span> '
           . htmlspecialchars($szoveg) . "\n";
    }
}

msg('Kapcsolódás a MySQL szerverhez...');

try {

    $pdo = new PDO(
        'mysql:host=localhost;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
    msg('Kapcsolat létrehozva.', 'ok');
} catch (PDOException $e) {
    msg('Nem sikerült kapcsolódni: ' . $e->getMessage(), 'hiba');
    exit(1);
}

msg('Adatbázis létrehozása (filmtar)...');

$pdo->exec("CREATE DATABASE IF NOT EXISTS `filmtar`
            DEFAULT CHARACTER SET utf8mb4
            DEFAULT COLLATE utf8mb4_hungarian_ci");
$pdo->exec("USE `filmtar`");
$pdo->exec("SET NAMES utf8mb4");

msg('Adatbázis kész.', 'ok');

msg('Táblák létrehozása...');

$pdo->exec("DROP TABLE IF EXISTS `g_kepek`");
$pdo->exec("DROP TABLE IF EXISTS `g_uzenetek`");
$pdo->exec("DROP TABLE IF EXISTS `g_filmek`");
$pdo->exec("DROP TABLE IF EXISTS `g_felhasznalok`");

$pdo->exec("
    CREATE TABLE `g_felhasznalok` (
        `id`              INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
        `felhasznalonev`  VARCHAR(50)  NOT NULL UNIQUE              COMMENT 'Bejelentkezési név',
        `jelszo`          VARCHAR(255) NOT NULL                     COMMENT 'Bcrypt hash (password_hash)',
        `csaladi_nev`     VARCHAR(100) NOT NULL                     COMMENT 'Családi név',
        `utonev`          VARCHAR(100) NOT NULL                     COMMENT 'Utónév',
        `email`           VARCHAR(100) NOT NULL                     COMMENT 'E-mail cím',
        `letrehozva`      DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Regisztráció időpontja'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
      COMMENT='Regisztrált felhasználók'
");
msg('  felhasznalok tábla létrehozva.', 'ok');

$pdo->exec("
    CREATE TABLE `g_filmek` (
        `id`         INT           AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
        `cim`        VARCHAR(200)  NOT NULL                     COMMENT 'Film címe',
        `rendezo`    VARCHAR(100)  NOT NULL                     COMMENT 'Rendező neve',
        `ev`         INT           NOT NULL                     COMMENT 'Megjelenés éve',
        `mufaj`      VARCHAR(100)  NOT NULL                     COMMENT 'Műfaj megnevezése',
        `ertekeles`  DECIMAL(3,1)  DEFAULT NULL                 COMMENT 'Értékelés (0.0 - 10.0)',
        `leiras`     TEXT          DEFAULT NULL                  COMMENT 'Film rövid leírása'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
      COMMENT='Filmek adatbázisa'
");
msg('  filmek tábla létrehozva.', 'ok');

$pdo->exec("
    CREATE TABLE `g_uzenetek` (
        `id`        INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
        `nev`       VARCHAR(100) NOT NULL                     COMMENT 'Küldő neve',
        `email`     VARCHAR(100) NOT NULL                     COMMENT 'Küldő e-mail címe',
        `targy`     VARCHAR(200) NOT NULL                     COMMENT 'Üzenet tárgya',
        `uzenet`    TEXT         NOT NULL                     COMMENT 'Üzenet szövege',
        `kuldo_id`  INT          DEFAULT NULL                 COMMENT 'Küldő felhasználó ID-ja (NULL = vendég)',
        `kuldve`    DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Küldés időpontja',

        FOREIGN KEY (`kuldo_id`) REFERENCES `g_felhasznalok`(`id`)
            ON DELETE SET NULL
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
      COMMENT='Kapcsolatfelvételi üzenetek'
");
msg('  uzenetek tábla létrehozva.', 'ok');

$pdo->exec("
    CREATE TABLE `g_kepek` (
        `id`            INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
        `fajlnev`       VARCHAR(255) NOT NULL                     COMMENT 'Tárolt fájlnév (szerveren)',
        `eredeti_nev`   VARCHAR(255) NOT NULL                     COMMENT 'Eredeti fájlnév (feltöltéskor)',
        `feltolto_id`   INT          NOT NULL                     COMMENT 'Feltöltő felhasználó ID-ja',
        `feltoltve`     DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Feltöltés időpontja',

        FOREIGN KEY (`feltolto_id`) REFERENCES `g_felhasznalok`(`id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
      COMMENT='Galéria képek'
");
msg('  kepek tábla létrehozva.', 'ok');

msg('Minden tábla sikeresen létrehozva.', 'ok');

msg('Felhasználók beszúrása...');

$felhasznalok = [
    [
        'felhasznalonev' => 'admin',
        'jelszo'         => 'admin123',
        'csaladi_nev'    => 'Admin',
        'utonev'         => 'Felhasználó',
        'email'          => 'admin@filmtar.hu',
        'letrehozva'     => '2025-01-01 10:00:00',
    ],
    [
        'felhasznalonev' => 'teszt',
        'jelszo'         => 'teszt123',
        'csaladi_nev'    => 'Teszt',
        'utonev'         => 'Elek',
        'email'          => 'teszt@filmtar.hu',
        'letrehozva'     => '2025-02-15 14:30:00',
    ],
    [
        'felhasznalonev' => 'user1',
        'jelszo'         => 'jelszo123',
        'csaladi_nev'    => 'Kovács',
        'utonev'         => 'János',
        'email'          => 'kovacs.janos@example.hu',
        'letrehozva'     => '2025-03-20 09:15:00',
    ],
];

$stmt = $pdo->prepare("
    INSERT INTO `g_felhasznalok`
        (`felhasznalonev`, `jelszo`, `csaladi_nev`, `utonev`, `email`, `letrehozva`)
    VALUES
        (:felhasznalonev, :jelszo, :csaladi_nev, :utonev, :email, :letrehozva)
");

foreach ($felhasznalok as $f) {

    $hash = password_hash($f['jelszo'], PASSWORD_DEFAULT);

    $stmt->execute([
        ':felhasznalonev' => $f['felhasznalonev'],
        ':jelszo'         => $hash,
        ':csaladi_nev'    => $f['csaladi_nev'],
        ':utonev'         => $f['utonev'],
        ':email'          => $f['email'],
        ':letrehozva'     => $f['letrehozva'],
    ]);

    msg("  {$f['felhasznalonev']} (jelszó: {$f['jelszo']}) - hash: " . substr($hash, 0, 30) . '...', 'ok');
}

msg('Filmek beszúrása...');

$filmek = [
    [
        'A Pál utcai fiúk', 'Fábri Zoltán', 1969, 'Dráma', 8.2,
        'Molnár Ferenc azonos című regényének filmadaptációja. A pesti srácok hősies küzdelme a grundért, amely egyben a barátság, a becsület és az áldozatvállalás örökérvényű története. A magyar filmgyártás egyik legismertebb és legkedveltebb alkotása.',
    ],
    [
        'Kontroll', 'Antal Nimród', 2003, 'Thriller', 7.7,
        'A budapesti metró földalatti világában játszódó egyedülálló thriller. A jegyellenőrök mindennapjait és személyes küzdelmeit mutatja be szürreális, sötét humorral átszőtt módon. A film nemzetközi sikert aratott a cannes-i filmfesztiválon.',
    ],
    [
        'Megáll az idő', 'Gothár Péter', 1982, 'Dráma', 7.8,
        'Az 1960-as évek Magyarországán játszódó coming-of-age dráma. Két középiskolás fiú barátsága és lázadása a szocialista rendszer hétköznapjai ellen. A film hűen ábrázolja a korszak hangulatát és a fiatalok vágyait a szabadságra.',
    ],
    [
        'Macskafogó', 'Ternovszky Béla', 1986, 'Animáció', 8.0,
        'Legendás magyar animációs film, amely egy egér-macska harcot mesél el egy képzeletbeli városban. A film tele van szellemes párbeszédekkel, emlékezetes karakterekkel és zenével. Generációk kedvence, a magyar animáció egyik csúcsteljesítménye.',
    ],
    [
        'Mindenki', 'Deák Kristóf', 2016, 'Rövidfilm', 7.6,
        'Oscar-díjas magyar kisjátékfilm egy iskolai kórusról. A történet középpontjában egy újonnan érkezett kislány áll, aki szembesül az igazságtalansággal. A film megható módon mutatja be a szolidaritás és a közösség erejét.',
    ],
    [
        'Napszállta', 'Nemes Jeles László', 2018, 'Dráma', 6.5,
        'Az első világháború előtti Budapesten játszódó rejtélyes dráma. Egy fiatal nő visszatér a családi kalapszalonba, és egy sötét titok nyomába ered. A film atmoszférikus képi világa lenyűgöző, de megosztotta a kritikusokat.',
    ],
    [
        'Saul fia', 'Nemes Jeles László', 2015, 'Dráma', 8.3,
        'Oscar-díjas magyar film a holokauszt borzalmairól. Saul Ausländer, egy auschwitzi Sonderkommando-tag megpróbálja méltóságteljesen eltemetni egy kisfiú holttestét. A film egyedülálló kamerahasználatával és nyers ábrázolásával megrázó élményt nyújt.',
    ],
    [
        'Hukkle', 'Pálfi György', 2002, 'Misztikus', 7.1,
        'Dialógus nélküli, különleges hangulatú film egy magyar faluról. A felszínen idilli vidéki élet alatt sötét titkok rejtőznek. Pálfi György rendkívül eredeti és innovatív debütáló filmje, amely nemzetközi elismerést szerzett.',
    ],
    [
        'A tanú', 'Bacsó Péter', 1969, 'Vígjáték', 8.1,
        'A Rákosi-korszak abszurditásait szatirikus humorral bemutató kultuszfilm. Pelikán József, az egyszerű gátőr akaratlanul is a rendszer fogaskerekei közé kerül. A filmet évtizedekig betiltották, de azóta a magyar filmtörténet egyik legfontosabb alkotásává vált.',
    ],
    [
        'Liza, a rókatündér', 'Ujj Mészáros Károly', 2015, 'Fantasy', 7.0,
        'Bájosan különc romantikus fantasyfilm egy magányos ápolónőről, aki egy japán rókatündér átkaként minden férfi meghal, akibe beleszeret. A film ötvözi a magyar és japán kultúrát, eredeti és szórakoztató módon.',
    ],
    [
        'A Viszkis', 'Antal Nimród', 2017, 'Bűnügyi', 6.8,
        'A rendszerváltás utáni Magyarországon játszódó bűnügyi dráma, amely Ambrus Attila, a whisky-s bankrabló valós történetén alapul. A film a kilencvenes évek hangulatát idézi meg, izgalmas és szórakoztató formában.',
    ],
    [
        'Taxidermia', 'Pálfi György', 2006, 'Groteszk', 7.0,
        'Három generáció groteszk története a 20. századi Magyarországon. A film provokatív és vizuálisan lenyűgöző módon mutatja be a test, az étkezés és az emberi lét szélsőségeit. Nem mindennapi filmes élmény a merészebb nézők számára.',
    ],
];

$stmt = $pdo->prepare("
    INSERT INTO `g_filmek` (`cim`, `rendezo`, `ev`, `mufaj`, `ertekeles`, `leiras`)
    VALUES (:cim, :rendezo, :ev, :mufaj, :ertekeles, :leiras)
");

foreach ($filmek as $film) {
    $stmt->execute([
        ':cim'       => $film[0],
        ':rendezo'   => $film[1],
        ':ev'        => $film[2],
        ':mufaj'     => $film[3],
        ':ertekeles' => $film[4],
        ':leiras'    => $film[5],
    ]);
    msg("  {$film[0]} ({$film[2]})", 'ok');
}

msg('Üzenetek beszúrása...');

$uzenetek = [
    [
        'nev'      => 'Admin Felhasználó',
        'email'    => 'admin@filmtar.hu',
        'targy'    => 'Üdvözlet a Filmtárban!',
        'uzenet'   => 'Kedves Felhasználók! Üdvözlünk a Filmtár webalkalmazásban. Ha bármilyen kérdésetek van, nyugodtan írjatok nekünk ezen az űrlapon keresztül. Jó szórakozást kívánunk a böngészéshez!',
        'kuldo_id' => 1,
        'kuldve'   => '2025-01-15 11:30:00',
    ],
    [
        'nev'      => 'Teszt Elek',
        'email'    => 'teszt@filmtar.hu',
        'targy'    => 'Új film javaslat',
        'uzenet'   => 'Sziasztok! Szeretném javasolni, hogy vegyétek fel az adatbázisba a "Tiszta szívvel" című filmet is. Nagyon jó magyar vígjáték, Till Attila rendezte 2016-ban. Köszönöm!',
        'kuldo_id' => 2,
        'kuldve'   => '2025-03-01 16:45:00',
    ],
    [
        'nev'      => 'Vendég László',
        'email'    => 'vendeg.laszlo@gmail.com',
        'targy'    => 'Kérdés az értékelésekről',
        'uzenet'   => 'Üdvözlöm! Szeretném megkérdezni, hogy az értékelések milyen rendszer szerint készülnek. Saját vélemények vagy IMDb-ről átvett adatok? Előre is köszönöm a választ.',
        'kuldo_id' => null,
        'kuldve'   => '2025-03-10 09:20:00',
    ],
    [
        'nev'      => 'Kovács János',
        'email'    => 'kovacs.janos@example.hu',
        'targy'    => 'Hibajelentés - képfeltöltés',
        'uzenet'   => 'Sziasztok! A képfeltöltés oldalon hibaüzenetet kapok, amikor PNG formátumú képet próbálok feltölteni. A fájl mérete 2 MB alatti. Tudnátok segíteni a probléma megoldásában?',
        'kuldo_id' => 3,
        'kuldve'   => '2025-03-18 14:10:00',
    ],
    [
        'nev'      => 'Szabó Anna',
        'email'    => 'szabo.anna@freemail.hu',
        'targy'    => 'Köszönet és visszajelzés',
        'uzenet'   => 'Kedves Filmtár csapat! Nagyon tetszik az oldal, régóta kerestem egy ilyen magyar filmeket összegyűjtő adatbázist. A leírások nagyon informatívak és a dizájn is szép. Csak így tovább!',
        'kuldo_id' => null,
        'kuldve'   => '2025-04-02 20:05:00',
    ],
];

$stmt = $pdo->prepare("
    INSERT INTO `g_uzenetek` (`nev`, `email`, `targy`, `uzenet`, `kuldo_id`, `kuldve`)
    VALUES (:nev, :email, :targy, :uzenet, :kuldo_id, :kuldve)
");

foreach ($uzenetek as $u) {
    $stmt->execute([
        ':nev'      => $u['nev'],
        ':email'    => $u['email'],
        ':targy'    => $u['targy'],
        ':uzenet'   => $u['uzenet'],
        ':kuldo_id' => $u['kuldo_id'],
        ':kuldve'   => $u['kuldve'],
    ]);
    $tipus = $u['kuldo_id'] ? "felhasználó (ID: {$u['kuldo_id']})" : 'vendég';
    msg("  \"{$u['targy']}\" - {$u['nev']} ({$tipus})", 'ok');
}

msg('');
msg('=== Adatbázis inicializálás befejezve ===', 'ok');
msg('');

$tablak = [
    'g_felhasznalok' => 'Felhasználók',
    'g_filmek'       => 'Filmek',
    'g_uzenetek'     => 'Üzenetek',
    'g_kepek'        => 'Képek',
];

foreach ($tablak as $tabla => $nev) {
    $db = $pdo->query("SELECT COUNT(*) AS db FROM `{$tabla}`")->fetch()['db'];
    msg("  {$nev} ({$tabla}): {$db} rekord", 'info');
}

msg('');
msg('Bejelentkezési adatok:', 'info');
msg('  admin  / admin123', 'info');
msg('  teszt  / teszt123', 'info');
msg('  user1  / jelszo123', 'info');
msg('');
msg('A jelszavak PHP password_hash() függvénnyel lettek titkosítva.', 'info');

if (!$isCli) {
    echo '</pre></body></html>';
}
