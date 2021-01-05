> ⚠️⚠️ SQLite >= 3.9.0 (was released in late 2015) is required
>
> [Here](https://github.com/grocy/grocy/issues/1209#issuecomment-749760765) is a workaround if you still run a SQLite version >= 3.8.3 < 3.9.0
>
> _PHP 7.2 with SQLite 3.8.3 was the formerly in [README mentioned](https://github.com/grocy/grocy#how-to-install) minimum runtime requirement, any future release will only be tested against a reasonable recent runtime (currently PHP 7.4 with SQLite 3.27.2) - supporting those (very) old runtime stuff is too time consuming..._

- Improved the prerequisites checker (added missing required PHP extension `ctype`) (thanks @Forceu)
- Added validation checks for most `data/config.php` settings to prevent using invalid ones (thanks @Forceu)
- When using reverse proxy authentication (`ReverseProxyAuthMiddleware`), _additionally_ a valid API key can now also be used for authentication (if you don't want to protect the API endpoints via your reverse proxy, however)
- Added a new API endpoint `/system/time` to get the current server time (thanks @Forceu)
- An amount attached to a barcode is now also prefiled when scanning the product on the Consume/Transfer/Inventory page
- Fixed that some number inputs were broken when the new decimal places setting were set to `0`
- Fixed that browser camera barcode scanning did not work on the product edit page for adding product barcodes
- Fixed that indirect unit conversions (those between units, not product overrides) could not be used/selected
- Fixed that the new product option "Never show on stock overview" was unintentionally set by default for new products
- Fixed that adding items to the shopping list from the context/more menu on the stock overview page did not work
- Fixed that consuming was not possible when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was disabled
- Fixed that adding images in text editor fields did not work
- Fixed some other minor UI glitches
