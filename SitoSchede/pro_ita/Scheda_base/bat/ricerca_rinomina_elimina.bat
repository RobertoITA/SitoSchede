@echo off
setlocal enabledelayedexpansion

rem Chiedi il nome del file da cercare
set /p filename=Inserisci il nome del file da cercare (includi estensione, es: file.txt): 

rem Itera nelle cartelle da 002 a 200
for /l %%i in (2,1,200) do (
    set "folder=00%%i"
    set "folder=!folder:~-3!"
    set "path=X:\SitoSchede\pro_ita\PRODOTTI\!folder!"

    rem Controlla se il file esiste nella cartella
    if exist "!path!\%filename%" (
        echo.
        echo File trovato in !path!\%filename%
        
        rem Chiedi all'utente cosa fare
        echo Cosa vuoi fare con il file "!path!\%filename%"?
        echo 1. Rinominare il file
        echo 2. Eliminare il file
        echo 3. Ignorare e passare al successivo
        set /p choice=Scegli un'opzione (1/2/3): 

        if "%choice%"=="1" (
            rem Richiedi nuovo nome del file
            set /p newname=Inserisci il nuovo nome per il file (includi estensione): 
            ren "!path!\%filename%" "!newname%"
            echo File rinominato in !newname!.
        ) else if "%choice%"=="2" (
            del "!path!\%filename%"
            echo File eliminato.
        ) else if "%choice%"=="3" (
            echo File ignorato.
        ) else (
            echo Scelta non valida, file ignorato.
        )
    )
)

echo Operazione completata.
pause
