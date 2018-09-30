# grocy
ERP beyond your fridge

## Give it a try
- Public demo of the latest stable version &rarr; [https://demo.grocy.info](https://demo.grocy.info)
- Public demo of the latest pre-release version (current master branch) &rarr; [https://demo-prerelease.grocy.info](https://demo-prerelease.grocy.info)

## Motivation
A household needs to be managed. I did this so far (almost 10 years) with my first self written software (a C# windows forms application) and with a bunch of Excel sheets. The software is a pain to use and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete household management"-thing. ERP your fridge!

## How to install
> **NEW**
>
> There is now grocy-desktop if you want to run grocy without a webserver just like a normal (windows) desktop application.
>
> See https://github.com/berrnd/grocy-desktop or directly download the [latest release](https://releases.grocy.info/latest-desktop) - the installation is nothing more than just clicking 2 times "next"...

Just unpack the [latest release](https://releases.grocy.info/latest) on your PHP (SQLite extension required, currently only tested with PHP 7.2) enabled webserver (webservers root should point to the `public` directory), copy `config-dist.php` to `data/config.php`, edit it to your needs, ensure that the `data` directory is writable and you're ready to go, (to make writable `chown -R www-data:www-data data/`). Default login is user `admin` with password `admin`, please change the password immediately (see user menu).

Alternatively clone this repository and install Composer and Yarn dependencies manually.

If you use nginx as your webserver, please include `try_files $uri /index.php;` in your location block.

If, however, your webserver does not support URL rewriting, set `DISABLE_URL_REWRITING` in `data/config.php` (`Setting('DISABLE_URL_REWRITING', true);`).

## How to run using Docker

The docker images build are based on [Alpine](https://hub.docker.com/_/alpine/), with an extremelly low footprint (less than 10 MB for nginx, and less than 70MB for grocy with php-fm. That number is eventually bumped up to 353MB after all the dependencies are downloaded, however). Anyhow, to run using docker just do the following:

```
> docker-compose up
```

And grocy should be accessible via `http(s)://localhost/`. The https option will work. However, since the certificate is self-signed, most browsers will complain.

## How to update
Just overwrite everything with the latest release while keeping the `data` directory, check `config-dist.php` for new configuration options and add them to your `data/config.php` (the default from values `config-dist.php` will be used for not in `data/config.php` defined settings). Just to be sure, please empty `data/viewcache`.

If you run grocy on Linux, there is also `update.sh` (remember to make the script executable, `chmod +x update.sh` and ensure that you have `unzip` installed) which does exactly this and additionally creates a backup (`.tgz` archive) of the current installation in `data/backups` (backups older than 60 days will be deleted during the update).

## Localization
grocy is fully localizable - the default language is English (integrated into code), a German localization is always maintained by me. There is one file per language in the `localization` directory, if you want to create a translation, it's best to copy `localization/de.php` to a new one (e. g. `localization/it.php`) and translating all strings there. (Language can be changed in `data/config.php`, e. g. `Setting('CULTURE', 'it');`)

### Maintaining your own localization
As the German translation will always be the most complete one, for maintaining your localization it would be easiest when you compare your localization with the German one with a diff tool of your choice.

## Things worth to know

### REST API & data model documentation
See the integrated Swagger UI instance on [/api](https://demo-en.grocy.info/api).

### Barcode readers
Some fields also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter which is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

### Input shorthands for date fields
For (productivity) reasons all date (and time) input fields use the ISO-8601 format regardless of localization.
The following shorthands are available:
- `MMDD` gets expanded to the given day on the current year, if > today, or to the given day next year, if < today, in proper notation
  - Example: `0517` will be converted to `2018-05-17`
- `YYYYMMDD` gets expanded to the proper ISO-8601 notation
  - Example: `20190417` will be converted to `2019-04-17`
- `YYYYMMe` or `YYYYMM+` gets expanded to the end of the given month in the given year in proper notation
  - Example: `201807e` will be converted to `2018-07-31`
- `x` gets expanded to `2999-12-31` (which I use for products which never expire)
- Down/up arrow keys will increase/decrease the date by one day
- Right/left arrow keys will increase/decrease the date by 1 week

### Keyboard shorthands for buttons
Wherever a button contains a bold highlighted letter, this is a shortcut key.
Example: Button "Add as new **p**roduct" can be "pressed" by using the `P` key on your keyboard.

### Barcode lookup via external services
Products can be directly added to the database via looking them up against external services by a barcode.
This is currently only possible through the REST API.
There is no plugin included for any service, see the reference implementation in `data/plugins/DemoBarcodeLookupPlugin.php`.

### Database migrations
Database schema migration is automatically done when visiting the root (`/`) route (click on the logo in the left upper edge).

### Adding your own CSS or JS without to have to modify the application itself
- When the file `data/custom_js.html` exists, the contents of the file will be added just before `</body>` (end of body) on every page
- When the file `data/custom_css.html` exists, the contents of the file will be added just before `</head>` (end of head) on every page

### Demo mode
When the file `data/demo.txt` exists, the application will work in a demo mode which means authentication is disabled and some demo data will be generated during the database schema migration.

### Embedded mode
When the file `embedded.txt` exists, it must contain a valid and writable path which will be used as the data directory instead of `data` and authentication will be disabled (used in [grocy-desktop](https://github.com/berrnd/grocy-desktop)).

In embedded mode, settings can be overridden by text files in `data/settingoverrides`, the file name must be `<SettingName>.txt` (e. g. `BASE_URL.txt`) and the content must be the setting value (normally one single line).

## Screenshots
#### Dashboard
![Dashboard](https://github.com/berrnd/grocy/raw/master/publication_assets/dashboard.png "Dashboard")

#### Purchase - with barcode scan
![Purchase - with barcode scan](https://github.com/berrnd/grocy/raw/master/publication_assets/purchase-with-barcode.gif "purchase-with-barcode")

#### Consume - with manual search
![Consume - with manual search](https://github.com/berrnd/grocy/raw/master/publication_assets/consume.gif "consume")

## License
The MIT License (MIT)
