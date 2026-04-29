

from docx import Document
from docx.shared import Pt, Cm, Inches, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT
from docx.enum.section import WD_ORIENT
from docx.oxml.ns import qn
from docx.oxml import OxmlElement
import os


def set_cell_shading(cell, color_hex):
    
    shading = OxmlElement("w:shd")
    shading.set(qn("w:fill"), color_hex)
    shading.set(qn("w:val"), "clear")
    cell._tc.get_or_add_tcPr().append(shading)


def set_cell_border(cell, **kwargs):
    
    tc = cell._tc
    tcPr = tc.get_or_add_tcPr()
    tcBorders = OxmlElement("w:tcBorders")
    for edge in ("start", "top", "end", "bottom", "insideH", "insideV"):
        if edge in kwargs:
            element = OxmlElement("w:{}".format(edge))
            for attr_name, attr_val in kwargs[edge].items():
                element.set(qn("w:{}".format(attr_name)), str(attr_val))
            tcBorders.append(element)
    tcPr.append(tcBorders)


def add_page_break(doc):
    
    doc.add_page_break()


def set_run_font(run, name="Times New Roman", size=12, bold=False, italic=False, color=None):
    
    run.font.name = name
    run.font.size = Pt(size)
    run.font.bold = bold
    run.font.italic = italic
    if color:
        run.font.color.rgb = RGBColor(*color)
    r = run._element
    rPr = r.get_or_add_rPr()
    rFonts = rPr.find(qn("w:rFonts"))
    if rFonts is None:
        rFonts = OxmlElement("w:rFonts")
        rPr.insert(0, rFonts)
    rFonts.set(qn("w:ascii"), name)
    rFonts.set(qn("w:hAnsi"), name)
    rFonts.set(qn("w:cs"), name)


def add_heading_styled(doc, text, level=1):
    
    heading = doc.add_heading(level=level)
    run = heading.add_run(text)
    if level == 1:
        set_run_font(run, size=16, bold=True, color=(26, 26, 46))
    elif level == 2:
        set_run_font(run, size=14, bold=True, color=(26, 26, 46))
    elif level == 3:
        set_run_font(run, size=12, bold=True, color=(26, 26, 46))
    return heading


def add_paragraph_styled(doc, text, bold=False, italic=False, size=12, alignment=None, space_after=6, first_line_indent=None):
    
    para = doc.add_paragraph()
    run = para.add_run(text)
    set_run_font(run, size=size, bold=bold, italic=italic)
    if alignment is not None:
        para.alignment = alignment
    para.paragraph_format.space_after = Pt(space_after)
    if first_line_indent:
        para.paragraph_format.first_line_indent = Cm(first_line_indent)
    return para


def add_bullet_point(doc, text, bold_prefix=None, level=0):
    
    para = doc.add_paragraph(style="List Bullet")
    if bold_prefix:
        run_bold = para.add_run(bold_prefix)
        set_run_font(run_bold, size=12, bold=True)
        run_rest = para.add_run(text)
        set_run_font(run_rest, size=12)
    else:
        run = para.add_run(text)
        set_run_font(run, size=12)
    if level > 0:
        para.paragraph_format.left_indent = Cm(1.27 * (level + 1))
    return para


def add_code_block(doc, code_text):
    
    para = doc.add_paragraph()
    para.paragraph_format.space_before = Pt(6)
    para.paragraph_format.space_after = Pt(6)
    para.paragraph_format.left_indent = Cm(1)
    run = para.add_run(code_text)
    set_run_font(run, name="Courier New", size=9, color=(40, 40, 40))
    pPr = para._element.get_or_add_pPr()
    shd = OxmlElement("w:shd")
    shd.set(qn("w:fill"), "F0F0F0")
    shd.set(qn("w:val"), "clear")
    pPr.append(shd)
    return para


def create_styled_table(doc, headers, rows, col_widths=None):
    
    table = doc.add_table(rows=1 + len(rows), cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER

    header_row = table.rows[0]
    for i, header_text in enumerate(headers):
        cell = header_row.cells[i]
        cell.text = ""
        para = cell.paragraphs[0]
        run = para.add_run(header_text)
        set_run_font(run, size=10, bold=True, color=(255, 255, 255))
        para.alignment = WD_ALIGN_PARAGRAPH.CENTER
        set_cell_shading(cell, "1A1A2E")

    for row_idx, row_data in enumerate(rows):
        row = table.rows[row_idx + 1]
        for col_idx, cell_text in enumerate(row_data):
            cell = row.cells[col_idx]
            cell.text = ""
            para = cell.paragraphs[0]
            run = para.add_run(str(cell_text))
            set_run_font(run, size=10)
            if row_idx % 2 == 1:
                set_cell_shading(cell, "F4F4F8")

    if col_widths:
        for i, width in enumerate(col_widths):
            for row in table.rows:
                row.cells[i].width = Cm(width)

    tbl = table._tbl
    tblPr = tbl.tblPr if tbl.tblPr is not None else OxmlElement("w:tblPr")
    borders = OxmlElement("w:tblBorders")
    for border_name in ["top", "left", "bottom", "right", "insideH", "insideV"]:
        border = OxmlElement("w:{}".format(border_name))
        border.set(qn("w:val"), "single")
        border.set(qn("w:sz"), "4")
        border.set(qn("w:space"), "0")
        border.set(qn("w:color"), "999999")
        borders.append(border)
    tblPr.append(borders)

    return table


SCREENSHOT_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), "docs", "screenshots")

SCREENSHOT_MAP = {
    "5.1.a": ["1. Főoldal — hero.png"],
    "5.1.b": ["1. Főoldal — videok.png"],
    "5.1.c": ["1. Főoldal — terkep.png"],
    "5.2.a": ["2. Regisztráció.png"],
    "5.2.b": ["2. Bejelentkezés.png"],
    "5.3":   ["3. Képek menü.png"],
    "5.4":   ["4. Kapcsolat.png"],
    "5.5":   ["5. Üzenetek táblázat.png"],
    "5.6.a": ["6. CRUD lista.png"],
    "5.6.b": ["5.6.b uresurlap.png"],
    "5.6.c": ["5.6.cszerkesztes.png"],
    "5.6.d": ["5.6.d torles.png"],
    "7":     ["7. zarthamburger.PNG", "7. nyitotthamb.PNG", "7. képfügg.PNG"],
}


def add_screenshot(doc, key, caption=""):
    files = SCREENSHOT_MAP.get(key, [])
    inserted = False
    for fname in files:
        full = os.path.join(SCREENSHOT_DIR, fname)
        if os.path.exists(full):
            para = doc.add_paragraph()
            para.alignment = WD_ALIGN_PARAGRAPH.CENTER
            para.paragraph_format.space_before = Pt(12)
            para.paragraph_format.space_after = Pt(4)
            run = para.add_run()
            try:
                run.add_picture(full, width=Cm(14))
            except Exception:
                run.add_text(f"[KEPHIBA: {fname}]")
            inserted = True

    if not inserted:
        add_screenshot_placeholder(doc, caption)
        return

    if caption:
        cap_para = doc.add_paragraph()
        cap_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
        cap_para.paragraph_format.space_after = Pt(12)
        run2 = cap_para.add_run(caption)
        set_run_font(run2, size=10, italic=True, color=(100, 100, 100))


def add_screenshot_placeholder(doc, caption=""):

    para = doc.add_paragraph()
    para.alignment = WD_ALIGN_PARAGRAPH.CENTER
    para.paragraph_format.space_before = Pt(12)
    para.paragraph_format.space_after = Pt(6)
    run = para.add_run("[SCREENSHOT PLACEHOLDER]")
    set_run_font(run, size=11, italic=True, color=(150, 150, 150))

    if caption:
        cap_para = doc.add_paragraph()
        cap_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
        cap_para.paragraph_format.space_after = Pt(12)
        run2 = cap_para.add_run(caption)
        set_run_font(run2, size=10, italic=True, color=(100, 100, 100))


