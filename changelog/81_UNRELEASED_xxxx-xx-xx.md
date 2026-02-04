> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- Optimized product definition quantity unit handling:
  - As long as a product was not once in stock, the product options "Default quantity unit purchase", "Default quantity unit consume" and "Quantity unit for prices" can now be changed to any other unit
  - When necessary (means when no default quantity unit conversions apply), "1:1" product specific quantity unit conversion between the product's QU stock and the corresponding other unit will now be created automatically after editing a product (like already done when initially creating a product with different unit definitions)
  - For convenience, when changing a product's QU stock and when all other unit properties ("Default quantity unit purchase" etc.) are the same, they will now be changed in tandem (like already done when creating a new product and initially setting the product's QU stock)
  - (This should drastically improve the workflow of completing a product setup looked up via an external barcode lookup plugin)
- Optimized the line plot markers color of the price history chart (product card) (thanks @DeepCoreSystem)
- External barcode lookup plugin optimizations:
  - When an image URL without a file extension is returned, the file extension is now determined by the Content-Type header (if any) (thanks @jordy-u for the idea)
  - Data URLs for images are now supported (`data:image/png;base64,xxxx`) (thanks @GammaC0de)
- Fixed that German Umlauts and other special characters were removed from product names when looking up a barcode via the built-in Open Food Facts external barcode lookup plugin
- Fixed that when using/scanning a barcode on the purchase page with a note attached (which prefills the note field) and when manually selecting another product afterwards, the note of the previously used barcode was incorrectly prefilled again
- Fixed that the "next input focus handling" (jumping to the next input after entering a value) didn't work at some places (e.g. after entering a purchased date on the purchase page)
- Fixed that changing the stock setting "Due soon days" wasn't saved/applied
- Fixed that "Reprint stock entry label" on the stock entry edit page didn't actually print the corresponding label

### Shopping list

- An amount of `0` is now allowed for shopping list items (just a convenience optimization, it was already possible to leave the amount field empty which implicitly resulted in an amount of `0`)
- Fixed that the "Add all list items to stock" workflow closed the dialog after every item instead only after the last one was added

### Recipes

- xxx

### Meal plan

- xxx

### Chores

- Fixed that changing the chore setting "Due soon days" wasn't saved/applied

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- Fixed that the corresponding form was broken when using a `%` in an Userfield caption (only affected numeric and date-time Userfields)

### General

- Added a workaround for different SQL errors when running Grocy on FreeBSD and SQLite 3.41+
- Fixed that the date input shorthand `[+/-]n[d/m/y]` didn't work when the value lenght was >= 4 (e.g. `+10d`)

### API

- xxx
