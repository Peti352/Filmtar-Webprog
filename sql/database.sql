-- =============================================================================
-- Filmtár - Magyar filmes adatbázis
-- =============================================================================
-- Adatbázis létrehozó és feltöltő szkript
--
-- Használat:
--   1. phpMyAdmin: Importálás fül -> fájl kiválasztása -> Indítás
--   2. Parancssor:  mysql -u root -p < database.sql
--
-- FIGYELEM: A szkript törli a meglévő táblákat és újra létrehozza őket!
--           A jelszavak placeholder hash-eket tartalmaznak.
--           Éles feltöltéshez használd a seed.php szkriptet, amely
--           PHP password_hash() függvénnyel generálja a jelszavakat.
-- =============================================================================


-- -----------------------------------------------------------------------------
-- 1. Adatbázis létrehozása és kiválasztása
-- -----------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `filmtar`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_hungarian_ci;

USE `filmtar`;

-- Karakterkészlet beállítása az aktuális kapcsolatra
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;


-- -----------------------------------------------------------------------------
-- 2. Meglévő táblák törlése (fordított függőségi sorrendben)
-- -----------------------------------------------------------------------------
-- A külső kulcs hivatkozások miatt először a hivatkozó táblákat töröljük,
-- majd utána a hivatkozott táblákat.

DROP TABLE IF EXISTS `kepek`;
DROP TABLE IF EXISTS `uzenetek`;
DROP TABLE IF EXISTS `filmek`;
DROP TABLE IF EXISTS `felhasznalok`;


-- -----------------------------------------------------------------------------
-- 3. Táblák létrehozása
-- -----------------------------------------------------------------------------

