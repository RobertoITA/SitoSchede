@echo off
for /l %%i in (1,1,200) do (
    setlocal enabledelayedexpansion
    set folder=00%%i
    set folder=!folder:~-3!
    set dest=X:\SitoSchede\pro_ita\PRODOTTI\!folder!
    
    if exist !dest! (
        rem Conta il numero di file nella cartella
        set filecount=0
        for %%f in ("!dest!\*") do (
            set /a filecount+=1
        )
        
        rem Verifica se ci sono meno di due file
        if !filecount! lss 2 (
            rem Verifica la presenza dei file specifici e li elimina se presenti
            if exist "!dest!\HTML - SCHEDA.php" (
                echo Eliminando il file HTML - SCHEDA.php nella cartella !dest!
                del "!dest!\HTML - SCHEDA.php"
            )
            if exist "!dest!\SCHEDA.csv" (
                echo Eliminando il file SCHEDA.csv nella cartella !dest!
                del "!dest!\SCHEDA.csv"
            )
        ) else (
            echo Due o più file trovati in !dest!, nessuna operazione.
        )
    ) else (
        echo La cartella !dest! non esiste.
    )
    endlocal
)

pause
