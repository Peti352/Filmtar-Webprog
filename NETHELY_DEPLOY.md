# Filmtár Gyakorlat — Nethely deploy útmutató

Cél: a projekt feltöltése a `peti352.nhely.hu/filmtar-webprog/` URL alá, az **előadás-projekt mellé**.

## Előkészítve neked

A repó gyökerében van a deploy-csomag és minden szükséges fájl:

| Fájl | Mire való |
|---|---|
| `deploy.zip` | Mindent tartalmaz, amit FTP-vel fel kell tölteni. A benne lévő `config.php` még nem érvényes adatbázis-adatokkal van kitöltve. |
| `sql/database_nethely.sql` | Ezt fogod a phpMyAdmin-ban lefuttatni. CREATE DATABASE / USE nélkül van — egyszerűen csak létrehozza a 4 új táblát és beszúrja a teszt-adatokat. |
| `config.nethely.php` | A `deploy.zip` ebből készült. Ha valamit később módosítasz, ezt szerkeszd, ne a zip-en belüli `config.php`-t. |

A 4 új tábla, ami létre fog jönni a meglévő adatbázisodban: `g_felhasznalok`, `g_filmek`, `g_uzenetek`, `g_kepek` — a `g_` prefix védi az előadás-projekt tábláit az ütközéstől.

---

## 1. Adatbázis adatok lekérése a Nethely paneljéből

1. Lépj be: https://www.nethely.hu/  → Belépés
2. Bal oldali menü: **Adatbázis** szekció
3. A meglévő (előadás-projekthez használt) adatbázisnál **jegyezd fel** a következőket:
   - **Adatbázis név** (pl. `peti352_db1`)
   - **Felhasználónév** (általában ugyanaz, mint a DB-név vagy hasonló)
   - **Jelszó** (ezt te állítottad be — ha nem emlékszel, lehet jelszót újraállítani)
   - **Host**: a Nethely-en `localhost` szokott lenni — a `config.nethely.php`-ben már ez van beírva.

---

## 2. Új táblák létrehozása phpMyAdmin-ban

1. Adatbázis szekció → kattints a meglévő DB-d melletti **phpMyAdmin** ikonra → bejelentkezés.
2. Bal oldali listában jelöld ki a **meglévő adatbázisodat**.
3. Felül: **SQL** fül.
4. Nyisd meg helyi gépeden a `sql/database_nethely.sql` fájlt egy szövegszerkesztővel, **másold ki a teljes tartalmát**, és illeszd be a phpMyAdmin SQL-mezőjébe.
5. Kattints **Go** / **Végrehajtás**.
6. Ellenőrzés: a bal oldalon a DB alatt megjelenik a 4 új tábla:
   - `g_felhasznalok`
   - `g_filmek`
   - `g_uzenetek`
   - `g_kepek`
7. **Ne aggódj** az előadás-projekt meglévő tábláiért — azokat semmilyen módon nem érintettük.

---

## 3. config.php szerkesztése

