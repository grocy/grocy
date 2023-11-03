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
- Fixed that the location dropdown on the consume page contained the same location multiple times if there are currently stock entries at multiple locations of the corresponding product
- Fixed that the status filter "n products are overdue" on the stock overview page also counted/included stock entries due today or tomorrow

### Shopping list

- xxx

### Recipes

- Fixed that copying recipes with special characters in the name was not possible

### Meal plan

- Fixed that the meal plan page was broken after deleting _all_ (of all weeks) meal plan items

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

- Fixed that when having a userfield of type "Select list (multiple items can be selected)" and selecting no item, editing of the corresponding form was broken

### General

- Optimized sidebar icon spacing (thanks @chris-thorn)

### API

- xxx