def generate_documentation():
    
    doc = Document()

    for section in doc.sections:
        section.top_margin = Cm(2.5)
        section.bottom_margin = Cm(2.5)
        section.left_margin = Cm(2.5)
        section.right_margin = Cm(2.5)

    style = doc.styles["Normal"]
    font = style.font
    font.name = "Times New Roman"
    font.size = Pt(12)
    rFonts = style.element.rPr.find(qn("w:rFonts")) if style.element.rPr is not None else None
    if rFonts is None:
        rPr = style.element.get_or_add_rPr()
        rFonts = OxmlElement("w:rFonts")
        rPr.insert(0, rFonts)
    rFonts.set(qn("w:ascii"), "Times New Roman")
    rFonts.set(qn("w:hAnsi"), "Times New Roman")
    rFonts.set(qn("w:cs"), "Times New Roman")

    for _ in range(6):
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(0)

    title_para = doc.add_paragraph()
    title_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = title_para.add_run("Filmtar")
    set_run_font(run, size=36, bold=True, color=(26, 26, 46))

    subtitle_para = doc.add_paragraph()
    subtitle_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = subtitle_para.add_run("Webalkalmazas Dokumentacio")
    set_run_font(run, size=24, bold=True, color=(226, 182, 22))

    line_para = doc.add_paragraph()
    line_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = line_para.add_run("_" * 50)
    set_run_font(run, size=12, color=(200, 200, 200))

    doc.add_paragraph()

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Webprogramozas 1 - Gyakorlat beadando")
    set_run_font(run, size=16, color=(80, 80, 80))

    doc.add_paragraph()

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Keszitettek:")
    set_run_font(run, size=14, color=(100, 100, 100))

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Gaál Péter")
    set_run_font(run, size=18, bold=True, color=(26, 26, 46))

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Neptun: GULX05")
    set_run_font(run, size=12, color=(100, 100, 100))

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Molnár Ádám")
    set_run_font(run, size=18, bold=True, color=(26, 26, 46))

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("Neptun: MFG82Z")
    set_run_font(run, size=12, color=(100, 100, 100))

    doc.add_paragraph()

    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run("2026")
    set_run_font(run, size=16, color=(100, 100, 100))

    add_page_break(doc)

    add_heading_styled(doc, "Tartalomjegyzek", level=1)

    toc_items = [
        ("1.", "Projekt attekintese", "3"),
        ("1.1.", "Az alkalmazas celja es funkcioi", "3"),
        ("1.2.", "Temavalasztas indoklasa", "3"),
        ("1.3.", "Hasznalt technologiak", "4"),
        ("1.4.", "Front-controller tervezesi minta", "4"),
        ("2.", "Rendszerkovetelmenyek", "5"),
        ("2.1.", "Szerver oldali kovetelmenyek", "5"),
        ("2.2.", "Kliens oldali kovetelmenyek", "5"),
        ("3.", "Adatbazis tervezes", "6"),
        ("3.1.", "ER diagram", "6"),
        ("3.2.", "Tablak reszletes leirasa", "6"),
        ("3.3.", "Kapcsolatok (kulso kulcsok)", "8"),
        ("4.", "Alkalmazas felepitese", "9"),
        ("4.1.", "Mappastruktura", "9"),
        ("4.2.", "Fajlok es szerepuk", "9"),
        ("4.3.", "Front-controller minta mukodese", "10"),
        ("4.4.", "PRG (Post-Redirect-Get) minta", "10"),
        ("5.", "Funkciok bemutatasa", "11"),
        ("5.1.", "Fooldal", "11"),
        ("5.2.", "Regisztracio es bejelentkezes", "11"),
        ("5.3.", "Kepgaleria es feltoltes", "12"),
        ("5.4.", "Kapcsolati urlap", "13"),
        ("5.5.", "Uzenetek oldal", "14"),
        ("5.6.", "CRUD muveletek", "14"),
        ("6.", "Biztonsag", "16"),
        ("7.", "Reszponziv dizajn", "17"),
        ("8.", "Osszefoglalas", "18"),
        ("9.", "Munkafelosztas", "19"),
        ("9.1.", "Gaal Peter felelossegi terulete", "19"),
        ("9.2.", "Molnar Adam felelossegi terulete", "19"),
        ("9.3.", "Kozos munka", "20"),
        ("10.", "Beadasi es belepesi adatok", "21"),
        ("10.1.", "Internetes elerhetoseg", "21"),
        ("10.2.", "Tarhely (FTP) belepesi adatok", "21"),
        ("10.3.", "MySQL adatbazis belepesi adatok", "21"),
        ("10.4.", "Teszt-felhasznalok", "22"),
        ("11.", "Irodalomjegyzek", "23"),
    ]

    for num, title, page in toc_items:
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(2)
        run_num = p.add_run(num + " ")
        is_main = not "." in num[:-1]  # Fo fejezet-e
        set_run_font(run_num, size=12, bold=is_main)
        run_title = p.add_run(title)
        set_run_font(run_title, size=12, bold=is_main)
        dots = " " + "." * (60 - len(num) - len(title)) + " "
        run_dots = p.add_run(dots)
        set_run_font(run_dots, size=12, color=(180, 180, 180))
        run_page = p.add_run(page)
        set_run_font(run_page, size=12)
        if num.count(".") > 1:
            p.paragraph_format.left_indent = Cm(1)

    add_page_break(doc)

    add_heading_styled(doc, "1. Projekt attekintese", level=1)

    add_heading_styled(doc, "1.1. Az alkalmazas celja es funkcioi", level=2)

    add_paragraph_styled(doc,
        "A Filmtar egy teljes erteku webalkalmazas, amely egy filmadatbazis "
        "kezeleset teszi lehetove. Az alkalmazas celja, hogy a felhasznalok "
        "kenyelmesen tallozhatnak a magyar filmek kozott, uj filmeket "
        "vehetnek fel az adatbazisba, szerkeszthetik es torolhetik azokat. "
        "Az alkalmazas emellett kozossegi funkciokal is rendelkezik: "
        "felhasznaloi regisztracio, bejelentkezes, kepfeltoltes es "
        "kapcsolati urlap reven.")

    add_paragraph_styled(doc, "Az alkalmazas fo funkcioi:")

    features = [
        "Felhasznaloi regisztracio es bejelentkezes (session alapu autentikacio)",
        "Filmek teljes koru kezelese (CRUD: letrehozas, olvasas, modositas, torles)",
        "Kepgaleria kepfeltoltesi lehetoseggel (csak bejelentkezett felhasznaloknak)",
        "Kapcsolati urlap ketszintu (kliens + szerver oldali) validacioval",
        "Uzenetek megtekintese (bejelentkezett felhasznaloknak)",
        "Fooldal hero szekcioval, video beagyazassal es Google Maps terkeppel",
        "Reszponziv (mobilbarat) megjelenes hamburger menuvel",
    ]
    for f in features:
        add_bullet_point(doc, f)

    add_heading_styled(doc, "1.2. Temavalasztas indoklasa", level=2)

    add_paragraph_styled(doc,
        "A Filmtar temavalasztas mellett tobb szempont is szolt. A filmadatbazis "
        "egy kozelitheto es koennyen ertelmezheto tema, amely lehetoseget ad "
        "a CRUD muveletek termeszetes bemutatasa. A filmeknek jol definialt "
        "tulajdonsagai vannak (cim, rendezo, ev, mufaj, ertekeles, leiras), "
        "amelyek kivaloan illusztraljak az adatbazis-tervezes es az "
        "urlapkezeles elveit.")

    add_paragraph_styled(doc,
        "A magyar filmek valasztasa kulonleges jelleget ad a projektnek, "
        "es egyben kulturalis erteket is kozvetit. Az adatbazisban 12 ismert "
        "magyar film szerepel, koztuk Oscar-dijas alkotasok (Saul fia, Mindenki) "
        "es ikonikus klasszikusok (A tanu, Macskafogo).")

    add_heading_styled(doc, "1.3. Hasznalt technologiak", level=2)

    tech_data = [
        ["PHP 8+", "Szerver oldali programozasi nyelv", "Uzleti logika, adatbazis muveletek, session kezeles"],
        ["MySQL 5.7+", "Relacios adatbazis-kezelo rendszer", "Adattarolas (felhasznalok, filmek, uzenetek, kepek)"],
        ["HTML5", "Jelolonyelv", "Oldal strukturaja, szemantikus elemek"],
        ["CSS3", "Stiluslapnyelv", "Megjelenes, reszponziv design, animaciok"],
        ["JavaScript", "Kliens oldali programozasi nyelv", "Form validacio, hamburger menu, lightbox galeria"],
        ["PDO", "PHP Data Objects", "Adatbazis absztrakcios reteg (prepared statements)"],
    ]

    create_styled_table(doc,
        ["Technologia", "Tipus", "Felhasznalasi terulet"],
        tech_data,
        col_widths=[4, 5, 8])

    doc.add_paragraph()

    add_heading_styled(doc, "1.4. Front-controller tervezesi minta", level=2)

    add_paragraph_styled(doc,
        "Az alkalmazas a front-controller tervezesi mintat valositja meg. "
        "Ez azt jelenti, hogy minden HTTP keres egyetlen belepesi ponton, "
        "az index.php fajlon keresztul erkezik. Az index.php a $_GET['page'] "
        "parameter alapjan donti el, mely oldal tartalom jelenjen meg. "
        "Ez a megoldasnak szamos elonye van:")

    fc_advantages = [
        "Kozponti hozzaferes-kezelest biztosit (pl. session ellenorzes)",
        "A POST keresesek feldolgozasa egyseges helyen tortenik (PRG minta)",
        "A navigacio es a sablonok (header/footer) automatikusan betoltodnek",
        "Biztonsagi ellenorzes: csak az engedelyezett oldalak erhetok el",
        "Konnyebb karbantartas: egyetlen fajlban latszik a teljes routing logika",
    ]
    for adv in fc_advantages:
        add_bullet_point(doc, adv)

    add_page_break(doc)

    add_heading_styled(doc, "2. Rendszerkovetelmenyek", level=1)

    add_heading_styled(doc, "2.1. Szerver oldali kovetelmenyek", level=2)

    server_reqs = [
        ["Apache", "2.4+", "Webszerver, mod_rewrite tamogatassal"],
        ["PHP", "8.0+", "Szerver oldali szkriptnyelv"],
        ["MySQL", "5.7+ / MariaDB 10.3+", "Relacios adatbazis-kezelo"],
        ["PDO MySQL", "Beepitett", "PHP adatbazis driver (php-mysql csomag)"],
    ]

    create_styled_table(doc,
        ["Szoftver", "Minimum verzio", "Megjegyzes"],
        server_reqs,
        col_widths=[4, 4.5, 8.5])

    doc.add_paragraph()

    add_paragraph_styled(doc,
        "Ajanlott fejlesztoi kornyezet: XAMPP, WAMP, MAMP vagy Laragon, "
        "amelyek az osszes szukseges szoftvert tartalmazzak.")

    add_heading_styled(doc, "2.2. Kliens oldali kovetelmenyek", level=2)

    client_reqs = [
        ["Google Chrome", "90+", "Teljes tamogatas"],
        ["Mozilla Firefox", "88+", "Teljes tamogatas"],
        ["Microsoft Edge", "90+", "Teljes tamogatas (Chromium alapu)"],
        ["Safari", "14+", "Teljes tamogatas"],
    ]

    create_styled_table(doc,
        ["Bongeszo", "Minimum verzio", "Tamogatottsag"],
        client_reqs,
        col_widths=[5, 4, 8])

    doc.add_paragraph()

    add_paragraph_styled(doc,
        "Az alkalmazas modern CSS3 technikat (CSS Grid, Flexbox, CSS valtozok, "
        "media queries) es HTML5 elemeket hasznal, ezert modern bongeszo "
        "szukseges a helyes megjeleniteshtez. JavaScript engedelyezese szukseges "
        "a kliens oldali urlapvalidaciohoz, a hamburger menuhoz es a lightbox "
        "galeriahoz.")

    add_page_break(doc)

    add_heading_styled(doc, "3. Adatbazis tervezes", level=1)

    add_paragraph_styled(doc,
        "Az alkalmazas egy 'filmtar' nevu MySQL adatbazist hasznal, amely negy "
        "tablat tartalmaz. Az adatbazis utf8mb4 karakterkeszlettel es "
        "utf8mb4_hungarian_ci rendezessel mukodik, biztositva a magyar "
        "karakterek helyes kezeleset.")

    add_heading_styled(doc, "3.1. ER diagram (szoveges abrazolas)", level=2)

    er_text = (
        "+------------------+          +------------------+\n"
        "|   felhasznalok   |          |      filmek      |\n"
        "+------------------+          +------------------+\n"
        "| PK id            |          | PK id            |\n"
        "|    felhasznalonev |          |    cim           |\n"
        "|    jelszo         |          |    rendezo       |\n"
        "|    csaladi_nev    |          |    ev            |\n"
        "|    utonev         |          |    mufaj         |\n"
        "|    email          |          |    ertekeles     |\n"
        "|    letrehozva     |          |    leiras        |\n"
        "+--------+---------+          +------------------+\n"
        "         |\n"
        "         | 1:N                       1:N\n"
        "         +-------------------+-------------------+\n"
        "         |                                       |\n"
        "         v                                       v\n"
        "+------------------+          +------------------+\n"
        "|    uzenetek      |          |      kepek       |\n"
        "+------------------+          +------------------+\n"
        "| PK id            |          | PK id            |\n"
        "|    nev            |          |    fajlnev       |\n"
        "|    email          |          |    eredeti_nev   |\n"
        "|    targy          |          | FK feltolto_id   |\n"
        "|    uzenet         |          |    feltoltve     |\n"
        "| FK kuldo_id      |          +------------------+\n"
        "|    kuldve         |\n"
        "+------------------+\n"
    )
    add_code_block(doc, er_text)

    add_paragraph_styled(doc,
        "A felhasznalok tabla a kozponti entitas, amelyhez az uzenetek "
        "es a kepek tablak kapcsolodnak kulso kulcsokon keresztul. A filmek "
        "tabla onallo entitas, nem kapcsolodik mas tablakhoz.")

    add_heading_styled(doc, "3.2. Tablak reszletes leirasa", level=2)

    add_heading_styled(doc, "felhasznalok tabla", level=3)
    add_paragraph_styled(doc,
        "A rendszer regisztralt felhasznaloit tarolja. A jelszo mezo "
        "PHP password_hash() fuggveny altal generalt bcrypt hash-t tartalmaz.")

    felh_rows = [
        ["id", "INT", "PK, AUTO_INCREMENT", "Egyedi azonosito"],
        ["felhasznalonev", "VARCHAR(50)", "NOT NULL, UNIQUE", "Bejelentkezesi nev"],
        ["jelszo", "VARCHAR(255)", "NOT NULL", "Bcrypt hash (password_hash)"],
        ["csaladi_nev", "VARCHAR(100)", "NOT NULL", "Csaladi nev"],
        ["utonev", "VARCHAR(100)", "NOT NULL", "Utonev"],
        ["email", "VARCHAR(100)", "NOT NULL", "E-mail cim"],
        ["letrehozva", "DATETIME", "DEFAULT CURRENT_TIMESTAMP", "Regisztracio idopontja"],
    ]
    create_styled_table(doc,
        ["Oszlop", "Tipus", "Megszoritas", "Leiras"],
        felh_rows,
        col_widths=[3.5, 3.5, 5, 5])

    doc.add_paragraph()

    add_heading_styled(doc, "filmek tabla", level=3)
    add_paragraph_styled(doc,
        "A filmadatbazis kozponti tablaja. Minden film adatait itt taroljuk. "
        "Ez a fo CRUD tabla, amelyen a letrehozas, olvasas, modositas es "
        "torles muveletek tortennnek.")

    filmek_rows = [
        ["id", "INT", "PK, AUTO_INCREMENT", "Egyedi azonosito"],
        ["cim", "VARCHAR(200)", "NOT NULL", "Film cime"],
        ["rendezo", "VARCHAR(100)", "NOT NULL", "Rendezo neve"],
        ["ev", "INT", "NOT NULL", "Megjelenes eve"],
        ["mufaj", "VARCHAR(100)", "NOT NULL", "Mufaj megnevezese"],
        ["ertekeles", "DECIMAL(3,1)", "DEFAULT NULL", "Ertekeles (0.0-10.0)"],
        ["leiras", "TEXT", "DEFAULT NULL", "Film rovid leirasa"],
    ]
    create_styled_table(doc,
        ["Oszlop", "Tipus", "Megszoritas", "Leiras"],
        filmek_rows,
        col_widths=[3.5, 3.5, 5, 5])

    doc.add_paragraph()

    add_heading_styled(doc, "uzenetek tabla", level=3)
    add_paragraph_styled(doc,
        "A kapcsolatfelveteli urlapon keresztul kuldott uzeneteket tarolja. "
        "A kuldo_id mezo NULL erteket vesz fel, ha vendeg (nem bejelentkezett "
        "felhasznalo) kuldte az uzenetet.")

    uzenetek_rows = [
        ["id", "INT", "PK, AUTO_INCREMENT", "Egyedi azonosito"],
        ["nev", "VARCHAR(100)", "NOT NULL", "Kuldo neve"],
        ["email", "VARCHAR(100)", "NOT NULL", "Kuldo e-mail cime"],
        ["targy", "VARCHAR(200)", "NOT NULL", "Uzenet targya"],
        ["uzenet", "TEXT", "NOT NULL", "Uzenet szovege"],
        ["kuldo_id", "INT", "DEFAULT NULL, FK", "Kuldo felhasznalo ID-ja"],
        ["kuldve", "DATETIME", "DEFAULT CURRENT_TIMESTAMP", "Kuldes idopontja"],
    ]
    create_styled_table(doc,
        ["Oszlop", "Tipus", "Megszoritas", "Leiras"],
        uzenetek_rows,
        col_widths=[3.5, 3.5, 5, 5])

    doc.add_paragraph()

    add_heading_styled(doc, "kepek tabla", level=3)
    add_paragraph_styled(doc,
        "A felhasznalok altal feltoltott galeria kepek nyilvantartasa. "
        "Csak bejelentkezett felhasznalok tolthetnek fel kepet, ezert a "
        "feltolto_id mezo kotelezo (NOT NULL).")

    kepek_rows = [
        ["id", "INT", "PK, AUTO_INCREMENT", "Egyedi azonosito"],
        ["fajlnev", "VARCHAR(255)", "NOT NULL", "Tarolt fajlnev (szerveren)"],
        ["eredeti_nev", "VARCHAR(255)", "NOT NULL", "Eredeti fajlnev (feltolteskor)"],
        ["feltolto_id", "INT", "NOT NULL, FK", "Feltolto felhasznalo ID-ja"],
        ["feltoltve", "DATETIME", "DEFAULT CURRENT_TIMESTAMP", "Feltoltes idopontja"],
    ]
    create_styled_table(doc,
        ["Oszlop", "Tipus", "Megszoritas", "Leiras"],
        kepek_rows,
        col_widths=[3.5, 3.5, 5, 5])

    add_heading_styled(doc, "3.3. Kapcsolatok (kulso kulcsok)", level=2)

    add_paragraph_styled(doc, "Az adatbazisban ket kulso kulcs (Foreign Key) kapcsolat talalhato:")

    fk_rows = [
        ["uzenetek.kuldo_id", "felhasznalok.id", "ON DELETE SET NULL", "Felhasznalo torlese eseten az uzenet megmarad, de a kuldo_id NULL-ra valt"],
        ["kepek.feltolto_id", "felhasznalok.id", "ON DELETE CASCADE", "Felhasznalo torlese eseten a felhasznalo altal feltoltott kepek is torlodnek"],
    ]
    create_styled_table(doc,
        ["Kulso kulcs", "Hivatkozott mezo", "Torlesi szabaly", "Magyarazat"],
        fk_rows,
        col_widths=[3.5, 3.5, 3.5, 6.5])

    doc.add_paragraph()

    add_paragraph_styled(doc,
        "Mindket kapcsolat ON UPDATE CASCADE szabalyt alkalmaz, ami azt jelenti, "
        "hogy ha a felhasznalok tablaban egy id ertek modosul, a kapcsolodo "
        "tablak automatikusan frissulnek.")

    add_page_break(doc)

    add_heading_styled(doc, "4. Alkalmazas felepitese", level=1)

    add_heading_styled(doc, "4.1. Mappastruktura", level=2)

    add_paragraph_styled(doc,
        "Az alkalmazas kovetkezo mappastrukturaba szervezodo:")

    tree = (
        "WebProg/\n"
        "|-- index.php              # Front controller (fo vezerlo)\n"
        "|-- config.php             # Adatbazis konfiguracio (PDO)\n"
        "|\n"
        "|-- css/\n"
        "|   |-- style.css          # Fo stiluslap (reszponziv)\n"
        "|\n"
        "|-- js/\n"
        "|   |-- main.js            # Hamburger menu, flash, lightbox\n"
        "|   |-- validation.js      # Kapcsolati urlap validacio\n"
        "|\n"
        "|-- pages/\n"
        "|   |-- fooldal.php        # Fooldal (hero, videok, terkep)\n"
        "|   |-- belepes.php        # Bejelentkezesi urlap\n"
        "|   |-- regisztracio.php   # Regisztracios urlap\n"
        "|   |-- kijelentkezes.php  # Kijelentkezesi oldal\n"
        "|   |-- kepek.php          # Kepgaleria + feltoltes\n"
        "|   |-- kapcsolat.php      # Kapcsolati urlap\n"
        "|   |-- uzenetek.php       # Uzenetek listazasa\n"
        "|   |-- crud.php           # Filmek CRUD kezelese\n"
        "|\n"
        "|-- templates/\n"
        "|   |-- header.php         # Fejlec sablon (navigacio)\n"
        "|   |-- footer.php         # Lablec sablon\n"
        "|\n"
        "|-- sql/\n"
        "|   |-- database.sql       # Adatbazis letrehozo szkript\n"
        "|   |-- seed.php           # Mintaadatok beszurasa PHP-bol\n"
        "|\n"
        "|-- uploads/               # Feltoltott kepek konyvtara\n"
        "|-- videos/                # Video fajlok konyvtara\n"
    )
    add_code_block(doc, tree)

    add_heading_styled(doc, "4.2. Fajlok es szerepuk", level=2)

    files_data = [
        ["index.php", "Front controller - egyetlen belepesi pont, routing, POST feldolgozas"],
        ["config.php", "PDO adatbazis kapcsolat letrehozasa, hibakezeles beallitasa"],
        ["css/style.css", "Teljes stiluslap: layout, szinek, reszponziv media queries"],
        ["js/main.js", "Hamburger menu, flash uzenet animacio, lightbox galeria"],
        ["js/validation.js", "Kapcsolati urlap kliens oldali validacioja (JS)"],
        ["pages/fooldal.php", "Fooldal: hero szekci, videok, Google Maps"],
        ["pages/belepes.php", "Bejelentkezesi urlap megjelenitese"],
        ["pages/regisztracio.php", "Regisztracios urlap (6 mezo)"],
        ["pages/kijelentkezes.php", "Kijelentkezesi visszajelzes oldal"],
        ["pages/kepek.php", "Kepgaleria + feltoltesi urlap"],
        ["pages/kapcsolat.php", "Kapcsolati urlap (szerver+kliens validacio)"],
        ["pages/uzenetek.php", "Uzenetek listazasa tablazatban"],
        ["pages/crud.php", "Filmek CRUD: listazas, letrehozas, szerkesztes, torles"],
        ["templates/header.php", "HTML fejlec, navigacio, felhasznaloi informacio"],
        ["templates/footer.php", "HTML lablec, JS betoltese"],
        ["sql/database.sql", "Adatbazis es tablak letrehozasa, mintaadatok (SQL)"],
        ["sql/seed.php", "Adatbazis inicializalas PHP-bol (password_hash)"],
    ]
    create_styled_table(doc,
        ["Fajl", "Szerep / Leiras"],
        files_data,
        col_widths=[5, 12])

    doc.add_paragraph()

    add_heading_styled(doc, "4.3. Front-controller minta mukodese", level=2)

    add_paragraph_styled(doc,
        "Az index.php az alkalmazas egyetlen belepesi pontja. Minden HTTP "
        "kerest ez a fajl fogad es dolgoz fel. A mukodese a kovetkezo:")

    fc_steps = [
        "1. Munkamenet (session) inditasa: session_start()",
        "2. Konfiguracio betoltese: config.php (adatbazis kapcsolat)",
        "3. Segefuggvenyek definialasa: flash(), getFlash(), bejelentkezveVan(), redirect()",
        "4. POST keresek feldolgozasa (ha van $_POST['action']):",
        "   - login: Bejelentkezes feldolgozas",
        "   - register: Regisztracio feldolgozas",
        "   - contact_submit: Kapcsolati urlap feldolgozas",
        "   - crud_create / crud_update / crud_delete: Film CRUD muveletek",
        "   - image_upload: Kepfeltoltes feldolgozas",
        "5. Utvonalvalasztas (routing): $_GET['page'] alapjan",
        "6. Engedelyezett oldalak ellenorzese (whitelist)",
        "7. Header sablon betoltese (templates/header.php)",
        "8. Flash uzenet megjelenitese (ha van)",
        "9. Oldal tartalom betoltese (pages/*.php)",
        "10. Footer sablon betoltese (templates/footer.php)",
    ]
    for step in fc_steps:
        if step.startswith("   "):
            add_paragraph_styled(doc, step, size=11, space_after=2)
        else:
            add_bullet_point(doc, step)

    add_heading_styled(doc, "4.4. PRG (Post-Redirect-Get) minta", level=2)

    add_paragraph_styled(doc,
        "Az alkalmazas kovetkezetesen alkalmazza a PRG (Post-Redirect-Get) "
        "mintat. Ennek lenyege, hogy a POST keresek feldolgozasa utan az "
        "alkalmazas HTTP 302 atiranyitast hajt vegre. Ez megakadalyozza, "
        "hogy az oldal ujratoltesevel (F5) a felhasznalo vetelenul ujra "
        "elkuldje az urlapadatokat.")

    add_paragraph_styled(doc, "A PRG minta folyamata:")

    prg_steps = [
        "1. A felhasznalo kitolti az urlapot es megnyomja a Kuldes gombot (POST keres)",
        "2. Az index.php feldolgozza a POST adatokat (pl. adatbazisba ment)",
        "3. Flash uzenetet tarol a session-ben (siker vagy hiba)",
        "4. HTTP 302 atiranyitast hajt vegre a redirect() fuggvennyel",
        "5. A bongeszo automatikusan GET kerest kuld az uj URL-re",
        "6. Az oldal betoltodik, a flash uzenet megjelenik, majd torlodik a session-bol",
    ]
    for step in prg_steps:
        add_bullet_point(doc, step)

    add_page_break(doc)

    add_heading_styled(doc, "5. Funkciok bemutatasa", level=1)

    add_heading_styled(doc, "5.1. Fooldal", level=2)

    add_paragraph_styled(doc,
        "A fooldal (fooldal.php) az alkalmazas nyitooldala, amely harom fo "
        "szekciot tartalmaz:")

    add_heading_styled(doc, "Hero szekio", level=3)
    add_paragraph_styled(doc,
        "Az oldal tetejen egy latvanyo hero szekci fogadja a latogatot. "
        "A 'Filmtar - A kedvenc filmjeid egy helyen' cimsort egy bemutato "
        "szoveg koveti, amely roviden ismerteti az alkalmazast. Ez alatt "
        "egy 'Rolunk' szekci reszletesebben bemutatja a projekt celjat "
        "es lehetosegeit.")

    add_screenshot(doc, "5.1.a", "5.1.a. abra: Fooldal - Hero szekio es Rolunk szoveg")

    add_heading_styled(doc, "Videok szekio", level=3)
    add_paragraph_styled(doc,
        "A videok szekio ket videot tartalmaz egymas mellett: egy helyi "
        "videolejatszot (<video> elem, sample.mp4) es egy YouTube "
        "beagyazott videot (<iframe>). Mindketto reszponzivan alkalmazkodik "
        "a kepernyomerthez.")

    add_screenshot(doc, "5.1.b", "5.1.b. abra: Fooldal - Videok szekio")

    add_heading_styled(doc, "Google Maps szekio", level=3)
    add_paragraph_styled(doc,
        "Az oldal aljan egy Google Maps terkep beagyazas lathatoott, amely "
        "egy budapesti cimet (1052 Budapest, Vaci utca 1.) jeleniti meg. "
        "A terkep iframe-ként van beagyazva, lazy loading tamogatassal.")

    add_screenshot(doc, "5.1.c", "5.1.c. abra: Fooldal - Google Maps beagyazas")

    add_heading_styled(doc, "5.2. Regisztracio es bejelentkezes", level=2)

    add_heading_styled(doc, "Regisztracio", level=3)
    add_paragraph_styled(doc,
        "A regisztracios oldal (regisztracio.php) egy urlapot jeleniti meg "
        "hat mezeovel: csaladi nev, utonev, felhasznalonev, e-mail, "
        "jelszo es jelszo megerosites. A validacio szerver oldalon tortenik "
        "az index.php-ban:")

    reg_validations = [
        "Minden mezo kitoltese kotelezo",
        "E-mail cim formatum ellenorzese (filter_var, FILTER_VALIDATE_EMAIL)",
        "Jelszavak egyezosenek vizsgalata",
        "Jelszo minimum hossz: 6 karakter",
        "Felhasznalonev egyedisegenek ellenorzese az adatbazisban",
        "Jelszo hashelese: password_hash($jelszo, PASSWORD_DEFAULT)",
    ]
    for v in reg_validations:
        add_bullet_point(doc, v)

    add_screenshot(doc, "5.2.a", "5.2.a. abra: Regisztracios urlap")

    add_heading_styled(doc, "Bejelentkezes", level=3)
    add_paragraph_styled(doc,
        "A bejelentkezesi oldal (belepes.php) ket mezobol all: felhasznalonev "
        "es jelszo. A feldolgozas soran a rendszer a felhasznalonev alapjan "
        "keresi ki a felhasznalot az adatbazisbol (prepared statement), majd "
        "a password_verify() fuggvennyel ellenorzi a jelszot. Sikeres "
        "bejelentkezes utan a felhasznalo adatai a $_SESSION['user'] tombben "
        "tarolodnak (jelszo nelkul!).")

    add_paragraph_styled(doc,
        "Ha a felhasznalo mar be van jelentkezve, az oldal errol tajekoztatja "
        "es nem jelenitit meg az urlapot.")

    add_screenshot(doc, "5.2.b", "5.2.b. abra: Bejelentkezesi urlap")

    add_heading_styled(doc, "Kijelentkezes", level=3)
    add_paragraph_styled(doc,
        "A kijelentkezes (kijelentkezes) nem kulon oldal, hanem az index.php "
        "kezeli. A session torlese ($_SESSION = []; session_destroy();) utan "
        "uj session indul a flash uzenethez, majd atiranyitas tortenik a "
        "fooldalra. A kijelentkezes.php csak egy visszajelzo oldal, amelyre "
        "normalis esetben nem jutunk el (az atiranyitas korabban megtortenik).")

    add_heading_styled(doc, "5.3. Kepgaleria es feltoltes", level=2)

    add_paragraph_styled(doc,
        "A kepgaleria oldal (kepek.php) ket fo funkciot lat el: "
        "a feltoltott kepek megjeleniteset galeria nezetben es "
        "uj kepek feltolteseteteaet.")

    add_heading_styled(doc, "Galeria nezet", level=3)
    add_paragraph_styled(doc,
        "A kepek CSS Grid elrendezesben jelennek meg (3 oszlop asztalon, "
        "2 oszlop tableten, 1 oszlop mobilon). Minden kep alatt latszik "
        "a feltolto neve es a feltoltes datuma. A kepekre kattintva egy "
        "lightbox overlay nyilik meg a nagyitott keptel (JavaScript altal "
        "vezerelt). Az ESC billentyuvel vagy az overlay-re kattintassal "
        "bezarhato.")

    add_heading_styled(doc, "Kepfeltoltes", level=3)
    add_paragraph_styled(doc,
        "Csak bejelentkezett felhasznalok tolthetnek fel kepet. "
        "A feltoltesi urlap enctype='multipart/form-data' attributummal "
        "rendelkezik. A feltoltes soran a kovetkezo ellenorzesek tortennek:")

    upload_checks = [
        "Bejelentkezes ellenorzese (session)",
        "Fajl erkezesenek ellenorzese ($_FILES['kep'])",
        "MIME tipus ellenorzese (mime_content_type): image/jpeg, image/png, image/gif, image/webp",
        "Kiterjesztes ellenorzese: jpg, jpeg, png, gif, webp",
        "Egyedi fajlnev generalasa (uniqid) az utkozesek elkerulesere",
        "Fajl athelyezese az uploads/ konyvtarba (move_uploaded_file)",
        "Adatbazisba mentes (kepek tabla)",
    ]
    for c in upload_checks:
        add_bullet_point(doc, c)

    add_screenshot(doc, "5.3", "5.3. abra: Kepgaleria CSS Grid elrendezesben")

    add_page_break(doc)

    add_heading_styled(doc, "5.4. Kapcsolati urlap", level=2)

    add_paragraph_styled(doc,
        "A kapcsolati oldal (kapcsolat.php) egy urlapot tartalmaz negy "
        "mezovel: nev, e-mail, targy es uzenet. Az urlap kettoa validacion "
        "esik at: kliens oldali (JavaScript) es szerver oldali (PHP).")

    add_heading_styled(doc, "Kliens oldali validacio (validation.js)", level=3)
    add_paragraph_styled(doc,
        "A JavaScript validacio az urlap submit esemenyre reagal. "
        "A validateContactForm() fuggveny ellenorzi az osszes mezot, es "
        "hibauzenetet jelenita meg a mezo mellett (span.error elemben). "
        "Nem hasznal HTML5 validacios attributumokat (required, pattern), "
        "a validacio teljes egeszeben JavaScript-ben tortenik.")

    js_validations = [
        "Nev: ne legyen ures, minimum 2 karakter",
        "E-mail: ne legyen ures, regex minta: /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/",
        "Targy: ne legyen ures, minimum 3 karakter",
        "Uzenet: ne legyen ures, minimum 10 karakter",
    ]
    for jv in js_validations:
        add_bullet_point(doc, jv)

    add_heading_styled(doc, "Szerver oldali validacio (index.php)", level=3)
    add_paragraph_styled(doc,
        "A szerver oldali validacio a POST keres feldolgozasakor fut le. "
        "Ellenorzi a kotelezoe mezokat es az e-mail formatumot "
        "(filter_var, FILTER_VALIDATE_EMAIL). Hiba eseten a hibauzenetek "
        "es az urlapadatok a session-ben tarolodnak, majd atiranyitas "
        "tortenik. Az adatok megorzese lehetove teszi, hogy a felhasznalo "
        "ne veszitse el a mar kitoltott mezok tartalmat.")

    add_paragraph_styled(doc,
        "Bejelentkezett felhasznalo eseten a nev es az e-mail mezo "
        "automatikusan kitoltodik a session adataival.")

    add_screenshot(doc, "5.4", "5.4. abra: Kapcsolati urlap validacios hibauzenetekkel")

    add_heading_styled(doc, "5.5. Uzenetek oldal", level=2)

    add_paragraph_styled(doc,
        "Az uzenetek oldal (uzenetek.php) a kapcsolati urlapon kuldott "
        "uzenetek listajat jelenitia meg tablazatos formatumban. Csak "
        "bejelentkezett felhasznalok szamara elerheto - nem bejelentkezett "
        "felhasznalok hibauzenet kapnak.")

    add_paragraph_styled(doc,
        "A tablazat oszlopai: sorszam, kuldo neve, e-mail, targy, uzenet "
        "szovege es a kuldes ideje. A vendeg (nem bejelentkezett) kuldonel "
        "'Vendeg' felirat jelenik meg. Az uzenetek a kuldes datuma szerint "
        "csokkenosorrendben jelennek meg (legujabb elol).")

    add_screenshot(doc, "5.5", "5.5. abra: Uzenetek oldal tablazatos nezetben")

    add_heading_styled(doc, "5.6. CRUD muveletek (filmek kezelese)", level=2)

    add_paragraph_styled(doc,
        "A CRUD (Create, Read, Update, Delete) funkcionalitas a filmek "
        "kezelesere szolgal. Egyetlen fajl (crud.php) kezeli mind a negy "
        "muveletet a $_GET['action'] parameter alapjan.")

    add_heading_styled(doc, "Listazas (Read)", level=3)
    add_paragraph_styled(doc,
        "Az alapertelmezett nezet (action=list) az osszes filmet egy "
        "tablazatban jelenitia meg. A tablazat oszlopai: #, Cim, Rendezo, "
        "Ev, Mufaj, Ertekeles, Muveletek. Minden sorban Szerkesztes es "
        "Torles gombok talalhatook. A filmek id szerint csokkeno sorrendben "
        "jelennek meg (legujabb elol). Az oldal tetejen egy 'Uj film "
        "hozzaadasa' gomb talalhato.")

    add_screenshot(doc, "5.6.a", "5.6.a. abra: Filmek listazasa tablazatban")

    add_heading_styled(doc, "Letrehozas (Create)", level=3)
    add_paragraph_styled(doc,
        "Az 'Uj film hozzaadasa' gombra kattintva (action=uj) egy urlap "
        "jelenik meg hat mezovel: cim, rendezo, ev, mufaj, ertekeles es "
        "leiras. A cim, rendezo es ev kitoltese kotelezo. Az ev mezo "
        "number tipusu, 1888 es az aktualis ev + 5 kozott fogad el "
        "ertekeket. Az ertekeles 0.0 es 10.0 kozotti DECIMAL ertek "
        "(0.1 lepeskozzel).")

    add_paragraph_styled(doc,
        "A mentes utan a rendszer a crud_create action-on keresztul "
        "szurja be az adatokat a filmek tablaba prepared statement "
        "segitsegevel, majd flash uzenettel tajekoztatja a felhasznalot "
        "es atiranyit a listara.")

    add_screenshot(doc, "5.6.b", "5.6.b. abra: Uj film hozzaadasa urlap")

    add_heading_styled(doc, "Szerkesztes (Update)", level=3)
    add_paragraph_styled(doc,
        "A Szerkesztes gombra kattintva (action=szerkeszt&id=X) ugyanaz "
        "az urlap jelenik meg, mint a letrehozasnal, de a mezok a film "
        "aktualis adataival vannak kitoltve. Az id parameter alapjan a "
        "rendszer lekerdezi a filmet az adatbazisbol. Ha a film nem "
        "talalhato, hibauzenet jelenik meg. A modositas a crud_update "
        "action-on keresztul tortenik UPDATE SQL utasitassal.")

    add_screenshot(doc, "5.6.c", "5.6.c. abra: Film szerkesztese")

    add_heading_styled(doc, "Torles (Delete)", level=3)
    add_paragraph_styled(doc,
        "A Torles gombra kattintva (action=torol&id=X) egy megerosito "
        "oldal jelenik meg, amely megjelenitia a film adatait (cim, rendezo, "
        "ev, mufaj, ertekeles) es megkerdezi a felhasznalot, biztosan "
        "torolni kivanja-e. Az 'Igen, torles' gombra kattintva a rendszer "
        "a crud_delete action-on keresztul torli a filmet a filmek tablabol. "
        "A 'Megsem' gomb visszairanyit a listara.")

    add_screenshot(doc, "5.6.d", "5.6.d. abra: Film torlesenek megerositese")

    add_page_break(doc)

    add_heading_styled(doc, "6. Biztonsag", level=1)

    add_paragraph_styled(doc,
        "Az alkalmazas tobb retuert biztonsagi megoldast alkalmaz a "
        "leggyakoribb webes tamadasok ellen. Az alabbiakban reszletezzuk "
        "az egyes vedelmeket.")

    add_heading_styled(doc, "Jelszo hasheles (Password Hashing)", level=2)
    add_paragraph_styled(doc,
        "A felhasznalok jelszavait soha nem taroljuk nyers szovegkent. "
        "A regisztracio soran a password_hash() fuggveny bcrypt "
        "algoritmussalhaseli a jelszot (PASSWORD_DEFAULT). A bejelentkezes "
        "soran a password_verify() fuggveny hasonlitja ossze a megadott "
        "jelszot a tarolt hash-sel. Ez biztositja, hogy meg adatbazis "
        "kompromittalas eseten sem fejthetok meg a jelszavak.")

    add_code_block(doc, "// Regisztracio:\n$hashelt_jelszo = password_hash($jelszo, PASSWORD_DEFAULT);\n\n// Bejelentkezes:\nif (password_verify($jelszo, $user['jelszo'])) { ... }")

    add_heading_styled(doc, "Prepared Statements (SQL Injection vedelem)", level=2)
    add_paragraph_styled(doc,
        "Minden adatbazis-lekerdezes PDO prepared statement-eket hasznal "
        "nevesitett parameterekkel (:param). Ez teljes vedelmet nyujt "
        "az SQL injection tamadasok ellen, mivel a parameterek ertekei "
        "soha nem epulnek be kozvetlenul az SQL utasitasba. A PDO "
        "ATTR_EMULATE_PREPARES beallitas false-ra van allitva, igy "
        "a szerver oldali prepared statement-ek hasznalodnak.")

    add_code_block(doc, "$stmt = $dbh->prepare(\n    'SELECT id FROM felhasznalok WHERE felhasznalonev = :fnev'\n);\n$stmt->execute([':fnev' => $felhasznalonev]);")

    add_heading_styled(doc, "XSS (Cross-Site Scripting) vedelem", level=2)
    add_paragraph_styled(doc,
        "Minden felhasznaloi bemenet, amely a HTML kimeneten megjelenik, "
        "az htmlspecialchars() fuggvenyen megy keresztul. Ez a fuggveny "
        "a <, >, &, \" es ' karaktereket HTML entitasokka alakitja, "
        "megakadalyozva a rosszindulatu JavaScript kodok futasat a "
        "felhasznalok bongeszojeben.")

    add_code_block(doc, "<?= htmlspecialchars($film['cim']) ?>\n<?= htmlspecialchars($flash['uzenet']) ?>")

    add_heading_styled(doc, "MIME tipus ellenorzes (kepfeltoltes)", level=2)
    add_paragraph_styled(doc,
        "A kepfeltoltes soran a rendszer nem csak a kiterjesztest, hanem "
        "a fajl tenyleg MIME tipusat is ellenorzi a mime_content_type() "
        "fuggvennyel. Ez megakadalyozza, hogy rosszindulatu fajlokat "
        "(pl. PHP szkripteket) tolthessenek fel kep mezzoben. Csak a "
        "kovetkezo tipusok engedetyezettek: image/jpeg, image/png, "
        "image/gif, image/webp.")

    add_heading_styled(doc, "Session kezeles", level=2)
    add_paragraph_styled(doc,
        "Az alkalmazas PHP session-oket hasznal a felhasznaloi allapot "
        "kovetesere. A bejelentkezes utan a felhasznalo adatai "
        "(jelszo nelkul!) a $_SESSION['user'] tombben tarolodnak. "
        "A kijelentkezeskor a session teljesen megsemmisul "
        "($_SESSION = []; session_destroy();). A flash uzenetek szinten "
        "a session-ben tarolodnak, es automatikusan torlodnek az "
        "elso megjelenitest kovetoen (egyszeri megjelenites).")

    add_heading_styled(doc, "Engedelyezett oldalak whitelist", level=2)
    add_paragraph_styled(doc,
        "Az index.php egy engedelyezett oldalak listat ($engedelyezett_oldalak) "
        "tart fenn. Csak azok az oldalak erhetok el, amelyek ebben a listaban "
        "szerepelnek. Ha a felhasznalo nem letezoeleo oldalt ker, flash "
        "hibauzenet jelenik meg es atiranyitas tortenik a fooldalra. "
        "Ez megakadalyozza a tetszoleges fajl betolteset (Path Traversal).")

    add_page_break(doc)

    add_heading_styled(doc, "7. Reszponziv dizajn", level=1)

    add_paragraph_styled(doc,
        "Az alkalmazas teljes mertekben reszponziv, vagyis alkalmazkodik "
        "a kulonbozo kepernyomeretekhez (asztali gepek, tabletek, mobiltelefonok). "
        "A reszponziv megjelenites harom fo technikan alapul: CSS media queries, "
        "Flexbox layout es CSS Grid.")

    add_heading_styled(doc, "Media queries es breakpointok", level=2)

    add_paragraph_styled(doc,
        "Az alkalmazas ket fo breakpointot hasznal, amelyek a tabletes "
        "es mobil nezeteket valasztjak el az asztali nezettol:")

    bp_data = [
        ["max-width: 768px", "Tablet", "Hamburger menu, 2 oszlopos galeria, kisebb padding"],
        ["max-width: 480px", "Mobil", "1 oszlopos galeria, kisebb betumeret (14px), kompakt layout"],
    ]
    create_styled_table(doc,
        ["Breakpoint", "Eszkoz", "Valtozasok"],
        bp_data,
        col_widths=[4.5, 3, 9.5])

    doc.add_paragraph()

    add_heading_styled(doc, "Hamburger menu mobil nezetben", level=2)

    add_paragraph_styled(doc,
        "768px alatti kepernyon a navigacio rejtett allapotban van "
        "(max-height: 0; overflow: hidden;) es egy harom vonalu hamburger "
        "gomb jelenik meg. A gombra kattintva a JavaScript toggle-eli "
        "az 'active' osztalyt, amely megnyitja a navigaciot fuggoleges "
        "elrendezesben. A hamburger gomb animalt X-alakra valtozik "
        "(CSS transform). A menupont kattintasra automatikusan bezarul "
        "a mobil menu.")

    add_code_block(doc,
        "/* Hamburger gomb megjelenitese */\n"
        ".hamburger { display: flex; }\n\n"
        "/* Navigacio elrejtese */\n"
        "nav { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }\n\n"
        "/* Navigacio megjelenitese aktiv allapotban */\n"
        "nav.active { max-height: 500px; }")

    add_heading_styled(doc, "CSS Grid galeria", level=2)

    add_paragraph_styled(doc,
        "A kepgaleria CSS Grid-et hasznal a reszponziv elrendezeshez. "
        "Asztali gepen 3 oszlopos, tableten 2 oszlopos, mobilon 1 oszlopos "
        "elrendezest alkalmaz. A galeria elemek hover allapotban enyheon "
        "felskalazodasat (scale(1.03)) es arnyek-melyulest mutatnak, ami "
        "kelolemest ad az interakciohoz.")

    add_code_block(doc,
        "/* Asztali: 3 oszlop */\n"
        ".gallery { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.2rem; }\n\n"
        "/* Tablet (768px): 2 oszlop */\n"
        "@media (max-width: 768px) {\n"
        "    .gallery { grid-template-columns: repeat(2, 1fr); }\n"
        "}\n\n"
        "/* Mobil (480px): 1 oszlop */\n"
        "@media (max-width: 480px) {\n"
        "    .gallery { grid-template-columns: 1fr; }\n"
        "}")

    add_heading_styled(doc, "Egyeb reszponziv megoldasok", level=2)

    responsive_features = [
        "A Flexbox layout biztositja a header es navigacio rugalmas elrendezeset",
        "A tablazatok .table-responsive wrapper-ben vannak, igy vizszintesen gorgethetokke valnak kis kepernyon",
        "A kepek max-width: 100%-ra vannak allitva, igy soha nem lognak ki a szulo elembol",
        "A videok es terkepek szelessege 100%-ra allitva, magassaguk allitodik",
        "A betumeret mobilon 14px-re csokken (html font-size), az osszes rem ertek aranyosan csokken",
        "A gombok es urlapmezok merete szinten alkalmazkodik a kepernyomerethez",
    ]
    for rf in responsive_features:
        add_bullet_point(doc, rf)

    add_screenshot(doc, "7", "7. abra: Az alkalmazas mobil nezetben (hamburger menu es 1 oszlopos galeria)")

    add_page_break(doc)

    add_heading_styled(doc, "8. Osszefoglalas", level=1)

    add_paragraph_styled(doc,
        "A Filmtar webalkalmazas egy teljes erteku, PHP alapu "
        "filmadatbazis-kezelo rendszer, amely a modern webfejlesztes "
        "legjobb gyakorlatait alkalmazza. A projekt soran a kovetkezo "
        "fo celkituzeseket valosiotottuk meg:")

    summary_points = [
        "Teljes CRUD funkcionalitas megvalositasa a filmek kezelesere",
        "Felhasznaloi autentikacio (regisztracio, bejelentkezes, kijelentkezes) biztonkagos session kezelestel",
        "Front-controller tervezesi minta alkalmazasa kozponti routing-gal",
        "PRG (Post-Redirect-Get) minta kovetkezetes hasznalata",
        "Tobbretegu biztonsag: jelszohasheles, prepared statements, XSS vedelem, MIME ellenorzes",
        "Ketszintu urlapvalidacio (kliens oldali JavaScript + szerver oldali PHP)",
        "Teljes mertekben reszponziv dizajn harom breakpointtal",
        "Interaktiv elemek: lightbox galeria, hamburger menu, flash uzenetek",
        "Letisztuolt, jol szervezett kodstruktura (sablonok, oldalak, statikus erofforrasok szeparalasa)",
    ]
    for sp in summary_points:
        add_bullet_point(doc, sp)

    add_paragraph_styled(doc,
        "Az alkalmazas tovabbfejlesztesi lehetosegei koze tartozik a "
        "filmek kereseese es szurese, lapozas (paginacio), felhasznaloi "
        "jogosultsagi szintek (admin/user), filmekhez fuzott kommentek, "
        "valamint RESTful API kialakitasa. A jelenlegi verzio stabil "
        "alapot biztosit ezekhez a bovitesekhez.")

    add_page_break(doc)

    add_heading_styled(doc, "9. Munkafelosztas", level=1)

    add_paragraph_styled(doc,
        "A projektet ket fos csoportmunkaban keszitettuk el. Az alabbi tablazat "
        "rogziti, hogy a ket hallgato kozul ki melyik feladatreszt valositotta meg.")

    munkafelosztas = [
        ["Hallgato",        "Neptun", "Felelossegi terulet"],
        ["Gaal Peter",      "GULX05", "Backend, adatbazis, autentikacio, CRUD, deploy"],
        ["Molnar Adam",     "MFG82Z", "Frontend, reszponziv CSS, JavaScript, multimedia"],
    ]
    create_styled_table(doc, munkafelosztas[0], munkafelosztas[1:], col_widths=[4, 3, 9])
    doc.add_paragraph()

    add_heading_styled(doc, "Gaal Peter (GULX05) - Backend es infrastruktura", level=2)
    gp_items = [
        "Adatbazis-sema tervezese: g_felhasznalok, g_filmek, g_uzenetek, g_kepek tablak es kulso kulcs kapcsolatok",
        "sql/database.sql es sql/seed.php adatbazis-inicializacios szkriptek",
        "Front-controller tervezesi minta megvalositasa (index.php) - routing, POST feldolgozas, PRG minta",
        "Autentikacio: regisztracio, bejelentkezes, kijelentkezes, session-kezeles",
        "CRUD muveletek a filmek tablahoz (pages/crud.php) - lista, uj, szerkesztes, torles utvonalakkal",
        "Bejelentkezesi es regisztracios oldalak (pages/belepes.php, pages/regisztracio.php)",
        "Apache .htaccess es config.php (PDO prepared statements) konfiguracio",
        "Internetes tarhelyre valo telepites - Nethely.hu, FTP feltoltes, MySQL import",
        "Megosztott tarhelyhez g_ tabla-prefix bevezetese az utkozesek elkerulesere",
    ]
    for it in gp_items:
        add_bullet_point(doc, it)

    add_heading_styled(doc, "Molnar Adam (MFG82Z) - Frontend, multimedia, deploy-csomag", level=2)
    ma_items = [
        "HTML5 sablonok: templates/header.php (navigacio, felhasznaloi info), templates/footer.php",
        "Teljes reszponziv CSS3 stiluslap (css/style.css) - Flexbox, Grid, ket breakpoint media query",
        "JavaScript funkciok (js/main.js): hamburger menu, lightbox kepgaleria, flash uzenet animacio",
        "Kapcsolati urlap kliens oldali validacioja (js/validation.js) - regex, hossz-ellenorzes",
        "Tartalmi oldalak: fooldal, kepgaleria + feltoltes, kapcsolati urlap, uzenetek listazasa",
        "Multimedia: 5 masodperces sajat intro video, YouTube beagyazas (Saul fia hivatalos elozetes), Google terkep",
        "Nethely deployment csomag es utmutato (NETHELY_DEPLOY.md, config.nethely.php, sql/database_nethely.sql)",
        "Kepgaleria es uzenetek SQL-lekerdezesek prefix-atirasa",
    ]
    for it in ma_items:
        add_bullet_point(doc, it)

    add_heading_styled(doc, "Kozos munka", level=2)
    kozos_items = [
        "Tema-valasztas (magyar filmadatbazis), tervezes, kovetelmeny-elemzes",
        "Kod-attekintes, hibajavitas, refaktoralas",
        "Dokumentacio osszeallitasa, kepernyokepek keszitese",
        "Tesztelek a hostolt alkalmazason",
    ]
    for it in kozos_items:
        add_bullet_point(doc, it)

    add_page_break(doc)

    add_heading_styled(doc, "10. Beadasi es belepesi adatok", level=1)

    add_paragraph_styled(doc,
        "Az alabbi adatok szuksegesek az alkalmazas elerheteseghez es ellenorzesehez. "
        "A jelszavakat csak az oktato szamara, a megoldas javitasahoz adjuk meg.")

    add_heading_styled(doc, "10.1. Internetes elerhetoseg", level=2)
    elerheto_rows = [
        ["Weboldal URL",   "http://filmtar.nhely.hu/"],
        ["GitHub repo",    "https://github.com/Peti352/Filmtar-Webprog"],
    ]
    create_styled_table(doc, ["Megnevezes", "Cim"], elerheto_rows, col_widths=[5, 11])
    doc.add_paragraph()

    add_heading_styled(doc, "10.2. Tarhely (FTP) belepesi adatok", level=2)
    ftp_rows = [
        ["FTP host",       "ftp.nethely.hu"],
        ["FTP felhasznalo","filmtar"],
        ["FTP jelszo",     "Webprog-1!"],
        ["FTP port",       "21"],
    ]
    create_styled_table(doc, ["Megnevezes", "Ertek"], ftp_rows, col_widths=[5, 11])
    doc.add_paragraph()

    add_heading_styled(doc, "10.3. MySQL adatbazis belepesi adatok", level=2)
    db_rows = [
        ["DB host",        "localhost"],
        ["DB nev",         "filmtar"],
        ["DB felhasznalo", "filmtar"],
        ["DB jelszo",      "Webprog-1!"],
        ["phpMyAdmin",     "https://www.nethely.hu/  (Adatbazis menu -> phpMyAdmin)"],
    ]
    create_styled_table(doc, ["Megnevezes", "Ertek"], db_rows, col_widths=[5, 11])
    doc.add_paragraph()

    add_heading_styled(doc, "10.4. Teszt-felhasznalok", level=2)
    add_paragraph_styled(doc, "A seed-adatokban harom teszt-felhasznalo van letrehozva, amelyekkel az alkalmazas funkcioi azonnal kiprobalhatok:")
    user_rows = [
        ["admin", "admin123",  "Adminisztrator (alapertelmezett)"],
        ["teszt", "teszt123",  "Teszt-felhasznalo"],
        ["user1", "jelszo123", "Normal felhasznalo"],
    ]
    create_styled_table(doc, ["Felhasznalonev", "Jelszo", "Szerepkor"], user_rows, col_widths=[4, 4, 8])
    doc.add_paragraph()

    add_paragraph_styled(doc,
        "Megjegyzes: Az alkalmazas reszponziv, igy mobil bongeszobol is megfeleloen "
        "megtekitheto. A bejelentkezes utan az 'Uzenetek' menupont es a CRUD muveletek "
        "is elerhetove valnak.")

    add_page_break(doc)

    add_heading_styled(doc, "11. Irodalomjegyzek", level=1)

    references = [
        [
            "PHP Official Documentation",
            "https://www.php.net/manual/en/",
            "A PHP nyelv hivatalos dokumentacioja. Hasznalva: PDO, password_hash(), "
            "password_verify(), session_start(), htmlspecialchars(), filter_var(), "
            "mime_content_type(), move_uploaded_file() fuggvenyek referenciakent.",
        ],
        [
            "MDN Web Docs - Mozilla Developer Network",
            "https://developer.mozilla.org/",
            "A webes technologiak (HTML, CSS, JavaScript) atfogo referenciaja. "
            "Hasznalva: CSS Grid, Flexbox, Media Queries, addEventListener(), "
            "classList, DOM manipulacio dokumentaciokent.",
        ],
        [
            "W3Schools Online Web Tutorials",
            "https://www.w3schools.com/",
            "Webtechnologiai oktatoanyagok es referenciak. Hasznalva: HTML5 "
            "elemek, CSS selektorok, JavaScript alapok, PHP szintaxis peldakent.",
        ],
        [
            "MySQL 8.0 Reference Manual",
            "https://dev.mysql.com/doc/refman/8.0/en/",
            "A MySQL adatbazis-kezelo rendszer hivatalos dokumentacioja. "
            "Hasznalva: CREATE TABLE, INSERT, UPDATE, DELETE, FOREIGN KEY, "
            "AUTO_INCREMENT szintaxis referenciakent.",
        ],
        [
            "OWASP - Open Web Application Security Project",
            "https://owasp.org/",
            "Webalkalmazas biztonsagi iranyelvek es ajanlasok. "
            "Hasznalva: SQL Injection, XSS, Password Storage "
            "legjobb gyakorlatok forrasakent.",
        ],
    ]

    for i, ref in enumerate(references, 1):
        p = doc.add_paragraph()
        p.paragraph_format.space_after = Pt(8)

        run_num = p.add_run("[{}] ".format(i))
        set_run_font(run_num, size=11, bold=True)

        run_title = p.add_run(ref[0])
        set_run_font(run_title, size=11, bold=True)

        p2 = doc.add_paragraph()
        p2.paragraph_format.space_after = Pt(2)
        p2.paragraph_format.left_indent = Cm(0.7)
        run_url = p2.add_run(ref[1])
        set_run_font(run_url, size=10, italic=True, color=(0, 100, 200))

        p3 = doc.add_paragraph()
        p3.paragraph_format.space_after = Pt(12)
        p3.paragraph_format.left_indent = Cm(0.7)
        run_desc = p3.add_run(ref[2])
        set_run_font(run_desc, size=10)

    output_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), "Filmtar_Dokumentacio.docx")
    doc.save(output_path)
    print("Dokumentacio sikeresen generalva: {}".format(output_path))
    print("A fajl megnyithato Microsoft Word-ben es PDF-be mentheto.")


if __name__ == "__main__":
    generate_documentation()
