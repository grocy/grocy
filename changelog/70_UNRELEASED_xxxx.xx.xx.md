> ⚠️ The minimum required SQLite version is now 3.34.0.

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Quantity unit conversions now support transitive conversions, means the QU hierarchy has now unlimited levels (thanks a lot @esclear)

### Shopping list

- Added a new button "Clear done items" (to clear all done items with one click)

### Recipes

- xxx

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- xxx

### General

- xxx

### API

- The following entities are now also available via the endpoint `/objects/{entity}` (only listing, no edit)
  - `quantity_unit_conversions_resolved` (returns all final/resolved conversion factors per product and any directly or indirectly related quantity units)
- The endpoint `/batteries` now also returns the corresponding battery object (as field/property `battery`)
