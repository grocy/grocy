set projectPath=%~dp0
if %projectPath:~-1%==\ set projectPath=%projectPath:~0,-1%

set releasePath=%projectPath%\.release
mkdir "%releasePath%"

for /f "tokens=*" %%a in ('type version.txt') do set version=%%a

del "%releasePath%\grocy_%version%.zip"
"build_tools\7za.exe" a -r "%releasePath%\grocy_%version%.zip" "%projectPath%\*" -xr!.* -xr!build_tools -xr!build.bat -xr!composer.json -xr!composer.lock -xr!composer.phar -xr!grocy.phpproj -xr!grocy.phpproj.user -xr!grocy.sln -xr!bower.json
"build_tools\7za.exe" d "%releasePath%\grocy_%version%.zip" data\*.*
