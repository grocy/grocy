pushd ..
set COMPOSER_FUND=0
call composer update
call yarn upgrade
popd
