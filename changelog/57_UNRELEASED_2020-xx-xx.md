### New feature: Price history per store
- Define stores under master data
- Track on purchase/inventory in which store you bought the product
- => The price history chart on the product card shows a line per store
- (Thanks @immae)

### Recipe fixes
- Fixed a PHP notice on the recipes page when there are no recipes (thanks @mrunkel)

### API improvements
- The endpoint `/stock/products/{productId}/locations` now also returns the current stock amount of the product in that loctation (new field/property `amount`) (thanks @Forceu)

### General & other improvements
- New `config.php` setting `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD` which activates the number pad for best-before-date fields on (supported) mobile browsers (useful because of [shorthands](https://github.com/grocy/grocy#input-shorthands-for-date-fields)) (defaults to `true`) (thanks @Mik-)
- Prerequisites (PHP extensions, critical files/folders) will now be checked and properly reported if there are problems (thanks @Forceu)
- Improved the the overview pages on mobile devices (main column was hidden) (thanks @Mik-)
- Optimized the handling of settings provided by `data/settingoverrides` files (thanks @dacto)
