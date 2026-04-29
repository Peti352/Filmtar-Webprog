# Képernyőképek a dokumentációhoz

A `Filmtar_Dokumentacio.docx`-ben minden funkcióhoz tartozik egy `[SCREENSHOT PLACEHOLDER]`. A beadandó dokumentáció elkészítésekor ezeket valódi képernyőképekkel kell helyettesíteni a publikált tárhelyen lévő alkalmazásból.

## Szükséges képernyőképek

| # | Helyszín a docx-ben | Mit kell ábrázolnia |
|---|---|---|
| 1 | 5.1.a — Főoldal hero | A főoldal felső része a Filmtár címmel és bemutató szöveggel |
| 2 | 5.1.b — Videók szekció | Mindkét videó látszódjon: saját 5 mp-es intró és Saul fia YouTube |
| 3 | 5.1.c — Google térkép | A térkép-beágyazás a budapesti címmel |
| 4 | 5.2.a — Regisztrációs űrlap | A teljes regisztrációs form |
| 5 | 5.2.b — Bejelentkezési űrlap | A teljes bejelentkezési form |
| 6 | 5.3 — Képgaléria | CSS Grid elrendezésű galéria, legalább 3-4 képpel |
| 7 | 5.4 — Kapcsolati űrlap | Validációs hibaüzenetekkel kitöltött űrlap |
| 8 | 5.5 — Üzenetek oldal | Táblázatos nézet legalább 2-3 üzenettel |
| 9 | 5.6.a — CRUD lista | Filmek listázása táblázatban |
| 10 | 5.6.b — Új film | Az „Új film hozzáadása" form |
| 11 | 5.6.c — Szerkesztés | Kitöltött szerkesztő form |
| 12 | 5.6.d — Törlés megerősítés | A megerősítő dialógus |
| 13 | 7. — Mobil nézet | Hamburger menü kinyitva + 1 oszlopos galéria mobilon |

## Hogyan készítsd

- **Asztali nézet:** F12 / DevTools nélkül, normál ablakból. Lehetőleg 1280×720 vagy 1920×1080.
- **Mobil nézet:** Chrome DevTools → Toggle device toolbar → iPhone vagy Pixel.
- **Fájlnév-konvenció:** `01-fooldal-hero.png`, `02-fooldal-videok.png`, …, `13-mobil-nezet.png`.
- **Formátum:** PNG, ne JPEG (élesebb szöveg).

A képeket ide tedd: `docs/screenshots/`. Onnan illeszd be őket a Word-be, vagy regeneráláskor a `generate_docs.py`-ba.
