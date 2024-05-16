> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- Added a new product picker workflow "External barcode lookup (via plugin)"
  - This executes the configured barcode lookup plugin with the given barcode
  - If the lookup was successful, the product edit page of the created product is displayed, where the product setup can be completed (if required)
  - After that, the transaction is continued with that product
- Fixed that when copying a product, the field "Treat opened as out of stock" wasn't copied along (thanks @TheDodger)

### Shopping list

- xxx

### Recipes

- When self producing a product ("Produces product" recipe option) the name of the recipe is now added to the note field of the created stock entry

### Meal plan

- xxx

### Chores

- There is now a new sub feature flag `FEATURE_FLAG_CHORES_OVERVIEW_DEFAULT_TRACK_ON_NEXT_SCHEDULE` to change the behaviour of the green button on `/choresoverview`. If set to true, the green button will track an execution of the corresponding chore on the next scheduled time. If set to false it will instead track an execution for now/today. (Flag defaults to `true`, so no changed behavior when not configured)

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

- xxx
