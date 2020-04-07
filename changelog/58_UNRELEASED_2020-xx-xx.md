### New feature: Price history per store
- Define stores under master data
- New product option to set the default store
- Track on purchase/inventory in which store you bought the product (gets prefilled by the last store you purchased the product, or the default store of the product if you never bought it)
- => The price history chart on the product card shows a line per store
- (Thanks @immae and @kriddles)

### Stock improvements
- When creating a new product, the "QU id stock" is now preset by the "QU id purchase" (because most of the time that's most probably the same) (thanks @Mik-)

### Recipe fixes
- Fixed a PHP notice on the recipes page when there are no recipes (thanks @mrunkel)

### Calendar fixes
- Fixed that the "Share/Integrate calendar (iCal)" button did not work (thanks @tsia)

### API improvements
- The endpoint `/stock/products/{productId}/locations` now also returns the current stock amount of the product in that loctation (new field/property `amount`) (thanks @Forceu)

### General & other improvements
- New `config.php` setting `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_FIELD_NUMBER_PAD` which activates the number pad for best-before-date fields on (supported) mobile browsers (useful because of [shorthands](https://github.com/grocy/grocy#input-shorthands-for-date-fields)) (defaults to `true`) (thanks @Mik-)
- Enhancements for the camera barcode scanner (thanks @Mik-)
  - The light button only displayed when the device has a flash light
  - New `config.php` setting `FEATURE_FLAG_AUTO_TORCH_ON_WITH_CAMERA` to always enable the flash light automatically
  - Various display/CSS improvements
- Prerequisites (PHP extensions, critical files/folders) will now be checked and properly reported if there are problems (thanks @Forceu)
- Improved the the overview pages on mobile devices (main column was hidden) (thanks @Mik-)
- Optimized the handling of settings provided by `data/settingoverrides` files (thanks @dacto)
- Optimized the update script (`update.sh`) to create the backup tar archive before writing to it (was a problem on Btrfs file systems) (thanks @shane-kerr)
- New translations: (thanks all the translators)
  - Japanese (demo available at https://ja.demo.grocy.info)
  - Chinese (Taiwan) (demo available at https://zh-tw.demo.grocy.info)
