> ⚠️ PHP 8.1 (with SQLite 3.34.0+) is from now on the only supported runtime version.

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Quantity unit conversions now support transitive conversions, means the QU hierarchy has now unlimited levels (thanks a lot @esclear)
- Fixed that hiding the "Purchased date" column (table options) on the stock entries page didn't work

### Shopping list

- Added a new button "Clear done items" (to clear all done items with one click)

### Recipes

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

- The following entities are now also available via the endpoint `/objects/{entity}` (only listing, no edit)
  - `quantity_unit_conversions_resolved` (returns all final/resolved conversion factors per product and any directly or indirectly related quantity units)
- The endpoint `/batteries` now also returns the corresponding battery object (as field/property `battery`)
