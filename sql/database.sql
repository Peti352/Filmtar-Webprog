

CREATE DATABASE IF NOT EXISTS `filmtar`
 DEFAULT CHARACTER SET utf8mb4
 DEFAULT COLLATE utf8mb4_hungarian_ci;

USE `filmtar`;

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

DROP TABLE IF EXISTS `g_kepek`;
DROP TABLE IF EXISTS `g_uzenetek`;
DROP TABLE IF EXISTS `g_filmek`;
DROP TABLE IF EXISTS `g_felhasznalok`;

CREATE TABLE `g_felhasznalok` (
 `id` INT AUTO_INCREMENT PRIMARY KEY ,
 `felhasznalonev` VARCHAR(50) NOT NULL UNIQUE ,
 `jelszo` VARCHAR(255) NOT NULL ,
 `csaladi_nev` VARCHAR(100) NOT NULL ,
 `utonev` VARCHAR(100) NOT NULL ,
 `email` VARCHAR(100) NOT NULL ,
 `letrehozva` DATETIME DEFAULT CURRENT_TIMESTAMP 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
 COMMENT='Regisztrált felhasználók';

CREATE TABLE `g_filmek` (
 `id` INT AUTO_INCREMENT PRIMARY KEY ,
 `cim` VARCHAR(200) NOT NULL ,
 `rendezo` VARCHAR(100) NOT NULL ,
 `ev` INT NOT NULL ,
 `mufaj` VARCHAR(100) NOT NULL ,
 `ertekeles` DECIMAL(3,1) DEFAULT NULL ,
 `leiras` TEXT DEFAULT NULL 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
 COMMENT='Filmek adatbázisa';

CREATE TABLE `g_uzenetek` (
 `id` INT AUTO_INCREMENT PRIMARY KEY ,
 `nev` VARCHAR(100) NOT NULL ,
 `email` VARCHAR(100) NOT NULL ,
 `targy` VARCHAR(200) NOT NULL ,
 `uzenet` TEXT NOT NULL ,
 `kuldo_id` INT DEFAULT NULL ,
 `kuldve` DATETIME DEFAULT CURRENT_TIMESTAMP ,

 FOREIGN KEY (`kuldo_id`) REFERENCES `g_felhasznalok`(`id`)
 ON DELETE SET NULL
 ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
 COMMENT='Kapcsolatfelvételi üzenetek';

CREATE TABLE `g_kepek` (
 `id` INT AUTO_INCREMENT PRIMARY KEY ,
 `fajlnev` VARCHAR(255) NOT NULL ,
 `eredeti_nev` VARCHAR(255) NOT NULL ,
 `feltolto_id` INT NOT NULL ,
 `feltoltve` DATETIME DEFAULT CURRENT_TIMESTAMP ,

 FOREIGN KEY (`feltolto_id`) REFERENCES `g_felhasznalok`(`id`)
 ON DELETE CASCADE
 ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci
 COMMENT='Galéria képek';

INSERT INTO `g_felhasznalok` (`felhasznalonev`, `jelszo`, `csaladi_nev`, `utonev`, `email`, `letrehozva`) VALUES

('admin', '$2y$10$xLRbXdEaRmMfCPn3k3VLnOQZbGjBHkfGmObaGEmMjgS3mMxGJ3dGe', 'Admin', 'Felhasználó', 'admin@filmtar.hu', '2025-01-01 10:00:00'),
('teszt', '$2y$10$kZpHDLeMRqMrTz3FVZaKiOYkIabHjT2M6M.GJfNxuWdYBasVkq.JG', 'Teszt', 'Elek', 'teszt@filmtar.hu', '2025-02-15 14:30:00'),
('user1', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Kovács', 'János', 'kovacs.janos@example.hu', '2025-03-20 09:15:00');

INSERT INTO `g_filmek` (`cim`, `rendezo`, `ev`, `mufaj`, `ertekeles`, `leiras`) VALUES
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

INSERT INTO `g_uzenetek` (`nev`, `email`, `targy`, `uzenet`, `kuldo_id`, `kuldve`) VALUES
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

