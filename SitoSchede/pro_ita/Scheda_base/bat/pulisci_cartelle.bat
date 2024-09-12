@echo off
for /l %%i in (2,1,200) do (
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
        
        rem Verifica se ci sono esattamente due file
        if !filecount! equ 2 (
            rem Verifica la presenza dei file specifici
            if exist "!dest!\HTML - SCHEDA.php" if exist "!dest!\SCHEDA.csv" (
                echo Eliminando i file HTML - SCHEDA.php e SCHEDA.csv nella cartella !dest!
                del "!dest!\HTML - SCHEDA.php"
                del "!dest!\SCHEDA.csv"
            ) else (
                echo File richiesti non trovati in !dest!, nessuna operazione.
            )
        ) else (
            echo Più di due file trovati in !dest!, nessuna operazione.
        )
    ) else (
        echo La cartella !dest! non esiste.
    )
    endlocal
)

pause
