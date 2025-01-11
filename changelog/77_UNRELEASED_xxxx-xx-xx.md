> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Added a new product picker workflow "External barcode lookup (via plugin)"
  - This executes the configured barcode lookup plugin with the given barcode
  - If the lookup was successful, the product edit page of the created product is displayed, where the product setup can be completed (if required)
  - After that, the transaction is continued with that product as usual
  - A plugin for [Open Food Facts](https://world.openfoodfacts.org/) is now included and used by default (see the `config.php` option `STOCK_BARCODE_LOOKUP_PLUGIN` and maybe change it as needed)
    - The product name and image (and of course the barcode itself) are taken over from Open Food Facts to the product being looked up
- Optimized that when moving a product to a freezer location (so when freezing it) the due date will no longer be replaced when the product option "Default due days after freezing" is set to `0`

### Shopping list

- xxx

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

- Optimized nested modal dialogs:
  - Nested dialogs are now no longer displayed "in each other" and instead "on top of each other"
  - Dialogs can now be closed with the `ESC` key on the keyboard
  - There is no longer a close button at the bottom (outside of the displayed `iframe`) and instead one at the top right corner of the dialog
  - Wide dialogs (e.g. all showing a table, like showing stock entries of a product from the stock overview more/context menu per line) now use the full screen width
  - Improved handling of the initial field focus

### API

- xxx
