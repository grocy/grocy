> ⚠️ The following PHP extensions are now additionally required: `json`, `intl`, `zlib`

> ⚠️ PHP 8 is now supported and from now on the only tested runtime version (but as of now, PHP 7.2 should still work).

### New feature: (Own) Product and stock entry labels/barcodes ("grocycode")
- Print own labels/barcodes for products and/or every stock entry and then scan that code on every place a product or stock entry can be selected
- Can be printed (or downloaded) via
  - The product edit page
  - The context/more menu per line on the stock overview and stock entries page
  - Automatically on purchase (new option on the purchase page, defaults can be configured per product)
- The used barcode type can be configured via the `config.php` option `GROCYCODE_TYPE`:
  - `1D` (default) will produce a `Code128` 1D barcode (supported by the integrated camera barcode scanner)
  - `2D` will produce a `DataMatrix` 2D barcode (currently not supported by the integrated camera barcode scanner, but can be probably printed smaller)
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
- On the stock overview page it's now also possible to search/filter by product barcodes (via the general search field)
- The product picker on the consume and transfer page now only shows products which are currently in stock
- Added a filter option to only show in-stock products on the products list page (master data)
- Added new columns on the stock overview page (hidden by default): Product description, product default location, parent product
- Fixed that editing stock entries was not possible
- Fixed that consuming with Scan Mode was not possible
- Fixed that the current stock total value (header of the stock overview page) didn't include decimal amounts (thanks @Ape)
- Fixed that the transfer page was not fully populated when opening it from the stock entries page
- Fixed that undoing a consume/open action from the success notification on the stock entries page was not possible
- Fixed that adding a barcode to a product didn't save the selected quantity unit when the product only has a single one
- Fixed that the store information on a stock entry was lost when transferring a partial amount to a different location
- Fixed that the "Spoil rate" on the product card was wrong in some cases
- Fixed that the stock journal showed always the products default location (instead of the location of the transaction)
 - Fixed that the aggregated amount of parent products was wrong when indirect quantity unit conversions were used

### Shopping list improvements/fixes
- The amount now defaults to `1` for adding items quicker
- Added a status filter for only _done_ items
- Fixed that shopping list prints had a grey background (thanks @Forceu)
- Fixed the form validation on the shopping list item page (thanks @Forceu)
- Fixed that when adding products to the shopping list from the stock overview page, the used quantity unit was always the products default purchase QU (and not the selected one)
- Fixed that the displayed last unit/total price was wrong when the used quantity unit was not the products stock QU
- Fixed that the "Add as barcode to existing product" productpicker workflow did not work

### Recipe improvements/fixes
- Recipe printing improvements (thanks @Ape)
- Calories are now always displayed per single serving (on the recipe and meal plan page)
- The note of an ingredient will now also be added to the corresponding shopping list item when using "Put missing products on the shopping list"
- Fixed that the recipe page was slow when there were a lot meal plan recipe entries
- Fixed that "Only check if any amount is in stock" (recipe ingredient option) didn't work for stock amounts < 1
- Fixed that when adding missing items to the shopping list, on the popup deselected items got also added
- Fixed that the amount of self produced products with tare weight handling enabled was wrong ("Produces product" recipe option)
- Fixed that the ingredient amount calculation for included/nested recipes was (for most cases) wrong
- Fixed that the ingredient amount was wrong when using a to the product indirectly related quantity unit

### Meal plan improvements/fixes
- Improved the meal plan page loading time (drastically when having a big history of meal plan entries)
- Meal plan entries can now be visually marked as "done" (new button per entry)
  - This happens automatically on consuming a recipe/product from the meal plan page
- Fixed that stock fulfillment checking used the desired servings of the recipe (those selected on the recipes page, not them from the meal plan entry)

### Chores improvements/fixes
- It's now possible to track any addtional info on a chore execution by using Userfields
  - => Configure the desired Userfields for the entity `chores_log`
  - => The on chore execution tracking entered information is then visible on the corresponding chore journal entry
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
  - => See the new `LDAP*` `config.php` options
- Some night mode style improvements (thanks @BlizzWave and @KTibow)
- Help tooltips are now additionally also triggered by clicking on them (instead of only hovering them, which doesn't work on mobile / touch devices)
- The camera barcode scanner now also supports Code 39 barcodes (used for example in Germany on pharma products (PZN)) (thanks @andreheuer)
- Fixed that the number picker up/down buttons did not work when the input field was empty or contained an invalid number
- Fixed that links and embeds (e.g. YouTube videos) did not work in the text editor
- Fixed that the "Manage users" and "Manage API keys" menu was not shown when using reverse proxy authentication

### API improvements/fixes
> ❗ Numbers are now returned as numbers (so technically without quotes around them, were strings for nearly all endpoints before - should practically be no real difference)
- Added a new API endpoint `/system/localization-strings` to get the localization strings (gettext JSON representation; in the by the user desired language)
- The `GET /chores` endpoint now also returns the `next_execution_assigned_user` per chore (like the endpoint `GET /chores​/{choreId}` already did for a single chore)
- The `GET /tasks` endpoint now also returns the assigned user and category object per task
- Empty Userfields are now also returned (were previously omitted, endpoint `GET /objects/{entity}` and `GET /objects/{entity}/{objectId}`)
- Fixed that due soon products with `due_type` = "Expiration date" were missing in `due_products` of the `/stock/volatile` endpoint
- Fixed that `PUT/DELETE /objects/{entity}/{objectId}` produced an internal server error when the given object id was invalid (now returns `400 Bad Request`)
- Fixed that hyphens in filter values did not work
- Fixed that cyrillic letters were not allowed in filter values
