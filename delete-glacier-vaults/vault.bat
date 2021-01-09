@echo off
setlocal enabledelayedexpansion

set "vault=panamericaglacier"
set "file=archiveIds.txt"
set /a count=1

echo !vault! !file! !count!
if exist !file! (
	for /F "tokens=*" %%A in (!file!) do ( 
		set line=%%A
		echo Deleting Archive !line!
    	echo Count: !count!
    	aws glacier delete-archive --account-id 903452794896 --vault-name !vault! --archive-id=!line! --profile kevin
    	set /a count+=1
		echo %%A 
	)
) else (
	echo file does not exists 
)

:end
endlocal
exit /b
