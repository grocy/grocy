
This page is here to help you setup your devel environnment for starting to contribute to grocy.
Feel free to share your setup if you think it can increase developper productivity.

## Environment Set up

Install PHP7 : 
 - [Windows](https://windows.php.net/download#php-7.4)
 - [Linux](https://www.php.net/distributions/php-7.4.5.tar.bz2) but using your distro package manager will do the job.

Install Composer :

- [Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)
- [Linux](https://getcomposer.org/doc/00-intro.md#downloading-the-composer-executable) but using your distro package manager will do the job

Enable ext-fileinfo, ext-gd and pdo_sqlite in your php.ini, uncomment the line `extension=fileinfo`, `extension=gd2` and `extension=pdo_sqlite` in php.ini

Run Composer to install dependencies

```
composer install
```

Copy ./config-dist.php to grocy's data directory  (./data) as config.php.

Open newest config.php and set `MODE` to dev.
And for convinience you can disable authentication by setting `DISABLE_AUTH` to true

Create viewcache directory in `data` folder

Install node_modules (You'll need Yarn for simplicity since npm don't support well installing node_modules outside of package.json's folder when package.json don't have a version field.)
```
yarn install
```

---

Everything should be up and running by now, go to http://localhost:3000/ and see by yourself. 

Note : If auth is enabled, the default username is admin and default password is admin)

## Useful plugins for VSCode

You may use whatever you want, the following list gather good ones that we use to develop Grocy.
- [PHP Debug](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-debug)
- [PHP Intellisense](https://marketplace.visualstudio.com/items?itemName=felixfbecker.php-intellisense)
- [PHP Server](https://marketplace.visualstudio.com/items?itemName=brapifra.phpserver)
- [Laravel Blade Snippets](https://marketplace.visualstudio.com/items?itemName=onecentlin.laravel-blade)