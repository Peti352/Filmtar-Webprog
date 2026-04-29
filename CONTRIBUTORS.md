# Csapat és felelősségi körök

A Filmtár webalkalmazás a **Webprogramozás 1 - Gyakorlat** tárgy beadandó feladata, amelyet két fős csoportmunkában készítettünk el.

## Csapat

| Név | Neptun | GitHub | Felelősségi terület |
|---|---|---|---|
| Gaál Péter | GULX05 | [Peti352](https://github.com/Peti352) | Backend és infrastruktúra |
| Molnár Ádám | MFG82Z | [gabahodi7-dot](https://github.com/gabahodi7-dot) | Frontend és multimédia |

## Munkafelosztás

### Gaál Péter (GULX05) — Backend, adatbázis, infrastruktúra

- Adatbázis-séma tervezése: `g_felhasznalok`, `g_filmek`, `g_uzenetek`, `g_kepek` táblák, kulső kulcs kapcsolatok
- `sql/database.sql` és `sql/seed.php` adatbázis-inicializációs szkriptek
- Front-controller tervezési minta megvalósítása (`index.php`)
- Autentikáció: regisztráció, bejelentkezés, kijelentkezés, session-kezelés
- CRUD műveletek: filmek létrehozása, listázása, szerkesztése, törlése (`pages/crud.php`)
- Bejelentkezési és regisztrációs oldalak (`pages/belepes.php`, `pages/regisztracio.php`)
- Apache `.htaccess` konfiguráció és biztonsági beállítások
- `config.php` adatbázis-kapcsolat (PDO, prepared statementek)
- Megosztott tárhelyhez `g_` tábla-prefix bevezetése

### Molnár Ádám (MFG82Z) — Frontend, multimédia, deploy

- HTML5 sablonok: `templates/header.php` (navigáció), `templates/footer.php`
- Teljes reszponzív CSS3 stíluslap (`css/style.css`) — Flexbox, Grid, media queries
- JavaScript funkciók (`js/main.js`): hamburger menü, lightbox képgaléria, flash üzenetek
- Kapcsolati űrlap kliens oldali validációja (`js/validation.js`)
- Tartalmi oldalak: főoldal (`pages/fooldal.php`), képgaléria és feltöltés (`pages/kepek.php`), kapcsolati űrlap (`pages/kapcsolat.php`), üzenetek listázása (`pages/uzenetek.php`)
- Multimédia: 5 másodperces saját intró videó, YouTube beágyazás (Saul fia hivatalos előzetes), Google térkép
- Nethely deployment csomag és útmutató (`NETHELY_DEPLOY.md`, `config.nethely.php`, `sql/database_nethely.sql`)
- Képgaléria és üzenetek SQL-lekérdezések prefix-átírása

## Közösen

- Témaválasztás (magyar filmadatbázis), tervezés, követelmény-elemzés
- Kódáttekintés, hibajavítás, refaktorálás
- Dokumentáció (`Filmtar_Dokumentacio.docx`)
- Tesztelés és minőségbiztosítás
