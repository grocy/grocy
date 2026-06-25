> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- The product picker now searches product names accent insensitive
- Fixed that the product picker workflow dialog was not displayed when the entered value contained double quotes
- Fixed that changing the location on the purchase page re-initialized the due date based on product defaults (if any)
- Fixed that when undoing a product consume or transfer transaction, the store of the corresponding stock entry wasn't restored
  - This will only apply to new consume / transfer transactions, not when undoing transactions made before using this release
- Fixed that the status filter on the master data products page always displayed "All" after selection (only affected Chrome/Edge)

### Shopping list

- Fixed that the shopping list setting (top right corner settings menu) "Round up quantity amounts to the nearest whole number" wasn't applied to shopping list item amounts where a quantity unit conversion was involved
- Fixed that printing the shopping list with "Group by product group" enabled created duplicated product group headlines in some cases

### Recipes

- Fixed that the ingredient list showed fixed "Calories" instead of the configured `ENERGY_UNIT`

### Meal plan

- Fixed that "add recipe"-dropdown wasn't sorted alphabetically

### Chores

- xxx

### Calendar

- xxx

### Tasks

- Added a table filter for "Assigned to"

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- Fixed that Userfields of type "Select list (a single item can be selected)" changed by keyboard only were not saved

### General

- Fixed accent insensitive searching using the general table search field was broken
- Fixed that it wasn't possible to log in using passwords containing special escape sequences (e.g. `<<`)
- Fixed that the initially created location and quantity units weren't localized (only applies to new installations)

### API

- Fixed that the endpoints `POST /stock/shoppinglist/add-product` and `POST /stock/shoppinglist/remove-product` truncated decimal product amounts
