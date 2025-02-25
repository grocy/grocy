> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- Optimized that the built-in Open Food Facts external barcode lookup plugin now uses the localized product name (if provided by the Open Food Facts API, based on the set Grocy language of the current user)

### Shopping list

- When the shopping list setting (top right corner settings menu) "Round up quantity amounts to the nearest whole number" is enabled, the "Last price (Total)" of each shopping list item and the total value of the shopping list are now also scaled up accordingly
- The print options (show header, layout type etc.) are now saved (as user settings, so global defaults can also defined in `config.php` as usual)

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

- Fixed that saving Userfields of type "Link (with title)" did not work

### General

- Fixed that most dialogs didn't work when hosting Grocy "embedded" in an `iframe` (affecting e.g. the [Home Assistant Add-on](https://github.com/hassio-addons/addon-grocy))

### API

- Exposed the `permission_hierarchy` entity (read only, => `GET /objects/permission_hierarchy`) to provide a permission name / id mapping
