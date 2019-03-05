set projectPath=%~dp0
if %projectPath:~-1%==\ set projectPath=%projectPath:~0,-1%
set projectPath=%projectPath%\..

set releasePath=%projectPath%\.release
mkdir "%releasePath%"

copy "%projectPath%\version.json" versiontemp.json
for /f "tokens=*" %%a in ('tools\jq.exe .Version versiontemp.json --raw-output') do set version=%%a
del versiontemp.json

del "%releasePath%\grocy_%version%.zip"
"tools\7za.exe" a -r "%releasePath%\grocy_%version%.zip" "%projectPath%\*" -xr!.* -xr!build.bat -xr!composer.json -xr!composer.lock -xr!package.json -xr!yarn.lock -xr!publication_assets
"tools\7za.exe" a "%releasePath%\grocy_%version%.zip" "%projectPath%\public\.htaccess"
"tools\7za.exe" rn "%releasePath%\grocy_%version%.zip" .htaccess public\.htaccess
"tools\7za.exe" d "%releasePath%\grocy_%version%.zip" data\*.* data\storage data\viewcache\*
