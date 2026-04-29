# Filmtár Gyakorlat — Nethely deploy útmutató

Cél: a projekt feltöltése egy **új, külön Nethely-fiókba**, hogy ne keveredjen az előadás-projekttel. Az alkalmazás a saját subdomain gyökerén fog futni.

## Áttekintés

| Mit | Hol |
|---|---|
| Új tárhely-fiók (külön email) | https://www.nethely.hu/ regisztráció |
| Subdomain | pl. `filmtar.nhely.hu`, `gaalpeter2.nhely.hu` — amit szabadon választasz |
| Teljes URL | `http://<az_új_subdomained>/` (gyökér, semmi almappa) |
| Adatbázis | saját, üres → ide jön a `g_*` 4 tábla |

A `deploy.zip`, `sql/database_nethely.sql`, `config.nethely.php` ugyanaz mint korábban — csak most az új fiókba kerül.

---

## 1. Új Nethely-fiók regisztráció

1. Nyisd meg: https://www.nethely.hu/
2. **Regisztráció** — egy email cím, ami **MÁS, mint amit az előadás-projekthez használtál**.
   - Lehet: a társad (Molnár Ádám) email-je, a te másik email-ed, vagy egy újonnan létrehozott Gmail-fiók.
3. Aktiváld a fiókot a kapott emailben.
4. Lépj be: https://www.nethely.hu/  → Belépés.

## 2. Ingyenes tárhely + subdomain aktiválása

1. Az új fiókban: **Ingyenes tárhely beindítása** → **Bekapcsolás**.
2. **Ingyenes domain választás** — válassz egy **szabad subdomaint**:
   - Próbálkozz: `filmtar.nhely.hu`, `filmtarweb.nhely.hu`, `gulx05.nhely.hu`, `mfg82z.nhely.hu`, vagy bármi szabad.
   - Az aktiválás 5–10 percet vehet igénybe.
3. **Domain csatolás**: Az Ön tárhelyei → Új domain csatolás → válaszd a most létrehozott subdomaint → Mentés.
4. **Webcím létrehozása**: Új webcím → előtag NÉLKÜL maradjon → **PHP verzió: 8** → Mentés.

## 3. Adatbázis létrehozása

1. Bal oldali menü: **Adatbázis** → **Új SQL adatbázis**.
2. Adatbázisnév: pl. `filmtar` (vagy ami szabad).
3. Jelszó: tetszőleges erős jelszó.
4. Mentés. Várj ~1 percet.
5. **Jegyezd fel** (vagy szúrd be ide a chatbe, ha azt akarod, hogy én csináljam a deploy-t):
   - **DB név** (pl. `xxxxxx_filmtar`)
   - **DB user** (általában a fiók-azonosító + db-suffix)
   - **DB jelszó** (amit te állítottál)
   - **DB host**: Nethely-en `localhost` szokott lenni
6. **FTP-adatok** ugyanezen a panelen: bal oldali menü → **FTP**:
   - **FTP host** (pl. `ftp.nhely.hu` vagy az új subdomained)
   - **FTP user** (általában a fiók-azonosító)
   - **FTP jelszó** (amit beállítottál)
   - **Port**: 21

## 4. SQL importálása phpMyAdmin-ban

1. **Adatbázis** szekció → a frissen létrehozott DB melletti **phpMyAdmin** ikon.
2. Bejelentkezés a DB user/jelszóval.
3. Bal oldalon kattints a saját adatbázisodra (kicsit várhat, amíg betölt).
4. Felül: **SQL** fül.
5. Helyi gépeden nyisd meg a `sql/database_nethely.sql` fájlt szövegszerkesztővel, **másold ki a teljes tartalmát**, és illeszd be a phpMyAdmin SQL-mezőjébe.
6. Kattints **Go** / **Végrehajtás**.
7. Bal oldalon megjelenik a 4 új tábla:
   - `g_felhasznalok`, `g_filmek`, `g_uzenetek`, `g_kepek`
8. Mehetsz tovább a 3 teszt-felhasználóval (`admin`/`admin123`, `teszt`/`teszt123`, `user1`/`jelszo123`) és a 12 magyar filmmel.

## 5. config.php szerkesztése

