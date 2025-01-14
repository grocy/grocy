> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Added a new product picker workflow "External barcode lookup"
  - This executes the configured barcode lookup plugin with the given barcode
  - If the lookup was successful, the product edit page of the created product is displayed, where the product setup can be completed (if required)
  - After that, the transaction is continued with that product as usual
  - A plugin for [Open Food Facts](https://world.openfoodfacts.org/) is now included and used by default (see the `config.php` option `STOCK_BARCODE_LOOKUP_PLUGIN` and maybe change it as needed)
    - The product name and image (and of course the barcode itself) are taken over from Open Food Facts to the product being looked up
  - => Quick video demo (using a Barcode Laser Scanner): https://www.youtube.com/watch?v=-moXPA-VvGc
  - => Quick video demo (using Browser Camera Barcode Scanning): https://www.youtube.com/watch?v=veezFX4X1JU
- Optimized that when moving a product to a freezer location (so when freezing it) the due date will no longer be replaced when the product option "Default due days after freezing" is set to `0`
- Product barcode matching is now case-insensitive
- Added a new column "Product picture" on the products list (master data) page (hidden by default)
- Optimized that when navigation between the different "Group by"-variants on the stock report "Spendings", the selected date range now remains persistent
- Fixed that a once set quantity unit on a product barcode could not be removed on edit
- Fixed that when consuming a specific stock entry which is opened, and which originated from a before partly opened stock entry, the unopened one was wrongly consume instead

### Shopping list

- xxx

### Recipes

- Optimized that when adding missing recipe ingredients with the option "Only check if any amount is in stock" enabled to the shopping list and when no corresponding unit conversion exists, the amount/unit is now taken "as is" (as defined in the recipe ingredient) into the created shopping list item
- Added a trendline to the price history chart (product card)
- Fixed that calories/costs of recipe ingredients were wrong when the ingredient option "Only check if any amount is in stock" was set and the on the ingredient used quantity unit was different from the product's QU stock
- Fixed that multi-nested recipes (at least 3 levels of "included recipes") resulted in wrong amounts/costs/calories calculated for the ingredients orginating in those nested recipes (also affected the meal plan)

### Meal plan

- Fixed that amounts/costs/calories were wrong for recipes which had at least 2 levels of "included recipes"

### Chores

- Added the possibility to see if a scheduled schedule chore was tracked/done on time or not:
  - When tracking chores, the "Next estimated tracking date" (so the current scheduled time) is now also stored in the corresponding chore journal entry and displayed in new column "Scheduled tracking time" on the chores journal page
  - When the "Tracked time" is later than the "Scheduled tracking time", the corresponding chore journal entry is now highlighted in red on the chores journal page
- Added a new column "Time of tracking" on the chores journal page (displays the time when the tracking actually happened, hidden by default)
- Added a new option "Swap track next schedule / track now buttons" (chores settings / top right corner settings menu) to swap the "Track next chore schedule" / "Track chore execution now" buttons/menu items on the chores overview page (defaults to disabled, so no changed behavior when not configured)

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- Optimized Userfields of type "Checkbox"
  - When it's a mandatory Userfield, the initial state of the corresponding checkbox is now indeterminate, means it's now also possible to actively not check it (previously mandatory meant the checkbox has to be set)
- Fixed that Userfield default values were not initialized for the `stock` entity (so affecting the purchase and inventory page)
- Fixed that uploading bigger or multiple files (so when the upload usually takes a little longer) didn't work (Userfield type "File")

### General

- Optimized nested modal dialogs:
  - Nested dialogs are now no longer displayed "in each other" and instead "on top of each other"
  - Dialogs can now be closed with the `ESC` key on the keyboard
  - There is no longer a close button at the bottom (outside of the displayed `iframe`) and instead one at the top right corner of the dialog
  - Wide dialogs (e.g. all showing a table, like showing stock entries of a product from the stock overview more/context menu per line) now use the full screen width
- Improved the handling of the initial field focus on form pages
- The previously manually necessary update steps (e.g. emptying the `data/viewcache` directory) are now fully automated, so no need to do this manually after this and future updates

### API

- xxx
