> ⚠️ PHP 8.1 (with SQLite 3.34.0+) is from now on the only supported runtime version.

> ❗ The major version bump is due to breaking API changes, please see below if you use the API.

> _Recommendation: Benchmark tests showed that e.g. unit conversion handling is up to 5 times faster when using a more recent (3.39.4+) SQLite version._

### New feature: xxxx

- xxx

### Stock

- Quantity unit conversions now support transitive conversions, means the QU hierarchy has now unlimited levels (thanks a lot @esclear)
- The product option "Factor purchase to stock quantity unit" was removed
  - => Use normal product specific QU conversions instead, if needed
  - An existing "Factor purchase to stock quantity unit" was automatically migrated to a product specific QU conversion
- New product option "Default quantity unit consume"
  - Will be used/selected as the default quantity unit on the consume page
  - The product's "Quick consume amount" is now displayed related to this quantity unit ("quick consume/open buttons" on the stock overview page)
  - Defaults to the product's "Quantity unit stock" (so no changed behavior when not configured)
- Changed that when the ingredient option "Only check if any amount is in stock" is enabled, costs and calories are now based on the original entered amount instead of an "virtual" fixed amount of `1`
- When using the "Add as barcode to existing product" workflow on a purchase transaction, the selected quantity unit and the entered amount is now also added to the new barcode
- When using the "Add as barcode to existing product" workflow on a purchase or inventory transaction, the entered note is now also added to the new barcode
- New product option "Auto reprint stock entry label"
  - When enabled, auto-changing the due date of a stock entry (by opening/freezing/thawing and having corresponding default due days set) will reprint its label (only server side label printer WebHooks are supported)
  - Defaults to disabled, so no changed behavior when not configured
- Added a new option "Reprint stock entry label" on the stock entry edit page (will print the correspondind stock entry label on save)
  - This option will be automatically set on changing the entry's due date
- The product option "Quick consume amount" (the amount used for the "quick consume/open buttons" on the stock overview page) has been split into another option "Quick open amount", to be able to set different amounts for consume and open (defaults to the "Quick consume amount" per product, so no changed behavior when not configured)
- Changed that for the product's average and last price (and for the price history chart) stock transactions with an empty or `0` price are ignored
- Fixed that hiding the "Purchased date" column (table options) on the stock entries page didn't work
- Fixed that sorting by the "Value" column on the stock overview page didn't work
- Fixed that the consumed amount was wrong, when consuming multiple substituted subproducts at once and when multiple/different conversion factors were involved
- Fixed that for a product's average price, only currently in-stock items were considered, not already consumed ones

### Shopping list

- Added a new button "Clear done items" (to clear all done items with one click)

### Recipes

- Added a new entry "Add to meal plan" in the context/more menu per recipe to directly add a recipe to the meal plan from the recipes page
- Changed that when a ingredient has a "Variable amount" set, the text entered there now also replaces the unit when displaying the recipe (not only the amount as before)
- When displaying a recipe in fullscreen mode, the ingredients and preparation is now shown side by side (or below each other on small screens) instead of in tabs
- When consuming a recipe which has a "Produces product" set and when the product's "Default stock entry label" is configured accordingly, the corresponding label will now be printed on that action (only server side label printer WebHooks are supported)
- Fixed that hiding the "Requirements fulfilled" column (table options) on the recipes page didn't work
- Fixed that ingredient costs and calories were wrong when product substitution and unit conversions were involved at the same time

### Meal plan

- Added a new sub feature flag `FEATURE_FLAG_RECIPES_MEALPLAN` (in `config.php`) to only disable the meal plan if not needed (thanks @webysther)
- Fixed that consuming a recipe from the meal plan didn't add its "Produces product"-product to stock (if any)

### Chores

- Changed the handling of the tracking buttons on the chores overview page:
  - The green button now tracks an execution of the corresponding chore on the next scheduled time, rather than for now/today
  - New context-/more menu option "Track chore execution now" to track an execution for now/today (so the same what the green button did before)
- Removed the limitation on the chore tracking page that the tracked time couldn't be in the future

### Calendar

- xxx

### Tasks

- Fixed that hiding the "Category" column (table options) on the tasks page didn't work

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- Product group Userfields are now also rendered on the shopping list
- Fixed that when having e.g. a Userfield for the `stock` entity and using the "Never overdue" shortcut checkbox for the due date on purchase, this Userfield would also be set to the corresponding "never overdue date"

### General

- Added a new `config.php` setting `ENERGY_UNIT` to customize the label to display energy values (was fixed `kcal` before and defaults to that, so no changed behavior when not configured)
- New translations: (thanks all the translators)
  - Romanian (demo available at <https://ro.demo.grocy.info>)

### API

- ⚠️ **Breaking changes**:
  - The product property `qu_factor_purchase_to_stock` was removed (existing factors were migrated to normal product specific QU conversions, see above)
  - Numbers are now returned as numbers (so technically without quotes around them, were strings for nearly all endpoints before)
- Endpoint `/stock/products/{productId}`:
  - Added a new field/property `qu_conversion_factor_purchase_to_stock` for convenience (contains the conversion factor of the corresponding QU conversion from the product's qu_id_purchase to qu_id_stock)
  - Added a new field/property `default_quantity_unit_consume` (contains the quantity unit object of the product's "Default quantity unit consume")
- The following entities are now also available via the endpoint `/objects/{entity}` (only listing, no edit)
  - `quantity_unit_conversions_resolved` (returns all final/resolved conversion factors per product and any directly or indirectly related quantity units)
- The endpoint `/batteries` now also returns the corresponding battery object (as field/property `battery`)
