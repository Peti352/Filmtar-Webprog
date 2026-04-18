# 📤 Tárhely Feltöltés Útmutató - Filmtár Projekt

## 🎯 Cél
A Filmtár webalkalmazás feltöltése **INGYENES** internetes tárhelyre (PHP + MySQL támogatással).

---

## 🌐 1. TÁRHELY VÁLASZTÁSA

### **Ajánlott ingyenes tárhelyek (2026):**

| Tárhely | PHP | MySQL | Tárhely | Reklám | Regisztráció |
|---------|-----|-------|---------|--------|--------------|
| **InfinityFree** | ✅ 8.2 | ✅ 400MB | ✅ 5GB | ❌ Nincs | https://infinityfree.com |
| **000webhost** | ✅ 8.2 | ✅ 1GB | ✅ 300MB | ❌ Nincs | https://www.000webhost.com |
| **FreeHosting** | ✅ 8.1 | ✅ Unlimited | ✅ 10GB | ⚠️ Van | https://freehosting.com |

**JAVASOLT:** **InfinityFree** vagy **000webhost** (nincs reklám, stabil)

---

## 📝 2. REGISZTRÁCIÓ ÉS BEÁLLÍTÁS

### **2.1. InfinityFree Regisztráció**

1. **Nyisd meg:** https://infinityfree.com
2. **Kattints:** "Sign Up" (Regisztráció)
3. **Add meg:**
   - Email cím: `[a te emailedet]`
   - Jelszó: `[biztonságos jelszó]`
4. **Email megerősítés:** Kattints a kapott linken
5. **Bejelentkezés:** https://app.infinityfree.com

### **2.2. Új Domain/Subdomain Létrehozása**

1. **Control Panel** → **"Create Account"**
2. **Domain választás:**
   - **Saját domain:** Ha van (pl. `filmtar.com`)
   - **Ingyenes subdomain:** `filmtar.rf.gd` (vagy hasonló)
3. **Létrehozás** → Várj 5-10 percet az aktivációra

---

## 🗄️ 3. MYSQL ADATBÁZIS LÉTREHOZÁSA

### **3.1. Adatbázis beállítások lekérése**

1. **Control Panel** → **"MySQL Databases"**
2. **Új adatbázis létrehozása:**
   - Database Name: `filmtar_db` (vagy ami elérhető)
   - Kattints: **"Create Database"**

3. **Jegyzd fel ezeket az adatokat:**
   ```
   MySQL Host:     sql123.infinityfree.com
   Database Name:  epiz_123456_filmtar
   Username:       epiz_123456
   Password:       [automatikusan generált]
   phpMyAdmin:     https://sql123.infinityfree.com/phpmyadmin
   ```

### **3.2. Adatbázis séma importálása**

1. **Nyisd meg:** phpMyAdmin URL-t (fentebb)
2. **Bejelentkezés:** Username + Password (fentebb)
3. **Válaszd ki:** A létrehozott adatbázist (bal oldali menü)
4. **Import tab** → **"Choose File"**
5. **Válaszd ki:** `sql/database.sql` (a projekt mappából)
6. **Kattints:** **"Go"** (Import)
7. **Ellenőrzés:** Látnod kell 4 táblát:
   - `felhasznalok`
   - `filmek`
   - `uzenetek`
   - `kepek`

---

## 📁 4. FÁJLOK FELTÖLTÉSE (FTP)

### **4.1. FTP Adatok lekérése**

1. **Control Panel** → **"FTP Details"**
2. **Jegyzd fel:**
   ```
   FTP Host:       ftpupload.net
   FTP Username:   epiz_123456
   FTP Password:   [ugyanaz mint MySQL]
   FTP Port:       21
   ```

### **4.2. FileZilla Telepítése (Ingyenes FTP kliens)**

1. **Letöltés:** https://filezilla-project.org/download.php?type=client
2. **Telepítés:** Alapértelmezett beállításokkal
3. **Indítás:** FileZilla Client

### **4.3. Csatlakozás FTP-n**

1. **FileZilla** → **Fájl** → **Webhelykezelő**
2. **Új webhely:**
   - Protokoll: **FTP**
   - Kiszolgáló: `ftpupload.net`
   - Port: `21`
   - Bejelentkezés típusa: **Normál**
   - Felhasználó: `epiz_123456`
   - Jelszó: `[FTP jelszó]`
3. **Csatlakozás** → Ha kérdezi, elfogad minden tanúsítványt

### **4.4. Fájlok feltöltése**

1. **Bal oldal (Local):** Navigálj a projekt mappához
   ```
   /Users/peti352/Assignments/WebProg/
   ```

2. **Jobb oldal (Remote):** Navigálj a `htdocs` mappába
   ```
   /htdocs/
   ```

