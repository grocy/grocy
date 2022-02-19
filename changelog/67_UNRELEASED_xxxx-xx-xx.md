> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- xxx

### Shopping list

- xxx

### Recipes

- Fixed that consuming recipes was possible when not all ingredients were in-stock (and this potentially consumed some of the in-stock ingredients; not matching the message "nothing removed")
- Fixed that the price of the "Produces product"-product, which is added to stock on consuming a recipe, was wrong (was the recipe total costs multiplied by the serving amount instead of only the recipe total costs)

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- Fixed that the batteries overview page was broken when there was any battery Userfields with enabled "Show as column in tables" option
- Fixed that grocycode label printer printing didn't work from the battery edit page (master data) (thanks @andreheuer)

### Equipment

- xxx

### Userfields

- xxx

### General

- Fixed an server error (on every page) when not having any quantity unit

### API

- Added a new endpoint `GET /stock/locations/{locationId}/entries` to get all stock entries of a given location (similar to the already existing endpoint `GET /stock/products/{productId}/entries`)
- Endpoint `/recipes/{recipeId}/consume`: Fixed that consuming partially fulfilled recipes was possible, although an error was already returned in that case (and potentially some of the in-stock ingredients were consumed in fact)
