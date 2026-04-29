@echo off
REM Filmtar dokumentacio (docx) ujragenerálasa.
REM Hasznalat: build_docs.bat
REM A generalt fajl: Filmtar_Dokumentacio.docx

python generate_docs.py
if errorlevel 1 (
    echo.
    echo HIBA: A dokumentacio generalas nem sikerult.
    echo Telepitsd a python-docx csomagot: pip install python-docx
    exit /b 1
)

echo.
echo Kesz: Filmtar_Dokumentacio.docx
echo Tipp: PDF-be mentes Word-bol -> Mentes maskent -> PDF