3. **Feltöltendő fájlok/mappák:**
   - ✅ `index.php`
   - ✅ `config.php` (később módosítjuk!)
   - ✅ `.htaccess`
   - ✅ `css/`
   - ✅ `js/`
   - ✅ `pages/`
   - ✅ `templates/`
   - ✅ `uploads/` (üresen, csak a mappa)
   - ✅ `videos/`
   - ❌ `sql/` (NEM kell feltölteni!)
   - ❌ `.git/` (NEM kell feltölteni!)
   - ❌ `README.md` (opcionális)

4. **Drag & Drop:** Húzd át a fájlokat bal oldalról jobb oldalra
5. **Várj:** Amíg minden feltöltődik (~2-5 perc)

---

## ⚙️ 5. CONFIG.PHP MÓDOSÍTÁSA

### **5.1. Szerkesztés FTP-n keresztül**

**FONTOS:** A `config.php` fájlt módosítani kell a tárhely adataival!

**Módszer 1: FileZilla-ban szerkesztés**
1. **Jobb klikk** a `config.php` fájlra (Remote oldalon)
2. **"View/Edit"** → Megnyílik egy szövegszerkesztőben
3. **Módosítsd** az adatokat (lásd lentebb)
4. **Mentés** → FileZilla automatikusan feltölti

**Módszer 2: Helyben szerkesztés, majd újra feltöltés**
1. Nyisd meg a helyi `config.php` fájlt
2. Módosítsd az adatokat (lásd lentebb)
3. Töltsd fel újra FTP-n

### **5.2. Módosítandó sorok**

**EREDETI (Helyi fejlesztés):**
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'filmtar');
define('DB_USER', 'root');
define('DB_PASS', '');
```

**MÓDOSÍTOTT (Tárhely):**
```php
define('DB_HOST', 'sql123.infinityfree.com');      // ← FTP adatokból
define('DB_NAME', 'epiz_123456_filmtar');         // ← Adatbázis neve
define('DB_USER', 'epiz_123456');                 // ← MySQL Username
define('DB_PASS', 'YOUR_MYSQL_PASSWORD_HERE');    // ← MySQL jelszó
```

**FONTOS:** Cseréld le a példa adatokat a SAJÁT tárhely adataidra!

---

## 🧪 6. TESZT ÉS ELLENŐRZÉS

### **6.1. Weboldal megnyitása**

1. **Böngésző:** Nyisd meg az oldal URL-jét
   ```
   http://filmtar.rf.gd
   ```
   (vagy a saját domain neved)

2. **Elvárt eredmény:**
   - ✅ Főoldal betöltődik
   - ✅ Látszik a navigáció
   - ✅ CSS megfelelően betöltődik

### **6.2. Funkciók tesztelése**

**Regisztráció teszt:**
1. **Kattints:** "Bejelentkezés" menü
2. **Regisztráció:** Új felhasználó létrehozása
   - Családi név: `Teszt`
   - Utónév: `Elek`
   - Felhasználónév: `teszt123`
   - Email: `teszt@example.com`
   - Jelszó: `teszt123`
3. **Elvárt:** "Sikeres regisztráció!" üzenet

**Bejelentkezés teszt:**
1. **Bejelentkezés:** `teszt123` / `teszt123`
2. **Elvárt:**
   - ✅ Bejelentkezve vagy
   - ✅ Fejlécen látszik: "Bejelentkezett: Teszt Elek (teszt123)"
   - ✅ Menüben megjelenik: "Üzenetek" és "Kilépés"

**CRUD teszt:**
1. **Kattints:** "CRUD" menü
2. **Új film hozzáadása:**
   - Cím: `Teszt Film`
   - Rendező: `Teszt Rendező`
   - Év: `2026`
   - Műfaj: `Teszt`
3. **Elvárt:** Film megjelenik a listában
4. **Szerkesztés és Törlés:** Teszteld ezeket is!

**Képfeltöltés teszt:**
1. **Kattints:** "Képek" menü
2. **Feltöltés:** Válassz ki egy kis JPG képet
3. **Elvárt:** Kép megjelenik a galériában

**Kapcsolat űrlap teszt:**
1. **Kattints:** "Kapcsolat" menü
2. **Küldés:** Tölts ki minden mezőt és küldj egy üzenetet
3. **Elvárt:** "Üzeneted sikeresen elküldtük!" üzenet
4. **Kattints:** "Üzenetek" menü
5. **Elvárt:** Az üzenet megjelenik a táblázatban

### **6.3. Hibakeresés**

**Ha üres/fehér oldal:**
1. **Ellenőrizd:** `config.php` adatbázis adatait
2. **phpMyAdmin:** Nézd meg hogy importálódott-e a séma

**Ha "Database connection error":**
1. **Ellenőrizd:** MySQL Host, Username, Password, Database Name
2. **phpMyAdmin:** Próbálj bejelentkezni ugyanezekkel az adatokkal

**Ha képek/CSS nem töltődnek:**
1. **Ellenőrizd:** FTP-n hogy feltöltődtek-e a `css/`, `js/`, `uploads/` mappák
2. **Jogosultságok:** `uploads/` mappa jogosultsága legyen 755 vagy 777

---

## 📋 7. DOKUMENTÁCIÓHOZ SZÜKSÉGES ADATOK

**A dokumentációba írd bele:**

```
=== INTERNETES ELÉRHETŐSÉG ===

