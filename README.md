<div align="center">
<img alt="Logo" height="50" src="https://raw.githubusercontent.com/grocy/grocy/master/public/img/grocy_logo.svg?sanitize=true"/>
<h3>ERP beyond your fridge</h3>
<h5> grocy is a web-based self-hosted groceries & household management solution for your home</h5>
</div>

-----

## Give it a try
- Public demo of the latest stable version (`release` branch) &rarr; [https://demo.grocy.info](https://demo.grocy.info)
- Public demo of the current development version (`master` branch) &rarr; [https://demo-prerelease.grocy.info](https://demo-prerelease.grocy.info)

## Questions / Help / Bug reporting / Feature requests
There is the [r/grocy subreddit](https://www.reddit.com/r/grocy) to connect with other grocy users and getting help.

If you've found something that does not work or if you have an idea for an improvement or new things which you would find useful, feel free to open a request on the [issue tracker](https://github.com/grocy/grocy/issues) here.

Please don't send me private messages regarding grocy help. I check the issue tracker and the subreddit pretty much daily, but don't provide grocy support beyond that.

## Community contributions
See the website for a list of community contributed Add-ons / Tools: [https://grocy.info/addons](https://grocy.info/addons)

## Motivation
A household needs to be managed. I did this so far (almost 10 years) with my first self written software (a C# Windows forms application) and with a bunch of Excel sheets. The software is a pain to use and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete household management"-thing. ERP your fridge!

## How to install
> Checkout [grocy-desktop](https://github.com/grocy/grocy-desktop), if you want to run grocy without having to manage a webserver just like a normal (Windows) desktop application.
>
> Directly download the [latest release](https://releases.grocy.info/latest-desktop) - the installation is nothing more than just clicking 2 times "next".

See [https://grocy.info/links](https://grocy.info/links) for some installation guides and troubleshooting help.

grocy is technically a pretty simple PHP application, so the basic notes to get it running are:
- Unpack the [latest release](https://releases.grocy.info/latest)
- Copy `config-dist.php` to `data/config.php` + edit to your needs
- Ensure that the `data` directory is writable
- The webserver root should point to the `public` directory
- Include `try_files $uri /index.php$is_args$query_string;` in your location block if you use nginx
  - Or disable URL rewriting (see the option `DISABLE_URL_REWRITING` in `data/config.php`)
- Based on user reports, the minmimum required/working runtime is PHP 7.2 with SQLite 3.9.0
  - However, I don't really care about supporting old runtime stuff, currently everything is only tested against (means 100 % works with) PHP 8.0 with SQLite 3.27.2
- &rarr; Default login is user `admin` with password `admin`, please change the password immediately (user menu at the top right corner)

Alternatively clone this repository (the `release` branch always references the latest released version, or checkout the latest tagged revision) and install Composer and Yarn dependencies manually.

## How to run using Docker

See [grocy/grocy-docker](https://github.com/grocy/grocy-docker) or [linuxserver/docker-grocy](https://github.com/linuxserver/docker-grocy) for instructions.

## How to update
Just overwrite everything with the latest release while keeping the `data` directory, check `config-dist.php` for new configuration options and add them to your `data/config.php` where appropriate (the default values from `config-dist.php` will be used for not in `data/config.php` defined settings). Just to be sure, please empty `data/viewcache`.

If you run grocy on Linux, there is also `update.sh` (remember to make the script executable (`chmod +x update.sh`) and ensure that you have `unzip` installed) which does exactly this and additionally creates a backup (`.tgz` archive) of the current installation in `data/backups` (backups older than 60 days will be deleted during the update).

## Localization
grocy is fully localizable - the default language is English (integrated into code), a German localization is always maintained by me.
You can easily help translating grocy at https://www.transifex.com/grocy/grocy, if your language is incomplete or not available yet.
(The default language can be set in `data/config.php`, e. g. `Setting('DEFAULT_LOCALE', 'it');` and there is also a user setting (see the user settings page) to set a different language per user).

The [pre-release demo](https://demo-prerelease.grocy.info) is available for any translation which is at least 70 % complete and will pull the translations from Transifex 10 minutes past every hour, so you can have a kind of instant preview of your contributed translations. Thank you!

Also any translation which once reached a completion level of 70 % will be included in releases.

_RTL languages are unfortunately not yet supported._

## Things worth to know

### REST API
See the integrated Swagger UI instance on [/api](https://demo.grocy.info/api).

### Barcode readers & camera scanning
Some fields (with a barcode icon above) also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter which is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

Additionally it's also possible to use your device camera to scan a barcode by using the camera button on the right side of the corresponding field (powered by [Quagga2](https://github.com/ericblade/quagga2), totally offline / client-side camera stream processing, please note due to browser security restrictions, this only works when serving grocy via a secure connection (`https://`)). Quick video demo: https://www.youtube.com/watch?v=Y5YH6IJFnfc

_My personal recommendation: Use a USB barcode laser scanner. They are cheap and work 1000 % better, faster, under any lighting condition and from any angle._

### Input shorthands for date fields
For (productivity) reasons all date (and time) input (and display) fields use the ISO-8601 format regardless of localization.
The following shorthands are available:
- `MMDD` gets expanded to the given day on the current year, if > today, or to the given day next year, if < today, in proper notation
  - Example: `0517` will be converted to `2018-05-17`
- `YYYYMMDD` gets expanded to the proper ISO-8601 notation
  - Example: `20190417` will be converted to `2019-04-17`
- `YYYYMMe` or `YYYYMM+` gets expanded to the end of the given month in the given year in proper notation
  - Example: `201807e` will be converted to `2018-07-31`
- `x` gets expanded to `2999-12-31` (which I use for products which are never overdue)
- Down/up arrow keys will increase/decrease the date by 1 day
- Right/left arrow keys will increase/decrease the date by 1 week
- Shift + down/up arrow keys will increase/decrease the date by 1 month
- Shift + right/left arrow keys will increase/decrease the date by 1 year

### Keyboard shorthands for buttons
Wherever a button contains a bold highlighted letter, this is a shortcut key.
Example: Button "**P** Add as new product" can be "pressed" by using the `P` key on your keyboard.

### Barcode lookup via external services
Products can be directly added to the database via looking them up against external services by a barcode.
This is currently only possible through the REST API.
There is no plugin included for any service, see the reference implementation in `data/plugins/DemoBarcodeLookupPlugin.php`.

### Database migrations
Database schema migration is automatically done when visiting the root (`/`) route (click on the logo in the left upper edge).

_Please note: Database migrations are supposed to work between releases, not between every commit. If you want to run the current `master` branch (which is the development version), however, you need to handle that (and maybe more) yourself._

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
There is none. The progress of a specific bug/enhancement is always tracked in the corresponding issue, at least by commit comment references.

## Screenshots
#### Stock overview
![Stock overview](https://github.com/grocy/grocy/raw/master/.github/publication_assets/stock.png "Stock overview")

#### Shopping List
![Shopping List](https://github.com/grocy/grocy/raw/master/.github/publication_assets/shoppinglist.png "Shopping List")

#### Meal Plan
![Meal Plan](https://github.com/grocy/grocy/raw/master/.github/publication_assets/mealplan.png "Meal Plan")

#### Chores overview
![Chores overview](https://github.com/grocy/grocy/raw/master/.github/publication_assets/chores.png "Chores overview")

## License
The MIT License (MIT)
