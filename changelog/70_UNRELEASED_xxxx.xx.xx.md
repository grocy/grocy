> ⚠️ PHP 8.1 (with SQLite 3.34.0+) is from now on the only supported runtime version.

> ❗ The major version bump is due to breaking API changes, please see below if you use the API.

### New feature: xxxx

- xxx

### Stock

- Quantity unit conversions now support transitive conversions, means the QU hierarchy has now unlimited levels (thanks a lot @esclear)
- The product option "Factor purchase to stock quantity unit" was removed
  - => Use normal product specific QU conversions instead, if needed
  - An existing "Factor purchase to stock quantity unit" was automatically migrated to a product specific QU conversion
- Fixed that hiding the "Purchased date" column (table options) on the stock entries page didn't work
- Fixed that the consumed amount was wrong, when consuming multiple substituted subproducts at once and when multiple/different conversion factors were involved

### Shopping list

- Added a new button "Clear done items" (to clear all done items with one click)

### Recipes

- Changed that when a ingredient has a "Variable amount" set, the text entered there now also replaces the unit when displaying the recipe (not only the amount as before)
- Fixed that hiding the "Requirements fulfilled" column (table options) on the recipes page didn't work
- Fixed that ingredient costs and calories were wrong when product substitution and unit conversions were involved at the same time

### Meal plan

- xxx

### Chores

- xxx

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

### General

- xxx

### API

- ⚠️ **Breaking changes**:
  - The product property `qu_factor_purchase_to_stock` was removed (existing factors were migrated to normal product specific QU conversions, see above)
  - The endpoint `/stock/products/{productId}` returns a new field/property `qu_conversion_factor_purchase_to_stock` for convenience (contains the conversion factor of the corresponding QU conversion from the product's qu_id_purchase to qu_id_stock)
- The following entities are now also available via the endpoint `/objects/{entity}` (only listing, no edit)
  - `quantity_unit_conversions_resolved` (returns all final/resolved conversion factors per product and any directly or indirectly related quantity units)
- The endpoint `/batteries` now also returns the corresponding battery object (as field/property `battery`)
