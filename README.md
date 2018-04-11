# grocy
ERP beyond your fridge

## Motivation
A household needs to be managed. I did this so far (almost 10 years) with my first self written software (a C# windows forms application) and with a bunch of Excel sheets. The software is a pain to use and Excel is Excel. So I searched for and tried different things for a (very) long time, nothing 100 % fitted, so this is my aim for a "complete houshold management"-thing.

## What it is about
For now my main focus is on stock management, ERP your fridge!

# Give it a try
Public demo of the latest version &rarr; [https://demo.grocy.info](https://demo.grocy.info) 

## How to install
Just unpack the [latest release](https://github.com/berrnd/grocy/releases/latest) on your PHP (7.0 or later required) enabled webserver (root is the `/public` directory), copy `config-dist.php` to `data/config.php`, edit it to your needs, ensure that the `data` directory is writable and you're ready to go. Alternatively clone this repository and install Composer and Bower dependencies manually.

If you use nginx as your webserver, please include `try_files $uri /index.php;` in your location block.

## Notes about barcode readers
Some fields also allow to select a value by scanning a barcode. It works best when your barcode reader prefixes every barcode with a letter this is normally not part of a item name (I use a `$`) and sends a `TAB` after a scan.

## Screenshots
#### Dashboard
![Dashboard](https://github.com/berrnd/grocy/raw/master/publication_assets/dashboard.png "Dashboard")

#### Purchase - with barcode scan
![Purchase - with barcode scan](https://github.com/berrnd/grocy/raw/master/publication_assets/purchase-with-barcode.gif "purchase-with-barcode")

#### Consume - with manual search
![Consume - with manual search](https://github.com/berrnd/grocy/raw/master/publication_assets/consume.gif "consume")

## License
The MIT License (MIT)
