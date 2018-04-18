# grocy
ERP beyond your fridge

## Give it a try
Public demo of the latest version &rarr; [https://demo.grocy.info](https://demo.grocy.info) 

## Motivation
A household needs to be managed. I did this so far (almost 10 years) with my first self written software (a C# windows forms application) and with a bunch of Excel sheets. The software is a pain to use and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete houshold management"-thing.

## What it is about
For now my main focus is on stock management, ERP your fridge!

## How to install
Just unpack the [latest release](https://github.com/berrnd/grocy/releases/latest) on your PHP (7.0 or later required) enabled webserver (webservers root should point to the `/public` directory), copy `config-dist.php` to `data/config.php`, edit it to your needs, ensure that the `data` directory is writable and you're ready to go.

Default login is user `admin` with password `admin` - see the `data/config.php` file. Alternatively clone this repository and install Composer and Bower dependencies manually.

If you use nginx as your webserver, please include `try_files $uri /index.php;` in your location block.

## How to update
Just overwrite everything with the latest release while keeping the `/data` directory, check `config-dist.php` for new configuration options and add them to your `data/config.php` (it will show up as an error if something is missing there).

## Things worth to know

### Barcode readers
Some fields also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter which is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

### Input shorthands for date fields
For (productivity) reasons all date (and time) input fields use the ISO-8601 format regardless of localization.
The following shorthands are available:
- `MMDD` gets expanded to the given day on the current year in proper notation
  - Example: `0517` will be converted to `2018-05-17`
- `YYYYMMDD` gets expanded to the proper ISO-8601 notation
  - Example: `20190417` will be converted to `2019-04-17`
- `x` gets expanded to `2099-12-31` (which I use for products which never expire)
- Down/up arrow keys will increase/decrease the date by one day
- Right/left arrow keys will increase/decrease the date by 1 week

### Keyboard shorthands for buttons
Wherever a button contains a bold highlighted letter, this is a shortcut key.
Example: Button "Add as new **p**roduct" can be "pressed" by using the `P` key on your keyboard.

### Database migrations
Database schema migration is automatically done when visiting the root (`/`) route (click on the logo in the left upper edge).

### Demo mode
When the file `data/demo.txt` exists, the application will work in a demo mode which means authentication is disabled and some demo data will be generated during the database schema migration.

## Screenshots
#### Dashboard
![Dashboard](https://github.com/berrnd/grocy/raw/master/publication_assets/dashboard.png "Dashboard")

#### Purchase - with barcode scan
![Purchase - with barcode scan](https://github.com/berrnd/grocy/raw/master/publication_assets/purchase-with-barcode.gif "purchase-with-barcode")

#### Consume - with manual search
![Consume - with manual search](https://github.com/berrnd/grocy/raw/master/publication_assets/consume.gif "consume")

## License
The MIT License (MIT)
