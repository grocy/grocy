> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- Fixed that certain fields were not copied when copying a product

### Shopping list

- xxx

### Recipes

- xxx

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- Fixed that the iCal export was broken (any shared `https://<Grocy>/api/calendar/ical?secret=xxx`-link always returned `401 Unauthorized`)

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- xxx

### General

- Optimized that table dropdown filters now search accent insensitive
- Fixed that it wasn't possible to log in using passwords containing non-latin characters

### API

- New API endpoint `/stock/products/{productId}/copy` (to copy a product)
- Fixed that API Key Authentication did not work when running Grocy in a subdirectory
