> ⚠️⚠️ SQLite >= 3.9.0 (was released in late 2015) is required
>
> [Here](https://github.com/grocy/grocy/issues/1209#issuecomment-749760765) is a workaround if you still run a SQLite version >= 3.8.3 < 3.9.0
>
> _PHP 7.2 with SQLite 3.8.3 was the currently in [README mentioned](https://github.com/grocy/grocy#how-to-install) minimum runtime requirement, any future release will only be tested against a reasonable recent runtime (currently PHP 7.4 with SQLite 3.27.2) - supporting those (very) old runtime stuff is too time consuming..._

- Improved the prerequisites checker (added missing required PHP extension `ctype`) (thanks @Forceu)
- Added validation checks for most `data/config.php` settings to prevent using invalid ones (thanks @Forceu)
- Fixed that browser camera barcode scanning did not work on the product edit page
- Fixed that the new product option "Never show on stock overview" was unintentionally set by default for new products
- Fixed that the success message on purchase displayed not amount when `FEATURE_FLAG_STOCK_PRICE_TRACKING` was disabled
