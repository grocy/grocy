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

- Fixed an server error (on every page) when not having any quantity unit

### API

- Endpoint `/recipes/{recipeId}/consume`: Fixed that consuming partially fulfilled recipes was possible, although an error was already returned in that case (and potentially some of the in-stock ingredients were consumed in fact)
