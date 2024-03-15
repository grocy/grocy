> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- When using/scanning a stock entry Grocycode on the transfer page, the "Use specific stock item" dropdown (and "From location") is now also prefilled accordingly
- Fixed that for the product's last price stock transactions with an empty or `0` price weren't ignored
- Fixed that when copying a product, the field "Move on open" wasn't copied along

### Shopping list

- The shopping lists dropdown on the shopping list page now also shows the number of items on the corresponding shopping list

### Recipes

- Optimized that when creating a recipe, the desired serving amount is now initially prefilled by the recipe's base serving amount

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- It's now possible to track any addtional info on a battery charge cycle by using Userfields (thanks @TheDodger)
  - => Configure the desired Userfields for the entity `battery_charge_cycles`
  - => The on a battery charge cycle tracking entered information is then visible on the corresponding battery charge cycle journal entry

### Equipment

- xxx

### Userfields

- xxx

### General

- Optimized that file uploads are not completely buffered in memory before writing them to disk (thanks @bbx0)
- Optimized top right corner menus icon spacing (thanks @TheDodger for the initial work on this)
- Fixed again that some dialogs were not properly (too small) displayed (this time mostly affecting Chrome/Edge)
- Fixed that table states (visible columns, sorting, column order and so on) could be reset to default after not accessing/using the corresponding page for a certain amount of time

### API

- xxx
