@echo off
set source=X:\SitoSchede\pro_ita\Scheda_base\logo.jpg

for /l %%i in (1,1,200) do (
    setlocal enabledelayedexpansion
    set folder=00%%i
    set folder=!folder:~-3!
    set dest=X:\SitoSchede\pro_ita\PRODOTTI\!folder!
    
    if exist !dest! (
        echo Copiando in !dest!
        copy "!source!" "!dest!\"
    ) else (
        echo La cartella !dest! non esiste.
    )
    endlocal
)

pause
