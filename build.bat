set projectPath=%~dp0
if %projectPath:~-1%==\ set projectPath=%projectPath:~0,-1%

set releasePath=%projectPath%\.release
mkdir "%releasePath%"

for /f "tokens=*" %%a in ('build_tools\jq.exe .Version version.json --raw-output') do set version=%%a

del "%releasePath%\grocy_%version%.zip"
"build_tools\7za.exe" a -r "%releasePath%\grocy_%version%.zip" "%projectPath%\*" -xr!.* -xr!build_tools -xr!build.bat -xr!composer.json -xr!composer.lock -xr!package.json -xr!yarn.lock -xr!publication_assets
"build_tools\7za.exe" a "%releasePath%\grocy_%version%.zip" "%projectPath%\public\.htaccess"
"build_tools\7za.exe" rn "%releasePath%\grocy_%version%.zip" .htaccess public\.htaccess
"build_tools\7za.exe" d "%releasePath%\grocy_%version%.zip" data\*.* data\storage data\viewcache\*
