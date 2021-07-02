> ⚠️ The following PHP extensions are now additionally required: `json`, `intl`, `zlib`

### New feature: (Own) Product and stock entry labels/barcodes ("grocycode")
- Print own labels/barcodes for products and/or every stock entry and then scan that code on every place a product or stock entry can be selected
- Can be printed (or downloaded) via
  - The product edit page
  - The context/more menu per line on the stock overview and stock entries page
  - Automatically on purchase (new option on the purchase page, defaults can be configured per product)
- The used barcode type is `DataMatrix`
- Label printer functionality can be enabled via the new feature flag `FEATURE_FLAG_LABELPRINTER` (defaults to disabled)
- Label printer communication happens via WebHooks - see the new `LABEL_PRINTER*` `config.php` options
- Those grocycodes can also be used without a label printer - you can view or download the pictures and print them manually
- More information:
  - https://github.com/grocy/grocy/blob/master/docs/grocycode.md
  - https://github.com/grocy/grocy/blob/master/docs/label-printing.md
- (Thanks a lot @mistressofjellyfish)

### New feature: Shopping list thermal printer support
- The shopping list can now be printed on a thermal printer
  - The printer must compatible to the `ESC/POS` protocol and needs to be locally attached or network reachable to/by the machine hosting grocy (so the server)
  - See the new `TPRINTER*` `config.php` options to configure the printer connection and other options
  - => New button on the shopping list print dialog
- Can be enabled via the new feature flag `FEATURE_FLAG_THERMAL_PRINTER` (defaults to disabled)
- (Thanks a lot @Forceu)

### Stock improvements/fixes
- Product barcodes are now enforced to be unique across products
- Fixed that editing stock entries was not possible
- Fixed that consuming with Scan Mode was not possible
- Fixed that the current stock total value (header of the stock overview page) didn't include decimal amounts (thanks @Ape)
- Fixed that the transfer page was not fully populated when opening it from the stock entries page
- Fixed that undoing a consume/open action from the success notification on the stock entries page was not possible
- Fixed that adding a barcode to a product didn't save the selected quantity unit when the product only has a single one
- Fixed that the store information on a stock entry was lost when transferring a partial amount to a different location
- Fixed that the "Spoil rate" on the product card was wrong in some cases
- Fixed that the stock journal showed always the products default location (instead of the location of the transaction)

### Shopping list improvements/fixes
- The amount now defaults to `1` for adding items quicker
- Fixed that shopping list prints had a grey background (thanks @Forceu)
- Fixed the form validation on the shopping list item page (thanks @Forceu)
- Fixed that when adding products to the shopping list from the stock overview page, the used quantity unit was always the products default purchase QU (and not the selected one)

### Recipe improvements/fixes
- Recipe printing improvements (thanks @Ape)
- Calories are now always displayed per single serving (on the recipe and meal plan page)
- Fixed that "Only check if any amount is in stock" (recipe ingredient option) didn't work for stock amounts < 1
- Fixed that when adding missing items to the shopping list, on the popup deselected items got also added

### Chores fixes
- Fixed that tracking chores with "Done by" a different user was not possible

### Userfield fixes
- Fixed that numeric Userfields were initialised with `1.0`
- Fixed that shortcuts (up/down key) and the format did not work correctly when using multiple date/time Userfields per object
- Fixed that Userfields were not saved when adding a product or a recipe (only on editing)

### General & other improvements/fixes
- LDAP authentication improvements / OpenLDAP support (thanks @tank0226)
  - A read only service account can now be used for binding
  - The username attribute is now configurable
  - Filtering of accounts is now possible
  - => See the new `config.php` options
- Some night mode style improvements (thanks @BlizzWave and @KTibow)
- Help tooltips are now additionally also triggered by clicking on them (instead of only hovering them, which doesn't work on mobile / touch devices)
- The camera barcode scanner now also supports Code 39 barcodes (used for example in Germany on pharma products (PZN)) (thanks @andreheuer)
- Fixed that the number picker up/down buttons did not work when the input field was empty or contained an invalid number
- Fixed that links and embeds (e.g. YouTube videos) did not work in the text editor

### API improvements/fixes
- Added a new API endpoint `/system/localization-strings` to get the localization strings (gettext JSON representation; in the by the user desired language)
- Fixed that due soon products with `due_type` = "Expiration date" were missing in `due_products` of the `/stock/volatile` endpoint
- Fixed that `PUT/DELETE /objects/{entity}/{objectId}` produced an internal server error when the given object id was invalid (now returns `400 Bad Request`)
