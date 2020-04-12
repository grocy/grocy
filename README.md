# grocy
ERP beyond your fridge

## Give it a try
- Public demo of the latest stable version (`release` branch) &rarr; [https://demo.grocy.info](https://demo.grocy.info)
- Public demo of the latest pre-release version (`master` branch) &rarr; [https://demo-prerelease.grocy.info](https://demo-prerelease.grocy.info)

## Questions / Help / Bug reporting / Feature requests
There is the [r/grocy subreddit](https://www.reddit.com/r/grocy) to connect with other grocy users and getting help.

If you've found something that does not work or if you have an idea for an improvement or new things which you would find useful, feel free to open an issue in the [issue tracker](https://github.com/grocy/grocy/issues) here.

## Community contributions
See the website for a list of community contributed Add-ons / Tools: [https://grocy.info/#addons](https://grocy.info/#addons)

## Motivation
A household needs to be managed. I did this so far (almost 10 years) with my first self written software (a C# windows forms application) and with a bunch of Excel sheets. The software is a pain to use and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete household management"-thing. ERP your fridge!

## How to install
> Checkout grocy-desktop, if you want to run grocy without a webserver just like a normal (windows) desktop application.
>
> See https://github.com/grocy/grocy-desktop or directly download the [latest release](https://releases.grocy.info/latest-desktop) - the installation is nothing more than just clicking 2 times "next"...

Just unpack the [latest release](https://releases.grocy.info/latest) on your PHP (SQLite (3.8.3 or higher) extension required, currently only tested with PHP 7.4) enabled webserver (webservers root should point to the `public` directory), copy `config-dist.php` to `data/config.php`, edit it to your needs, ensure that the `data` directory is writable and you're ready to go, (to make it writable, maybe use `chown -R www-data:www-data data/`). Default login is user `admin` with password `admin`, please change the password immediately (see user menu).

Alternatively clone this repository (the `release` branch always references the latest released version, or checkout the latest tagged revision) and install Composer and Yarn dependencies manually.

If you use nginx as your webserver, please include `try_files $uri /index.php$is_args$query_string;` in your location block.

If, however, your webserver does not support URL rewriting, set `DISABLE_URL_REWRITING` in `data/config.php` (`Setting('DISABLE_URL_REWRITING', true);`).

See the website for further installation guides and troubleshooting help: https://grocy.info/links

## How to run using Docker

See [grocy/grocy-docker](https://github.com/grocy/grocy-docker) or [linuxserver/docker-grocy](https://github.com/linuxserver/docker-grocy) for instructions.

## How to update
Just overwrite everything with the latest release while keeping the `data` directory, check `config-dist.php` for new configuration options and add them to your `data/config.php` where appropriate (the default values from `config-dist.php` will be used for not in `data/config.php` defined settings). Just to be sure, please empty `data/viewcache`.

If you run grocy on Linux, there is also `update.sh` (remember to make the script executable (`chmod +x update.sh`) and ensure that you have `unzip` installed) which does exactly this and additionally creates a backup (`.tgz` archive) of the current installation in `data/backups` (backups older than 60 days will be deleted during the update).

## Localization
grocy is fully localizable - the default language is English (integrated into code), a German localization is always maintained by me.
You can easily help translating grocy at https://www.transifex.com/grocy/grocy, if your language is incomplete or not available yet.
(Language can be changed in `data/config.php`, e. g. `Setting('CULTURE', 'it');`)

The [pre-release demo](https://demo-prerelease.grocy.info) is available for any translation which is at least 80 % complete and will pull the translations from Transifex 10 minutes past every hour, so you can have a kind of instant preview of your contributed translations. Thank you!

Also any translation which reached a completion level of 80 % will be included in releases.

## Things worth to know

### REST API & data model documentation
See the integrated Swagger UI instance on [/api](https://demo.grocy.info/api).

### Barcode readers & camera scanning
Some fields (with a barcode icon above) also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter which is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

Additionally it's also possible to use your device camera to scan a barcode by using the camera button on the right side of the corresponding field (powered by [QuaggaJS](https://github.com/serratus/quaggaJS), totally offline / client-side camera stream processing, please note due to browser security restrictions, this only works when serving grocy via a secure connection (`https://`)). Quick video demo: https://www.youtube.com/watch?v=Y5YH6IJFnfc

My personal recommendation: Use a USB barcode laser scanner. They are cheap and work 1000 % better, faster, under any lighting condition and from any angle.

### Input shorthands for date fields
For (productivity) reasons all date (and time) input (and display) fields use the ISO-8601 format regardless of localization.
The following shorthands are available:
- `MMDD` gets expanded to the given day on the current year, if > today, or to the given day next year, if < today, in proper notation
  - Example: `0517` will be converted to `2018-05-17`
- `YYYYMMDD` gets expanded to the proper ISO-8601 notation
  - Example: `20190417` will be converted to `2019-04-17`
- `YYYYMMe` or `YYYYMM+` gets expanded to the end of the given month in the given year in proper notation
  - Example: `201807e` will be converted to `2018-07-31`
- `x` gets expanded to `2999-12-31` (which I use for products which never expire)
- Down/up arrow keys will increase/decrease the date by 1 day
- Right/left arrow keys will increase/decrease the date by 1 week
- Shift + down/up arrow keys will increase/decrease the date by 1 month
- Shift + right/left arrow keys will increase/decrease the date by 1 year

### Keyboard shorthands for buttons
Wherever a button contains a bold highlighted letter, this is a shortcut key.
Example: Button "Add as new **p**roduct" can be "pressed" by using the `P` key on your keyboard.

### Barcode lookup via external services
Products can be directly added to the database via looking them up against external services by a barcode.
This is currently only possible through the REST API.
There is no plugin included for any service, see the reference implementation in `data/plugins/DemoBarcodeLookupPlugin.php`.

### Database migrations
Database schema migration is automatically done when visiting the root (`/`) route (click on the logo in the left upper edge).

### Disable certain features
If you don't use certain feature sets of grocy (for example if you don't need "Chores"), there are feature flags per major feature set to hide/disable the related UI elements (see `config-dist.php`)

### Adding your own CSS or JS without to have to modify the application itself
- When the file `data/custom_js.html` exists, the contents of the file will be added just before `</body>` (end of body) on every page
- When the file `data/custom_css.html` exists, the contents of the file will be added just before `</head>` (end of head) on every page

### Demo mode
When the `MODE` setting is set to `dev`, `demo` or `prerelease`, the application will work in a demo mode which means authentication is disabled and some demo data will be generated during the database schema migration.

### Embedded mode
When the file `embedded.txt` exists, it must contain a valid and writable path which will be used as the data directory instead of `data` and authentication will be disabled (used in [grocy-desktop](https://github.com/grocy/grocy-desktop)).

In embedded mode, settings can be overridden by text files in `data/settingoverrides`, the file name must be `<SettingName>.txt` (e. g. `BASE_URL.txt`) and the content must be the setting value (normally one single line).

## Contributing / Say thanks
Any help is more than appreciated. Feel free to pick any open unassigned issue and submit a pull request, but please leave a short comment or assign the issue yourself, to avoid working on the same thing.

See https://grocy.info/#say-thanks for more ideas if you just want to say thanks.

## Roadmap
There is none. grocy is only my hobby, one I like, but not the only one, and because of that, there are no release dates, no schedules for when anything is ready, it's done when it's done, maybe tomorrow, maybe tomorrow next year, everyone is invited to contribute - I appreciate all ideas and contributions. The progress of a specific bug/enhancement is always tracked in the corresponding issue, at least by commit comment references.

## Screenshots
#### Dashboard
![Dashboard](https://github.com/grocy/grocy/raw/master/publication_assets/dashboard.png "Dashboard")

#### Purchase - with barcode scan
![Purchase - with barcode scan](https://github.com/grocy/grocy/raw/master/publication_assets/purchase-with-barcode.gif "purchase-with-barcode")

#### Consume - with manual search
![Consume - with manual search](https://github.com/grocy/grocy/raw/master/publication_assets/consume.gif "consume")

## License
The MIT License (MIT)