Weboldal URL:
http://filmtar.rf.gd

GitHub Repository:
https://github.com/Peti352/Filmtar-Webprog

=== TÁRHELY BELÉPÉSI ADATOK (Oktatónak) ===

FTP Adatok:
- Host: ftpupload.net
- Username: epiz_123456
- Password: [FTP jelszó]
- Port: 21

MySQL Adatok:
- Host: sql123.infinityfree.com
- Database: epiz_123456_filmtar
- Username: epiz_123456
- Password: [MySQL jelszó]
- phpMyAdmin: https://sql123.infinityfree.com/phpmyadmin

Teszt Felhasználó:
- Felhasználónév: admin
- Jelszó: admin123
```

**FONTOS:** Cseréld le a példa adatokat a valós tárhely adataidra!

---

## ✅ 8. ELLENŐRZŐLISTA (Beadás előtt)

- [ ] Tárhely létrehozva és aktív
- [ ] MySQL adatbázis létrehozva
- [ ] `database.sql` importálva phpMyAdmin-on
- [ ] Minden fájl feltöltve FTP-n (`htdocs/` mappába)
- [ ] `config.php` módosítva a tárhely adataival
- [ ] `uploads/` mappa jogosultsága megfelelő (755/777)
- [ ] Weboldal URL működik
- [ ] Regisztráció működik
- [ ] Bejelentkezés működik
- [ ] CRUD műveletek működnek
- [ ] Képfeltöltés működik
- [ ] Kapcsolat űrlap működik
- [ ] Üzenetek megjelennek
- [ ] GitHub repo publikus és elérhető
- [ ] Dokumentációban benne vannak az FTP/MySQL adatok
- [ ] Dokumentáció PDF formátumban (Név-NeptunKód.pdf)

---

## 🆘 9. GYAKORI HIBÁK ÉS MEGOLDÁSOK

### **"Access denied for user"**
- **Ok:** Rossz MySQL Username vagy Password
- **Megoldás:** Ellenőrizd a Control Panel → MySQL Databases részt

### **"Unknown database"**
- **Ok:** Rossz Database Name
- **Megoldás:** phpMyAdmin-ban nézd meg a pontos adatbázis nevet

### **"Can't connect to MySQL server"**
- **Ok:** Rossz MySQL Host
- **Megoldás:** Control Panel-ből másold ki a pontos host nevet (pl. `sql123.infinityfree.com`)

### **CSS/JS nem töltődik**
- **Ok:** Rossz fájlútvonalak vagy nincs feltöltve
- **Megoldás:** Ellenőrizd FTP-n hogy a `css/` és `js/` mappák a `htdocs/` alatt vannak

### **Képfeltöltés nem működik**
- **Ok:** `uploads/` mappa jogosultsága nem megfelelő
- **Megoldás:**
  1. FileZilla → Jobb klikk `uploads/` → **"File permissions"**
  2. Állítsd be: **755** vagy **777**
  3. "Recurse into subdirectories" bejelölése
  4. OK

### **"Parse error" vagy "Syntax error"**
- **Ok:** PHP verzió eltérés vagy rossz fájl encoding
- **Megoldás:**
  1. Ellenőrizd hogy a tárhely PHP 8.0+ verzió
  2. Fájlok UTF-8 encoding-gal legyenek mentve

---

## 📞 10. SUPPORT ÉS TOVÁBBI SEGÍTSÉG

**InfinityFree Support:**
- Forum: https://forum.infinityfree.com
- Knowledge Base: https://docs.infinityfree.com

**000webhost Support:**
- Help Center: https://www.000webhost.com/forum

**PHP/MySQL Tutorial:**
- W3Schools: https://www.w3schools.com/php/
- PHP Manual: https://www.php.net/manual/en/

---

## 🎓 ÖSSZEFOGLALÁS

1. ✅ **Regisztráció** tárhely szolgáltatónál
2. ✅ **MySQL adatbázis** létrehozása és importálás
3. ✅ **FTP feltöltés** FileZilla-val
4. ✅ **config.php módosítása** tárhely adatokkal
5. ✅ **Tesztelés** minden funkció ellenőrzése
6. ✅ **Dokumentáció** frissítése az URL-ekkel és belépési adatokkal
7. ✅ **Beadás** Teams-en keresztül (csak a PDF dokumentáció!)

**Sikeres feltöltést és jó pontszámot kívánok! 🎉**
