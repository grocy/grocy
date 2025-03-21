-----

<div align="center">
<img alt="Logo" height="50" src="https://raw.githubusercontent.com/grocy/grocy/master/public/img/logo.svg?sanitize=true" />
<h2>ERP beyond your fridge</h2>
<h3>Grocy is a web-based self-hosted groceries & household management solution for your home</h3>
<em><h4>This is a hobby project by <a href="https://berrnd.de">Bernd Bestel</a></h4></em>
</div>

-----

## Give it a try

- Public demo of the latest stable version (`release` branch) &rarr; [https://demo.grocy.info](https://demo.grocy.info)
- Public demo of the current development version (`master` branch) &rarr; [https://demo-prerelease.grocy.info](https://demo-prerelease.grocy.info)

## Features

See the website. &rarr; <https://grocy.info>

## Questions / Help / Bug Reports / Feature Requests

- General help and usage questions &rarr;  [r/grocy subreddit](https://www.reddit.com/r/grocy)
- Bug Reports and Feature Requests &rarr; [Issue Tracker](https://github.com/grocy/grocy/issues/new/choose)

_Please don't send me private messages or call me regarding anything Grocy. I check the issue tracker and the subreddit pretty much daily, but don't provide any support beyond that._

## Community contributions

See the website for a list of community contributed Add-ons / Tools. &rarr; [https://grocy.info/addons](https://grocy.info/addons)

## How to install

> Checkout [Grocy Desktop](https://github.com/grocy/grocy-desktop), if you want to run Grocy without having to manage a webserver just like a normal (Windows) desktop application.
>
> Directly download the [latest release](https://releases.grocy.info/latest-desktop) - the installation is nothing more than just clicking 2 times "next".

Grocy is technically a pretty simple PHP application, so the basic notes to get it running are:
- Unpack the [latest release](https://releases.grocy.info/latest)
- Copy `config-dist.php` to `data/config.php` + edit to your needs
- Ensure that the `data` directory is writable
- The webserver root should point to the `public` directory
- Include `try_files $uri /index.php$is_args$query_string;` in your location block if you use nginx
  - Or disable URL rewriting (see the option `DISABLE_URL_REWRITING` in `data/config.php`)
- &rarr; Default login is user `admin` with password `admin`, please change the password immediately (user menu at the top right corner)

Alternatively clone this repository (the `release` branch always references the latest released version) and install Composer and Yarn dependencies manually.

See the website for more installation guides and troubleshooting help. &rarr; [https://grocy.info/links](https://grocy.info/links)

### Platform support

- PHP 8.2 or 8.3 (with SQLite 3.34.0+)
  - Required PHP extensions: `fileinfo`, `pdo_sqlite`, `gd`, `ctype`, `intl`, `zlib`, `mbstring`
  - _Recommendation: Benchmark tests showed that e.g. unit conversion handling is up to 5 times faster when using a more recent (3.39.4+) SQLite version._
- Recent Firefox, Chrome or Edge

## How to run using Docker

&rarr; https://hub.docker.com/r/linuxserver/grocy

## How to update

- Overwrite everything with the [latest release](https://releases.grocy.info/latest) while keeping the `data` directory
- Check `config-dist.php` for new configuration options and add them to your `data/config.php` where appropriate (the default values from `config-dist.php` will be used for not in `data/config.php` defined settings)

If you run Grocy on Linux, there is also `update.sh` (remember to make the script executable (`chmod +x update.sh`) and ensure that you have `unzip` installed) which does exactly this and additionally creates a backup (`.tgz` archive) of the current installation in `data/backups` (backups older than 60 days will be deleted during the update).

## Localization

Grocy is fully localizable - the default language is English (integrated into code), a German localization is always maintained by me.

You can easily help translating Grocy on [Transifex](https://explore.transifex.com/grocy/grocy/) if your language is incomplete or not available yet.

The default language can be set in `data/config.php`, e. g. `Setting('DEFAULT_LOCALE', 'de');` and there is also a user setting (see the user settings page) to set a different language per user.

The [pre-release demo](https://demo-prerelease.grocy.info) is available for any translation which is at least 70 % complete and will pull the translations from Transifex 10 minutes past every hour, so you can have a kind of instant preview of your contributed translations. Thank you!

Also any translation which once reached a completion level of 70 % ([`strings` resource](https://app.transifex.com/grocy/grocy/strings/)) will be included in releases.

_RTL languages are unfortunately not yet supported._

## Motivation

A household needs to be managed. Before Grocy I did this (for almost 10 years) using my first self written software (a C# Windows forms application) and with a bunch of Excel sheets. The software was a pain to use at the end and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete household management"-thing. ERP your fridge!

## Things worth to know

### REST API

See the integrated Swagger UI instance on [/api](https://demo.grocy.info/api).

The web frontend uses exactly this API for pretty much everything. So everything you can do there is also possible via the API.

### Barcode readers & camera scanning

Some fields (with a barcode icon above) also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter which is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

Additionally it's also possible to use your device camera to scan a barcode by using the camera button on the right side of the corresponding input field (powered by [ZXing](https://github.com/zxing-js/library), totally offline / client-side camera stream processing, please note due to browser security restrictions, this only works when serving Grocy via a secure connection (`https://`)). [Here](https://www.youtube.com/watch?v=veezFX4X1JU) and [there](https://www.youtube.com/watch?v=Y5YH6IJFnfc) are quick video demos of that.

_My personal recommendation: Use a USB barcode laser scanner. They are cheap and work 1000 % better, faster, under any lighting condition and from any angle._

### Barcode lookup via external services

Products can be directly added to the database via looking them up against external services by a barcode.

This can be done in-place using the product picker workflow "External barcode lookup" (the workflow dialog is displayed when entering something unknown in any product input field) Quick video demo: <https://www.youtube.com/watch?v=-moXPA-VvGc>

A plugin for [Open Food Facts](https://world.openfoodfacts.org/) is included and used by default (see the `data/config.php` option `STOCK_BARCODE_LOOKUP_PLUGIN`).

See that plugin or `plugins/DemoBarcodeLookupPlugin.php` for a commented example implementation if you want to build a plugin.

### Input shorthands for date fields

For (productivity) reasons all date (and time) input (and display) fields use the ISO-8601 format regardless of localization.
The following shorthands are available:
- `MMDD` gets expanded to the given day on the current year, if > today, or to the given day next year, if < today, in proper notation
  - Example: `0517` will be converted to `2025-05-17`
- `YYYYMMDD` gets expanded to the proper ISO-8601 notation
  - Example: `20250417` will be converted to `2025-04-17`
- `YYYYMMe` or `YYYYMM+` gets expanded to the end of the given month in the given year in proper notation
  - Example: `202507e` will be converted to `2025-07-31`
- `[+/-]n[d/m/y]` gets expanded to a date relative to today, while adding (**+**) or subtracting (**-**) the **n**umber of **d**ays/**m**onths/**y**ears, in proper notation
  - Example: `+1m` will be converted to the same day next month
- `x` gets expanded to `2999-12-31` (which is an alias for "never overdue")
- Down/up arrow keys will increase/decrease the date by 1 day
- Right/left arrow keys will increase/decrease the date by 1 week
- Shift + down/up arrow keys will increase/decrease the date by 1 month
- Shift + right/left arrow keys will increase/decrease the date by 1 year

### Keyboard shorthands for buttons

Wherever a button contains a bold highlighted letter, this is a shortcut key.
Example: Button "**P** Add as new product" can be "pressed" by using the `P` key on your keyboard.

### Installable web app (PWA)

Grocy's web frontend is responsive and an "installable web app" ([PWA](https://en.wikipedia.org/wiki/Progressive_web_app), without providing any offline usage capabilities), that provides a pretty native mobile app-like experience without the need for additional tools.

- Quick video demo on Android/Firefox: <https://www.youtube.com/watch?v=L38drVZfwHs>
- Quick video demo on Android/Chrome: <https://www.youtube.com/watch?v=rjLdXUFDNuk>

### Database migrations

Database schema migration is done when visiting the root (`/`) route (click on the logo in the left upper edge) as needed and is also triggered automatically if the version has changed (so when an update has been made).

_Please note: Database migrations are supposed to work between releases, not between every commit. If you want to run the current `master` branch (which is the development version), you need to handle that (and more) yourself._

### Disable certain features

If you don't use certain feature sets of Grocy (for example if you don't need "Chores"), there are feature flags per major feature set to hide/disable the related UI elements (see `config-dist.php`).

### Adding your own CSS or JS without to have to modify the application itself

- When the file `data/custom_js.html` exists, the contents of the file will be added just before `</body>` (end of body) on every page
- When the file `data/custom_css.html` exists, the contents of the file will be added just before `</head>` (end of head) on every page

### Demo mode

When the `MODE` setting is set to `dev`, `demo` or `prerelease`, the application will work in a demo mode which means authentication is disabled and some demo data will be generated during the database schema migration (pass the query parameter `nodemodata`, e.g. `https://grocy.example.com/?nodemodata` to skip that).

### Embedded mode

When the file `embedded.txt` exists, it must contain a valid and writable path which will be used as the data directory instead of `data` and authentication will be disabled (used in [Grocy Desktop](https://github.com/grocy/grocy-desktop)).

In embedded mode, settings can be overridden by text files in `data/settingoverrides`, the file name must be `<SettingName>.txt` (e. g. `BASE_URL.txt`) and the content must be the setting value (normally one single line).

## Contributing / Say Thanks

Any help is welcome, feel free to contribute anything which comes to your mind or see <https://grocy.info/#say-thanks> if you just want to say thanks.

## Roadmap

There is none. The progress of a specific bug/enhancement is always tracked in the corresponding issue, at least by commit comment references.

[Milestones](https://github.com/grocy/grocy/milestones) are used to indicate in which version the corresponding request was done (`vNEXT` means it's currently planned to do that for the next release).

## Screenshots

### Stock overview

![Stock overview](https://github.com/grocy/grocy/raw/master/.github/publication_assets/stock.png "Stock overview")

### Shopping List

![Shopping List](https://github.com/grocy/grocy/raw/master/.github/publication_assets/shoppinglist.png "Shopping List")

### Meal Plan

![Meal Plan](https://github.com/grocy/grocy/raw/master/.github/publication_assets/mealplan.png "Meal Plan")

### Chores overview

![Chores overview](https://github.com/grocy/grocy/raw/master/.github/publication_assets/chores.png "Chores overview")

## License

The MIT License (MIT)
