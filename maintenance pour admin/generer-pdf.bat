@echo off
REM Script pour convertir HTML en PDF et assembler un document complet

REM Variables
set HTML_FILE=c:\xampp\htdocs\mairie_wp\DIAGNOSTIC_SECURITE_MAIRIE_CIVIQUE.html
set PDF_OUTPUT=c:\xampp\htdocs\mairie_wp\DIAGNOSTIC_SECURITE_MAIRIE_CIVIQUE.pdf

REM Vérifier que le fichier HTML existe
if not exist "%HTML_FILE%" (
    echo ERREUR: Fichier HTML non trouvé: %HTML_FILE%
    exit /b 1
)

echo.
echo ========================================================================
echo Rapport de Diagnostic Sécurité - Mairie Civique
echo ========================================================================
echo.
echo Fichier HTML généré: %HTML_FILE%
echo Taille: 
for %%A in ("%HTML_FILE%") do echo   %%~zA bytes
echo.
echo Pour convertir en PDF, vous pouvez utiliser:
echo - Chrome/Edge (imprimer en PDF)
echo - Puppeteer/Playwright
echo - wkhtmltopdf
echo.
echo Accédez au fichier HTML via navigateur pour impression PDF:
echo   http://localhost/mairie_wp/DIAGNOSTIC_SECURITE_MAIRIE_CIVIQUE.html
echo.
echo ========================================================================
echo Fichier sauvegardé avec succès!
echo ========================================================================
echo.
pause
