### New Feature: External Barcode Lookups as a product picker workflow + a built-in Plugin for Open Food Facts

- Added a new product picker workflow "External barcode lookup"
  - This executes the configured barcode lookup plugin with the given barcode
  - If the lookup was successful, the product edit page of the created product is displayed where the product setup can be completed (if required)
  - After that, the transaction is continued with that product as usual
  - A plugin for [Open Food Facts](https://world.openfoodfacts.org/) is now included and used by default (see the `config.php` option `STOCK_BARCODE_LOOKUP_PLUGIN` and maybe change it as needed)
    - The product name and image (and of course the barcode itself) are taken over from Open Food Facts to the product being looked up
  - => Quick video demo (using a Barcode Laser Scanner): https://www.youtube.com/watch?v=-moXPA-VvGc
  - => Quick video demo (using Browser Camera Barcode Scanning): https://www.youtube.com/watch?v=veezFX4X1JU

### Stock

- Added a new stock setting (top right corner settings menu) "Show all out of stock products" to optionally also show all out of stock products on the stock overview page (defaults to disabled, so no changed behavior when not configured)
  - By default the stock overview page lists all products which are currently in stock or below their min. stock amount
  - When this new setting is enabled, all (active) products are always shown
- Added a new product option "Can't be opened"
  - When enabled the product open functionality for that product is disabled
  - Defaults to disabled, so no changed behavior when not configured
- Added a new product option "Default purchase price type"
  - Can be used to set the default price type (Unit price / Total price) on the purchase page per product
  - Previously "Unit price" was the default on purchase and the selection was not saved and not reset when selecting another product, means the price type selection was retained when doing multiple purchase transactions in one go
  - The default for the new product option is "Unspecified" which keeps the previous behavior
- When products are automatically added to the shopping list (e.g. by the "below defined min. stock amount"-functionality or when adding missing recipe ingredients) the product's "Default quantity unit purchase" is now used (instead of the product's "Quantity unit stock")
- Product barcode matching is now case-insensitive
- Added a new column "Product picture" on the products list (master data) page (hidden by default)
- Optimized that when navigation between the different "Group by"-variants on the stock report "Spendings", the selected date range now remains persistent
- Added a new "Presets for new products" stock setting for the "Default stock entry label" option of new products
- Added a trendline to the price history chart (product card)
- Added a "Add to shopping list"-button on the product card
- Optimized that when moving a product to a freezer location (so when freezing it) the due date will no longer be replaced when the product option "Default due days after freezing" is set to `0`
- The "Decimal places allowed" stock settings now have an upper limit of 10 (since such a high number makes not so much sense in reality and causes form validation problems)
- Fixed that a once set quantity unit on a product barcode could not be removed on edit
- Fixed that when consuming a specific stock entry which is opened, and which originated from a before partly opened stock entry, the unopened one was wrongly consumed instead

### Shopping list

- Added a new shopping list setting (top right corner settings menu) "Round up quantity amounts to the nearest whole number"
  - When enabled, all quantity amounts on the shopping list are always displayed rounded up to the nearest whole number
  - Defaults to disabled, so no changed behavior when not configured

### Recipes

- Consuming a recipe is now also possible when not all needed ingredients are currently in stock (the in stock amount, if any, of the corresponding ingredient will be consumed in that case)
- For in stock ingredients, the amount actually in stock is now displayed next to the hint "Enough in stock"
- Added a new recipe ingredient option "Round up quantity amounts to the nearest whole number"
  - When enabled, resulting quantity amounts (after scaling according the desired serving amount) are always rounded up to the nearest whole number
  - Defaults to disabled, so no changed behavior when not configured
- Optimizations/changes when adding missing recipe ingredients to the shopping list:
  - When the ingredient option "Only check if any amount is in stock" is enabled and when no corresponding unit conversion exists, the amount/unit is now taken "as is" (as defined in the recipe ingredient) into the created shopping list item
  - The shopping list item no longer gets a note "Added for recipe" set and the ingredient note is no longer appended
  - When the corresponding product is already on the shopping list, the amount of the existing item is incremented instead of creating a new shopping list item
  - When a new shopping list item is created, the product's "Default quantity unit purchase" is now used (instead of the product's "Quantity unit stock")
- When no price information is available for at least one ingredient, a red exclamation mark is now displayed next to the recipe total cost information
- When clicking a recipe ingredient / product name, the product card will now be opened like on many other places throughout Grocy
- Fixed that calories/costs of recipe ingredients were wrong when the ingredient option "Only check if any amount is in stock" was set and the on the ingredient used quantity unit was different from the product's QU stock
- Fixed that multi-nested recipes (at least 3 levels of "included recipes") resulted in wrong amounts/costs/calories calculated for the ingredients originating in those nested recipes (also affected the meal plan)

### Meal plan

- Fixed that amounts/costs/calories were wrong for recipes which had at least 2 levels of "included recipes"

### Chores

- Added a possibility to see if a scheduled schedule chore was tracked/done on time or not:
  - When tracking chores, the "Next estimated tracking date" (so the current scheduled time) is now also stored in the corresponding chore journal entry and displayed in a new column "Scheduled tracking time" on the chores journal page
  - When the "Tracked time" is later than the "Scheduled tracking time", the corresponding chore journal entry is now highlighted in red on the chores journal page
- Added a new column "Time of tracking" on the chores journal page (displays the time when the tracking actually happened, hidden by default)
- Added a new option "Swap track next schedule / track now buttons" (chores settings / top right corner settings menu) to swap the "Track next chore schedule" / "Track chore execution now" buttons/menu items on the chores overview page (defaults to disabled, so no changed behavior when not configured)

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