A `deploy.zip`-ben lévő `config.php` még helykitöltőkkel van:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'PASTE_NETHELY_DB_NAME_HERE');
define('DB_USER', 'PASTE_NETHELY_DB_USER_HERE');
define('DB_PASS', 'PASTE_NETHELY_DB_PASSWORD_HERE');
```

Két opció:

**A) Helyben szerkesztsd, aztán töltsd újra a zip-et:**
1. Csomagold ki a `deploy.zip`-et egy ideiglenes mappába.
2. Nyisd meg a `config.php`-t és írd át a 3 helykitöltőt a Nethely-es adataiddal.
3. Mentsd, majd ezt az egész mappát töltöd majd fel FTP-n.

**B) FileZilla-ban, feltöltés UTÁN szerkeszd:**
1. Tölts fel mindent (6. lépés).
2. FileZilla-ban: jobb klikk a `config.php`-re → **View/Edit** → módosítsd → mentsd → FileZilla automatikusan visszatölti.

## 6. FTP feltöltés a gyökérbe

1. **FileZilla** indítása (vagy WinSCP, Total Commander).
2. Csatlakozás az FTP-adatokkal:
   - Host: a Control Panel-ből (pl. `ftp.nhely.hu`)
   - User / jelszó: amit a panelben látsz
   - Port: 21
3. **Bal oldal (Local):** csomagold ki helyi gépen a `deploy.zip`-et egy átmeneti mappába (pl. `D:\filmtar-deploy\`), navigálj oda.
4. **Jobb oldal (Remote):** navigálj a gyökér web-mappádba. Nethely-en ez tipikusan `/web/` vagy `/htdocs/`. Ha `/web/index.html` ott van, az a helyes hely.
5. **Töröld** ott az alapértelmezett `index.html`-t (Nethely placeholder), ha van.
6. **Drag & drop** a kicsomagolt deploy mappa **összes tartalmát** a remote oldalra.

A végeredmény távoli oldalon ez a struktúra legyen (közvetlenül a `/web/` vagy `/htdocs/` ALATT):
```
/web/   (vagy /htdocs/)
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

## 7. uploads/ jogosultság

A képfeltöltés csak akkor működik, ha az `uploads/` mappa írható.

1. FileZilla-ban: jobb klikk az `uploads/` mappára (a remote oldalon).
2. **File permissions...** / **Fájl jogosultságok...**
3. Numerikus érték: **755** (vagy ha nem megy: **777**).
4. **OK**.

## 8. Tesztelés

Nyisd meg böngészőben:
```
http://<a_te_subdomained>/
```
(pl. `http://filmtar.nhely.hu/`)

Várt eredmény:
- Betöltődik a Filmtár főoldal.
- Látszik a hero szekció, a saját 5 mp-es videó, a Saul fia YouTube-ágyazás, a Google térkép.
- A navigáció működik: Főoldal / Képek / Kapcsolat / CRUD / Bejelentkezés.

**Ellenőrző-kör:**
1. **Regisztrálj** új felhasználót — ha nem kapsz „Database connection error"-t, az adatbázis-kapcsolat OK.
2. **Lépj be** `admin` / `admin123`-mal (a seed-ből).
3. **CRUD** — látnod kell a 12 magyar filmet a táblázatban.
4. Új film hozzáadása → szerkesztés → törlés.
5. **Képek** → tölts fel egy kicsi képet → galériában megjelenik.
6. **Kapcsolat** → küldj üzenetet → **Üzenetek** → ott van.

Ha mind megy, a deploy kész.

## 9. Hibakeresés

| Probléma | Megoldás |
|---|---|
| Üres / fehér oldal | A `config.php`-ben rosszak az adatbázis-adatok. Vagy az `.htaccess` nem támogatott — nevezd át átmenetileg `_htaccess`-re. |
| `Database connection error` | DB név / user / jelszó rossz a `config.php`-ben. |
| `Table 'xyz.g_filmek' doesn't exist` | A 4. lépést nem futtattad le. Menj phpMyAdmin → futtasd újra a `database_nethely.sql`-t. |
| CSS nem töltődik | Az `.htaccess` nem támogatott — töröld le, vagy nevezd át. Vagy a CSS fájl nem töltődött fel — ellenőrizd FTP-ben. |
| Képfeltöltés `move_uploaded_file failed` | `uploads/` jogosultság nem 755/777. Lásd 7. pont. |
| Az alapértelmezett Nethely placeholder oldal jön fel | Az `index.html`-t nem törölted ki a 6. lépésben. Töröld a remote oldalon. |

## 10. Beadáshoz szükséges adatok

```
=== Internetes elérhetőség ===
Weboldal URL:    http://<a_te_új_subdomained>/
GitHub repó:     https://github.com/Peti352/Filmtar-Webprog

=== Tárhely belépési adatok (oktatónak) ===
FTP Host:        (Nethely Control Panel, FTP szekció)
FTP Username:    (uo.)
FTP Password:    (te állítottad)
FTP Port:        21

=== MySQL ===
Host:            localhost
Database:        (Nethely Adatbázis szekcióból)
Username:        (uo.)
Password:        (te állítottad)
phpMyAdmin URL:  https://www.nethely.hu/   (Control Panel → Adatbázis → phpMyAdmin)

=== Teszt-felhasználó ===
Felhasználónév:  admin
Jelszó:          admin123
```

## Megjegyzés a `g_` prefixről

A `g_` előtag a tábla-neveken (`g_filmek` stb.) eredetileg azért lett bevezetve, hogy ne ütközzön az előadás-projekt tábláival, ha **közös DB-be** kerültek volna. Ebben az új scenarióban (külön fiók, külön DB) ez nem szükséges, **de nem is zavar** — a kód már így van, és bármikor törölhető (visszaállítható sima `filmek`/`felhasznalok` névre), ha úgy akarjátok.
