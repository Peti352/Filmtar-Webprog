# Filmtár - Magyar Filmadatbázis Webalkalmazás

## Projekt leírás

A Filmtár egy PHP-alapú webalkalmazás, amely magyar filmek adatbázisának kezelését teszi lehetővé. Az alkalmazás a Webprogramozás 1 tantárgy gyakorlati beadandó feladataként készült, a 7a - PHP Front-controller tervezési minta (2. Megoldás) alapján.

Elérhető: http://peti352.nhely.hu/

## Funkciók

### Bárki számára elérhető
- Regisztráció és bejelentkezés (session alapú)
- Filmek böngészése
- Képgaléria megtekintése
- Kapcsolatfelvétel űrlapon keresztül

### Bejelentkezett felhasználóknak
- Képfeltöltés a galériába
- Üzenetek megtekintése
- CRUD műveletek filmekkel (létrehozás, szerkesztés, törlés)

### Főoldal
- Hero szekció a projekt bemutatásával
- 2 videó (helyi mp4 + YouTube beágyazás)
- Google Maps beágyazás

## Technológiák

- **Backend:** PHP 8.0+ (PDO, session kezelés)
- **Frontend:** HTML5, CSS3 (Flexbox, Grid, Media Queries)
- **JavaScript:** Kliens oldali validáció, hamburger menü, lightbox
- **Adatbázis:** MySQL / MariaDB
- **Minta:** Front-controller (2. Megoldás), Post-Redirect-Get (PRG)

## Projekt struktúra

```
WebProg/
├── index.php                        # Front controller (fő belépési pont)
├── .htaccess                        # URL rewrite szabályok
│
├── includes/
│   └── config.inc.php               # $oldalak tömb, DB kapcsolat, segédfüggvények
│
├── logicals/
│   ├── belep.php                    # Bejelentkezés feldolgozás
│   ├── kilepes.php                  # Kijelentkezés
│   ├── regisztral.php               # Regisztráció feldolgozás
│   ├── kapcsolat.php                # Üzenetküldés feldolgozás
│   ├── crud.php                     # Film CRUD műveletek
│   └── kepfeltoltes.php             # Képfeltöltés feldolgozás
│
├── templates/
│   ├── index.tpl.php                # Fő layout sablon (fejléc, menü, lábléc)
│   └── pages/
│       ├── fooldal.tpl.php          # Főoldal
│       ├── belepes.tpl.php          # Bejelentkezés
│       ├── regisztracio.tpl.php     # Regisztráció
│       ├── kepek.tpl.php            # Képgaléria + feltöltés
│       ├── kapcsolat.tpl.php        # Kapcsolat űrlap
│       ├── uzenetek.tpl.php         # Üzenetek listája
│       └── crud.tpl.php             # Filmek CRUD kezelése
│
├── css/
│   └── style.css                    # Stíluslap (reszponzív, media queries)
│
├── js/
│   ├── main.js                      # Hamburger menü, lightbox
│   └── validation.js                # Kapcsolati űrlap validáció
│
├── sql/
│   └── database.sql                 # Adatbázis séma + mintaadatok
│
├── uploads/                         # Feltöltött képek
└── videos/                          # Videó fájlok
```

## Telepítés

### Előfeltételek
- PHP 8.0 vagy újabb
- MySQL 5.7+ vagy MariaDB 10.3+
- Apache webszerver (mod_rewrite engedélyezve)

### Adatbázis létrehozása
```bash
mysql -u root -p < sql/database.sql
```

Vagy importáld phpMyAdmin-on keresztül a `sql/database.sql` fájlt.

### Konfiguráció
Szerkeszd az `includes/config.inc.php` fájlt az adatbázis adataiddal:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'filmtar');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Webszerver indítása
XAMPP/WAMP/MAMP esetén helyezd a projekt mappát a `htdocs/` könyvtárba, majd nyisd meg: `http://localhost/WebProg/`

## Teszt felhasználók

A `database.sql` tartalmaz 3 teszt felhasználót:

| Felhasználónév | Jelszó | Leírás |
|----------------|--------|--------|
| admin | admin123 | Adminisztrátor |
| teszt | teszt123 | Teszt felhasználó |
| user1 | jelszo123 | Normál felhasználó |

## Adatbázis séma

```
g_felhasznalok (id, felhasznalonev, jelszo, csaladi_nev, utonev, email, letrehozva)
    |
    ├── g_uzenetek (id, kuldo_id, nev, email, targy, uzenet, kuldve)
    ├── g_kepek (id, feltolto_id, fajlnev, eredeti_nev, feltoltve)

g_filmek (id, cim, rendezo, ev, mufaj, ertekeles, leiras)
```

## Biztonság

- Jelszavak bcrypt hash-eléssel tárolva (password_hash / password_verify)
- Paraméterezett SQL lekérdezések (SQL injection védelem)
- XSS védelem (htmlspecialchars)
- Fájlfeltöltés validáció (MIME type + kiterjesztés ellenőrzés)

## Reszponzív design

Az alkalmazás három töréspontra van optimalizálva:
- Mobil (480px alatt): hamburger menü, 1 oszlopos elrendezés
- Tablet (768px alatt): adaptív elrendezés, 2 oszlopos galéria
- Desktop: teljes vízszintes navigáció, 3 oszlopos galéria

## Készítők

Webprogramozás 1 - Gyakorlat Beadandó

| Név | Neptun | Terület |
|---|---|---|
| Gaál Péter | GULX05 | Backend, adatbázis, autentikáció, CRUD |
| Molnár Ádám | MFG82Z | Frontend, reszponzív CSS, JavaScript, multimédia, deploy |

A részletes munkafelosztásért lásd a [CONTRIBUTORS.md](CONTRIBUTORS.md) fájlt.
