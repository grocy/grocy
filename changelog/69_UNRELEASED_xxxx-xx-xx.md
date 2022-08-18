> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Fixed that stock entry notes were lost when consuming/opening/transferring a partial amount of the corresponding stock entry (thanks @akoshpinter)
- Fixed that the average shelf life of a product (on the productcard) was wrong when the corresponding stock entry was edited
- Fixed that when the stock setting "Decimal places allowed for amounts" was set to `0`, unit conversion (if any) failed when adding the corresponding product to stock
- Fixed that consuming a parent product which is not in stock itself (so essentially using any of the child products) may failed when unit conversions were involved (the current stock amount check was wrong in that case)
- Fixed that the status button counters on the stock overview page ("X products are overdue" and so on) included products which have the option `Never show on stock overview` enabled
- Fixed that adding Userfields to existing stock entries was not possible (only editing existing Userfield values, e.g. added during purchase or inventory, was possible)

### Shopping list

- Fixed that products could not be added to the shopping list via barcode scanning

### Recipes

- Fixed that headlines in the recipe description (preparation text) were removed on saving

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

- Fixed that edit forms were broken when editing an object with `null` Userfields (so when the field for that object was not set before / on the initial object creation)

### General

- New translations: (thanks all the translators)
  - Lithuanian (demo available at <https://lt.demo.grocy.info>)

### API

- Endpoint `/stock/volatile`
  - The field/property `missing_products` now also contains the `product` object