A `deploy.zip`-ben lévő `config.php` négy helykitöltőt tartalmaz:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'PASTE_NETHELY_DB_NAME_HERE');
define('DB_USER', 'PASTE_NETHELY_DB_USER_HERE');
define('DB_PASS', 'PASTE_NETHELY_DB_PASSWORD_HERE');
```

**Két opció a kitöltésre:**

**A) Helyben szerkesztsd, aztán töltsd újra a zip-et:**
1. Csomagold ki a `deploy.zip`-et egy ideiglenes mappába.
2. Nyisd meg a `config.php`-t és írd át a 3 helykitöltőt a Nethely-es adataiddal.
3. Tömörítsd újra (vagy egyszerűen csak ezt az egy fájlt töltsd majd fel utólag).

**B) FileZilla-ban, feltöltés UTÁN szerkeszd:**
1. Tölts fel mindent (4. lépés).
2. FileZilla-ban: jobb klikk a `config.php`-re → **View/Edit** → módosítsd → mentsd → FileZilla automatikusan visszatölti.

---

## 4. FTP feltöltés

1. **FileZilla** indítása. Csatlakozás a Nethely FTP adataival (Control Panel → FTP).
2. **Bal oldal (Local):** navigálj a `D:\Filmtar-Webprog\` mappába, és bontsd ki a `deploy.zip`-et lokálisan egy átmeneti mappába.
3. **Jobb oldal (Remote):** navigálj oda, ahol az előadás-projekted is van — ez Nethely-en általában `/web/` vagy `/htdocs/` (nézd meg, melyiket látod).
4. **Hozz létre egy új mappát** ott: jobb klikk → Új mappa → név: `filmtar-webprog`.
5. Lépj bele a `filmtar-webprog/` mappába a remote oldalon.
6. **Drag & Drop az összes fájlt** a kicsomagolt deploy mappából a remote `filmtar-webprog/`-ba.
7. Várd meg, amíg minden feltöltődik (~30 másodperc, csak 164 KB).

A végeredmény távoli oldalon ez a struktúra legyen:
```
.../filmtar-webprog/
├── index.php
├── config.php          ← itt kell helyes adatbázis-adatokkal lennie
├── .htaccess
├── css/style.css
├── js/main.js
├── js/validation.js
├── pages/...
├── templates/...
├── videos/sample.mp4
└── uploads/             (ide fognak menni a feltöltött képek)
```

---

## 5. uploads/ mappa jogosultság

A képfeltöltés csak akkor fog működni, ha az `uploads/` mappa írható.

1. FileZilla-ban: jobb klikk az `uploads/` mappára (a remote oldalon).
2. **File permissions...** / **Fájl jogosultságok...**
3. Numerikus érték: **755** (vagy ha nem megy, **777**).
4. **OK**.

---

## 6. Tesztelés

Nyisd meg böngészőben:
```
http://peti352.nhely.hu/filmtar-webprog/
```

Várt eredmény:
- Betöltődik a Filmtár főoldal.
- Látszik a hero szekció, az 5 mp-es videó, a Saul fia YouTube-ágyazás, a Google térkép.
- A navigáció működik: Főoldal / Képek / Kapcsolat / CRUD / Bejelentkezés.

**Ellenőrző kör:**
1. Próbálj **regisztrálni** egy új felhasználót — ha nem kapsz „Database connection error"-t, az adatbázis-kapcsolat rendben van.
2. Lépj be `admin` / `admin123`-mal (a seed-ből).
3. Menj a **CRUD**-ra — látnod kell a 12 magyar filmet a táblázatban.
4. Próbálj egy új filmet hozzáadni → szerkeszteni → törölni.
5. Menj a **Képek**-re → tölts fel egy kicsi képet → lásd, hogy megjelenik a galériában.
6. Menj a **Kapcsolat**-ra → küldj egy üzenetet → menj az **Üzenetek**-re → lásd, hogy ott van.

Ha mindegyik megy, a deploy kész.

---

## 7. Hibakeresés (gyakori esetek)

| Probléma | Megoldás |
|---|---|
| Üres / fehér oldal | A `config.php`-ben rosszak az adatbázis-adatok. PHP error_log megnézése. Vagy az `.htaccess` nem támogatott — próbáld átmenetileg átnevezni `_htaccess`-re. |
| `Database connection error` | DB név / user / jelszó rossz a `config.php`-ben. |
| `Table 'xyz.g_filmek' doesn't exist` | A 2. lépést nem futtattad le. Menj phpMyAdmin → futtasd újra a `database_nethely.sql`-t. |
| CSS nem töltődik | Az `.htaccess` nem támogatott — próbáld letörölni Nethely-ről. Vagy a CSS fájl nem töltődött fel — ellenőrizd FTP-ben. |
| Képfeltöltés `move_uploaded_file failed` | `uploads/` jogosultság nem 755/777. Lásd 5. pont. |
| 403 / 404 a gyökér URL-en | A `peti352.nhely.hu/` még az ELŐADÁS-projektet hozza, jó az. A gyakorlat-projekt a `/filmtar-webprog/` ALATT van. |

---

## 8. Beadáshoz szükséges adatok

Ha minden megy, gyűjtsd össze a dokumentációba:

```
=== Internetes elérhetőség ===
Weboldal URL:    http://peti352.nhely.hu/filmtar-webprog/
GitHub repó:     https://github.com/Peti352/Filmtar-Webprog

=== Tárhely belépési adatok (oktatónak) ===
FTP Host:        ftp.nethely.hu (vagy a panelben szereplő pontos host)
FTP Username:    (Nethely Control Panel-ből)
FTP Password:    (te állítottad)
FTP Port:        21

=== MySQL ===
Host:            localhost
Database:        (Nethely-es DB név)
Username:        (Nethely-es DB user)
Password:        (te állítottad)
phpMyAdmin URL:  https://www.nethely.hu/   (Control Panel → Adatbázis → phpMyAdmin)

=== Teszt-felhasználó ===
Felhasználónév:  admin
Jelszó:          admin123
```
