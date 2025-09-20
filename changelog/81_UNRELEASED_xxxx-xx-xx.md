> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- Optimized the line plot markers color of the price history chart (product card) (thanks @DeepCoreSystem)
- Optimized that when an external barcode lookup plugin returned an image URL without a file extension, the file extension is now determined by the Content-Type header (if any) (thanks @jordy-u for the idea)
- Fixed that German Umlauts were removed from product names when looking up a barcode via the built-in Open Food Facts external barcode lookup plugin
- Fixed that when using/scanning a barcode on the purchase page with a note attached (which prefills the note field) and when manually selecting another product afterwards, the note of the previously used barcode was incorrectly prefilled again
- Fixed that the "next input focus handling" (jumping to the next input after entering a value) didn't work at some places (e.g. after entering a purchased date on the purchase page)

### Shopping list

- An amount of `0` is now allowed for shopping list items (just a convenience optimization, it was already possible to leave the amount field empty which implicitly resulted in an amount of `0`)
- Fixed that the "Add all list items to stock" workflow closed the dialog after every item instead only after the last one was added

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

- Fixed that the date input shorthand `[+/-]n[d/m/y]` didn't work when the value lenght was >= 4 (e.g. `+10d`)

### API

- xxx
