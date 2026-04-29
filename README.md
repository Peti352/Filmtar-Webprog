# Filmtár - Magyar Filmadatbázis Webalkalmazás

## 📝 Projekt leírás

A Filmtár egy PHP-alapú webalkalmazás, amely magyar filmek adatbázisának kezelését teszi lehetővé. Az alkalmazás a **Webprogramozás 1** tantárgy gyakorlati beadandó feladataként készült.

## 🎯 Funkciók

### Felhasználói funkciók
- ✅ Regisztráció és bejelentkezés (session alapú)
- ✅ Filmek böngészése és keresése
- ✅ Képgaléria megtekintése
- ✅ Kapcsolatfelvétel űrlapon keresztül

### Bejelentkezett felhasználóknak
- ✅ Képfeltöltés a galériába
- ✅ Üzenetek megtekintése
- ✅ CRUD műveletek filmekkel (létrehozás, szerkesztés, törlés)

### Főoldal
- ✅ Hero szekció a projekt bemutatásával
- ✅ 2 videó (helyi + YouTube beágyazás)
- ✅ Google Maps integráció

## 🛠️ Technológiák

- **Backend:** PHP 8.0+ (PDO, session kezelés)
- **Frontend:** HTML5, CSS3 (Flexbox, Grid, Media Queries)
- **JavaScript:** Kliens oldali validáció, interaktivitás
- **Adatbázis:** MySQL / MariaDB
- **Tervezési minták:** Front-controller, PRG (Post-Redirect-Get)

## 📁 Projekt struktúra

```
WebProg/
├── index.php              # Front controller (fő belépési pont)
├── config.php             # Adatbázis konfiguráció
├── .htaccess              # Apache beállítások
│
├── css/
│   └── style.css          # Fő stíluslap (reszponzív)
│
├── js/
│   ├── main.js            # Hamburger menü, lightbox
│   └── validation.js      # Kapcsolati űrlap validáció
│
├── pages/
│   ├── fooldal.php        # Főoldal
│   ├── belepes.php        # Bejelentkezés
│   ├── regisztracio.php   # Regisztráció
│   ├── kepek.php          # Képgaléria
│   ├── kapcsolat.php      # Kapcsolat űrlap
│   ├── uzenetek.php       # Üzenetek listája
│   └── crud.php           # Filmek CRUD kezelése
│
├── templates/
│   ├── header.php         # Fejléc sablon
│   └── footer.php         # Lablec sablon
│
├── sql/
│   ├── database.sql       # Adatbázis séma
│   └── seed.php           # Mintaadatok
│
├── uploads/               # Feltöltött képek
└── videos/                # Videó fájlok
```

## 🚀 Telepítés

### 1. Előfeltételek
- PHP 8.0 vagy újabb
- MySQL 5.7+ vagy MariaDB 10.3+
- Apache webszerver (mod_rewrite engedélyezve)

### 2. Adatbázis létrehozása
```bash
mysql -u root -p < sql/database.sql
```

Vagy importáld phpMyAdmin-on keresztül a `sql/database.sql` fájlt.

### 3. Konfiguráció
Szerkeszd a `config.php` fájlt az adatbázis adataiddal:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'filmtar');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Mintaadatok betöltése (opcionális)
```bash
php sql/seed.php
```

### 5. Webszerver indítása
**XAMPP/WAMP/MAMP:**
Helyezd a projekt mappát a `htdocs/` könyvtárba és nyisd meg: `http://localhost/WebProg/`

**PHP beépített szerver:**
```bash
php -S localhost:8000
```

## 👤 Teszt felhasználók

A `database.sql` tartalmaz 3 teszt felhasználót:

| Felhasználónév | Jelszó | Szerepkör |
|----------------|--------|-----------|
| `admin` | `admin123` | Adminisztrátor |
| `teszt` | `teszt123` | Teszt felhasználó |
| `user1` | `jelszo123` | Normál felhasználó |

## 📊 Adatbázis séma

```
felhasznalok (id, felhasznalonev, jelszo, csaladi_nev, utonev, email, letrehozva)
    |
    ├── uzenetek (id, kuldo_id, nev, email, targy, uzenet, kuldve)
    └── kepek (id, feltolto_id, fajlnev, eredeti_nev, feltoltve)

filmek (id, cim, rendezo, ev, mufaj, ertekeles, leiras)
```

## 🔒 Biztonsági funkciók

- ✅ Jelszavak bcrypt hash-eléssel tárolva (`password_hash()`)
- ✅ Paraméterezett SQL lekérdezések (SQL injection védelem)
- ✅ XSS védelem (`htmlspecialchars()`)
- ✅ CSRF védelem session alapú autentikációval
- ✅ Fájlfeltöltés validáció (típus, méret ellenőrzés)

## 📱 Reszponzív tervezés

Az alkalmazás teljes mértékben reszponzív:
- 📱 Mobil nézet: Hamburger menü
- 💻 Tablet nézet: Adaptív táblázatok
- 🖥️ Desktop nézet: Teljes navigáció

## 📚 Dokumentáció

A részletes dokumentáció a `Filmtar_Dokumentacio.docx` fájlban található.

## 👥 Készítők

**Webprogramozás 1 - Gyakorlat Beadandó**

| Név | Neptun | Terület |
|---|---|---|
| Gaál Péter | GULX05 | Backend, adatbázis, autentikáció, CRUD |
| Molnár Ádám | MFG82Z | Frontend, reszponzív CSS, JavaScript, multimédia, deploy |

A részletes munkafelosztásért lásd a [CONTRIBUTORS.md](CONTRIBUTORS.md) fájlt.

Dátum: 2026

## 📄 Licenc

Ez a projekt oktatási célból készült.
