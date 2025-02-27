set projectPath=%~dp0
if %projectPath:~-1%==\ set projectPath=%projectPath:~0,-1%
set projectPath=%projectPath%\..

set releasePath=%projectPath%\.release
mkdir "%releasePath%"

copy "%projectPath%\version.json" versiontemp.json
for /f "tokens=*" %%a in ('jq .Version versiontemp.json --raw-output') do set version=%%a
del versiontemp.json

del "%releasePath%\grocy_%version%.zip"
7za a -r "%releasePath%\grocy_%version%.zip" "%projectPath%\*" -xr!.* -xr!build.bat -xr!composer.json -xr!composer.lock -xr!package.json -xr!yarn.lock -xr!docs -xr!test -x!public\packages\@fortawesome\fontawesome-free\js -x!public\packages\@fortawesome\fontawesome-free\less -x!public\packages\@fortawesome\fontawesome-free\metadata -x!public\packages\@fortawesome\fontawesome-free\scss -x!public\packages\@fortawesome\fontawesome-free\sprites -x!public\packages\@fortawesome\fontawesome-free\svgs
7za a "%releasePath%\grocy_%version%.zip" "%projectPath%\public\.htaccess"
7za rn "%releasePath%\grocy_%version%.zip" .htaccess public\.htaccess
7za d "%releasePath%\grocy_%version%.zip" data\*.* data\storage data\viewcache\* changelog\__TEMPLATE.md
7za a "%releasePath%\grocy_%version%.zip" "%projectPath%\data\.htaccess"
7za rn "%releasePath%\grocy_%version%.zip" .htaccess data\.htaccess