-- ~~~ felhasznalok (Felhasználók) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
-- A rendszer regisztrált felhasználóit tárolja.
-- A 'jelszo' mező PHP password_hash() kimenetét tartalmazza (bcrypt).
CREATE TABLE `felhasznalok` (
    `id`              INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
    `felhasznalonev`  VARCHAR(50)  NOT NULL UNIQUE              COMMENT 'Bejelentkezési név',
    `jelszo`          VARCHAR(255) NOT NULL                     COMMENT 'Bcrypt hash (password_hash)',
    `csaladi_nev`     VARCHAR(100) NOT NULL                     COMMENT 'Családi név',
    `utonev`          VARCHAR(100) NOT NULL                     COMMENT 'Utónév',
    `email`           VARCHAR(100) NOT NULL                     COMMENT 'E-mail cím',
    `letrehozva`      DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Regisztráció időpontja'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
  COMMENT='Regisztrált felhasználók';


-- ~~~ filmek (Filmek - fő CRUD tábla) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
-- A filmadatbázis központi táblája. Minden film adatait itt tároljuk.
CREATE TABLE `filmek` (
    `id`         INT           AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
    `cim`        VARCHAR(200)  NOT NULL                     COMMENT 'Film címe',
    `rendezo`    VARCHAR(100)  NOT NULL                     COMMENT 'Rendező neve',
    `ev`         INT           NOT NULL                     COMMENT 'Megjelenés éve',
    `mufaj`      VARCHAR(100)  NOT NULL                     COMMENT 'Műfaj megnevezése',
    `ertekeles`  DECIMAL(3,1)  DEFAULT NULL                 COMMENT 'Értékelés (0.0 - 10.0)',
    `leiras`     TEXT          DEFAULT NULL                  COMMENT 'Film rövid leírása'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
  COMMENT='Filmek adatbázisa';


-- ~~~ uzenetek (Kapcsolatfelvételi üzenetek) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
-- A kapcsolatfelvételi űrlapon keresztül küldött üzenetek.
-- A kuldo_id NULL, ha vendég (nem bejelentkezett felhasználó) küldte.
CREATE TABLE `uzenetek` (
    `id`        INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
    `nev`       VARCHAR(100) NOT NULL                     COMMENT 'Küldő neve',
    `email`     VARCHAR(100) NOT NULL                     COMMENT 'Küldő e-mail címe',
    `targy`     VARCHAR(200) NOT NULL                     COMMENT 'Üzenet tárgya',
    `uzenet`    TEXT         NOT NULL                     COMMENT 'Üzenet szövege',
    `kuldo_id`  INT          DEFAULT NULL                 COMMENT 'Küldő felhasználó ID-ja (NULL = vendég)',
    `kuldve`    DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Küldés időpontja',

    FOREIGN KEY (`kuldo_id`) REFERENCES `felhasznalok`(`id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
  COMMENT='Kapcsolatfelvételi üzenetek';


-- ~~~ kepek (Galéria képek) ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
-- A felhasználók által feltöltött képek nyilvántartása.
-- A feltolto_id kötelező, csak bejelentkezett felhasználó tölthet fel képet.
CREATE TABLE `kepek` (
    `id`            INT          AUTO_INCREMENT PRIMARY KEY   COMMENT 'Egyedi azonosító',
    `fajlnev`       VARCHAR(255) NOT NULL                     COMMENT 'Tárolt fájlnév (szerveren)',
    `eredeti_nev`   VARCHAR(255) NOT NULL                     COMMENT 'Eredeti fájlnév (feltöltéskor)',
    `feltolto_id`   INT          NOT NULL                     COMMENT 'Feltöltő felhasználó ID-ja',
    `feltoltve`     DATETIME     DEFAULT CURRENT_TIMESTAMP    COMMENT 'Feltöltés időpontja',

    FOREIGN KEY (`feltolto_id`) REFERENCES `felhasznalok`(`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
  COMMENT='Galéria képek';


-- =============================================================================
-- 4. Mintaadatok beszúrása
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 4a. Felhasználók
-- -----------------------------------------------------------------------------
-- MEGJEGYZÉS: Az alábbi jelszó hash-ek a PHP password_hash() függvénnyel
-- lettek generálva. Ha a bejelentkezés nem működik, futtasd a seed.php
-- szkriptet, amely újragenerálja a hash-eket.
--
-- Jelszavak (nyílt szöveges):
--   admin  -> admin123
--   teszt  -> teszt123
--   user1  -> jelszo123

INSERT INTO `felhasznalok` (`felhasznalonev`, `jelszo`, `csaladi_nev`, `utonev`, `email`, `letrehozva`) VALUES
-- A bcrypt hash-ek password_hash()-sel generálva (PASSWORD_DEFAULT, cost=10)
('admin', '$2y$10$xLRbXdEaRmMfCPn3k3VLnOQZbGjBHkfGmObaGEmMjgS3mMxGJ3dGe', 'Admin', 'Felhasználó', 'admin@filmtar.hu',   '2025-01-01 10:00:00'),
('teszt', '$2y$10$kZpHDLeMRqMrTz3FVZaKiOYkIabHjT2M6M.GJfNxuWdYBasVkq.JG', 'Teszt', 'Elek',         'teszt@filmtar.hu',   '2025-02-15 14:30:00'),
('user1', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Kovács', 'János',       'kovacs.janos@example.hu', '2025-03-20 09:15:00');

-- FONTOS: A fenti hash-ek demonstrációs célúak. Éles használathoz futtasd
-- a seed.php szkriptet, amely a helyes hash-eket hozza létre!


-- -----------------------------------------------------------------------------
-- 4b. Filmek (12 magyar / magyar vonatkozású film)
-- -----------------------------------------------------------------------------

INSERT INTO `filmek` (`cim`, `rendezo`, `ev`, `mufaj`, `ertekeles`, `leiras`) VALUES
(
    'A Pál utcai fiúk',
    'Fábri Zoltán',
    1969,
    'Dráma',
    8.2,
    'Molnár Ferenc azonos című regényének filmadaptációja. A pesti srácok hősies küzdelme a grundért, amely egyben a barátság, a becsület és az áldozatvállalás örökérvényű története. A magyar filmgyártás egyik legismertebb és legkedveltebb alkotása.'
),
(
    'Kontroll',
    'Antal Nimród',
    2003,
    'Thriller',
    7.7,
    'A budapesti metró földalatti világában játszódó egyedülálló thriller. A jegyellenőrök mindennapjait és személyes küzdelmeit mutatja be szürreális, sötét humorral átszőtt módon. A film nemzetközi sikert aratott a cannes-i filmfesztiválon.'
),
(
    'Megáll az idő',
    'Gothár Péter',
    1982,
    'Dráma',
    7.8,
    'Az 1960-as évek Magyarországán játszódó coming-of-age dráma. Két középiskolás fiú barátsága és lázadása a szocialista rendszer hétköznapjai ellen. A film hűen ábrázolja a korszak hangulatát és a fiatalok vágyait a szabadságra.'
),
(
    'Macskafogó',
    'Ternovszky Béla',
    1986,
    'Animáció',
    8.0,
    'Legendás magyar animációs film, amely egy egér-macska harcot mesél el egy képzeletbeli városban. A film tele van szellemes párbeszédekkel, emlékezetes karakterekkel és zenével. Generációk kedvence, a magyar animáció egyik csúcsteljesítménye.'
),
(
    'Mindenki',
    'Deák Kristóf',
    2016,
    'Rövidfilm',
    7.6,
    'Oscar-díjas magyar kisjátékfilm egy iskolai kórusról. A történet középpontjában egy újonnan érkezett kislány áll, aki szembesül az igazságtalansággal. A film megható módon mutatja be a szolidaritás és a közösség erejét.'
),
(
    'Napszállta',
    'Nemes Jeles László',
    2018,
    'Dráma',
    6.5,
    'Az első világháború előtti Budapesten játszódó rejtélyes dráma. Egy fiatal nő visszatér a családi kalapszalonba, és egy sötét titok nyomába ered. A film atmoszférikus képi világa lenyűgöző, de megosztotta a kritikusokat.'
),
(
    'Saul fia',
    'Nemes Jeles László',
    2015,
    'Dráma',
    8.3,
    'Oscar-díjas magyar film a holokauszt borzalmairól. Saul Ausländer, egy auschwitzi Sonderkommando-tag megpróbálja méltóságteljesen eltemetni egy kisfiú holttestét. A film egyedülálló kamerahasználatával és nyers ábrázolásával megrázó élményt nyújt.'
),
(
    'Hukkle',
    'Pálfi György',
    2002,
    'Misztikus',
    7.1,
    'Dialógus nélküli, különleges hangulatú film egy magyar faluról. A felszínen idilli vidéki élet alatt sötét titkok rejtőznek. Pálfi György rendkívül eredeti és innovatív debütáló filmje, amely nemzetközi elismerést szerzett.'
),
(
    'A tanú',
    'Bacsó Péter',
    1969,
    'Vígjáték',
    8.1,
    'A Rákosi-korszak abszurditásait szatirikus humorral bemutató kultuszfilm. Pelikán József, az egyszerű gátőr akaratlanul is a rendszer fogaskerekei közé kerül. A filmet évtizedekig betiltották, de azóta a magyar filmtörténet egyik legfontosabb alkotásává vált.'
),
(
    'Liza, a rókatündér',
    'Ujj Mészáros Károly',
    2015,
    'Fantasy',
    7.0,
    'Bájosan különc romantikus fantasyfilm egy magányos ápolónőről, aki egy japán rókatündér átkaként minden férfi meghal, akibe beleszeret. A film ötvözi a magyar és japán kultúrát, eredeti és szórakoztató módon.'
),
(
    'A Viszkis',
    'Antal Nimród',
    2017,
    'Bűnügyi',
    6.8,
    'A rendszerváltás utáni Magyarországon játszódó bűnügyi dráma, amely Ambrus Attila, a whisky-s bankrabló valós történetén alapul. A film a kilencvenes évek hangulatát idézi meg, izgalmas és szórakoztató formában.'
),
(
    'Taxidermia',
    'Pálfi György',
    2006,
    'Groteszk',
    7.0,
    'Három generáció groteszk története a 20. századi Magyarországon. A film provokatív és vizuálisan lenyűgöző módon mutatja be a test, az étkezés és az emberi lét szélsőségeit. Nem mindennapi filmes élmény a merészebb nézők számára.'
);


-- -----------------------------------------------------------------------------
-- 4c. Üzenetek (5 minta üzenet - vegyes: bejelentkezett és vendég)
-- -----------------------------------------------------------------------------

INSERT INTO `uzenetek` (`nev`, `email`, `targy`, `uzenet`, `kuldo_id`, `kuldve`) VALUES
(
    'Admin Felhasználó',
    'admin@filmtar.hu',
    'Üdvözlet a Filmtárban!',
    'Kedves Felhasználók! Üdvözlünk a Filmtár webalkalmazásban. Ha bármilyen kérdésetek van, nyugodtan írjatok nekünk ezen az űrlapon keresztül. Jó szórakozást kívánunk a böngészéshez!',
    1,
    '2025-01-15 11:30:00'
),
(
    'Teszt Elek',
    'teszt@filmtar.hu',
    'Új film javaslat',
    'Sziasztok! Szeretném javasolni, hogy vegyétek fel az adatbázisba a "Tiszta szívvel" című filmet is. Nagyon jó magyar vígjáték, Till Attila rendezte 2016-ban. Köszönöm!',
    2,
    '2025-03-01 16:45:00'
),
(
    'Vendég László',
    'vendeg.laszlo@gmail.com',
    'Kérdés az értékelésekről',
    'Üdvözlöm! Szeretném megkérdezni, hogy az értékelések milyen rendszer szerint készülnek. Saját vélemények vagy IMDb-ről átvett adatok? Előre is köszönöm a választ.',
    NULL,
    '2025-03-10 09:20:00'
),
(
    'Kovács János',
    'kovacs.janos@example.hu',
    'Hibajelentés - képfeltöltés',
    'Sziasztok! A képfeltöltés oldalon hibaüzenetet kapok, amikor PNG formátumú képet próbálok feltölteni. A fájl mérete 2 MB alatti. Tudnátok segíteni a probléma megoldásában?',
    3,
    '2025-03-18 14:10:00'
),
(
    'Szabó Anna',
    'szabo.anna@freemail.hu',
    'Köszönet és visszajelzés',
    'Kedves Filmtár csapat! Nagyon tetszik az oldal, régóta kerestem egy ilyen magyar filmeket összegyűjtő adatbázist. A leírások nagyon informatívak és a dizájn is szép. Csak így tovább!',
    NULL,
    '2025-04-02 20:05:00'
);


-- =============================================================================
-- Kész! Az adatbázis sikeresen létrehozva és feltöltve mintaadatokkal.
-- =============================================================================
-- Összesítés:
--   - felhasznalok: 3 felhasználó (admin, teszt, user1)
--   - filmek:       12 magyar film részletes adatokkal
--   - uzenetek:     5 minta üzenet (3 bejelentkezett + 2 vendég)
--   - kepek:        üres (a felhasználók töltik fel)
--
-- A jelszavak helyes működéséhez futtasd a seed.php szkriptet is!
-- =============================================================================
