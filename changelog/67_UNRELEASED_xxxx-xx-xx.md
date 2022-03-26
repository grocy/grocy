> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- It's now possible to change a products stock QU, even after it was once added to stock
  - When the product was once added to stock, there needs to exist a corresponding unit conversion for the new QU
- Added the product grocycode as a (hidden by default) column to the products list (master data)
- Fixed that consuming via the consume page was not possible when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was disabled

### Shopping list

- Fixed that when using "Add products that are below defined min. stock amount", the calculated missing amount was wrong for products which had the new product option `Treat opened as out of stock` set and when having at least one opened stock entry

### Recipes

- Fixed that consuming recipes was possible when not all ingredients were in-stock (and this potentially consumed some of the in-stock ingredients; not matching the message "nothing removed")
- Fixed that the price of the "Produces product"-product, which is added to stock on consuming a recipe, was wrong (was the recipe total costs multiplied by the serving amount instead of only the recipe total costs)
- Fixed that calories of recipe ingredients were displayed with an indefinite number of decimal places
- Fixed that ingredient amounts were wrong for multi-nested (> 2 levels) recipes, when the included recipe used an serving amount other than 1
- Fixed that searching/filtering the recipe gallery view did not work correctly

### Meal plan

- Fixed that it was not possible to print the meal plan (and other pages) in landscape (thanks @miguelangel-nubla)

### Chores

- The `Daily` period type has been changed to schedule the chore at the _same time_ (based on the start date) each `n` days
  - This period type scheduled chores `n` days _after the last execution_ before, which is also possible by using the `Hourly` period type and a corresponding period interval; all existing `Daily` schedules will be converted to that on migration
- Optimized that when skipping chores via the chore tracking page, the given time is used as the "skipped time", not the scheduled next estimated tracking time of the corresponding chore (essentially making it possible to skip more then one schedule at once)
- Fixed that when consuming a parent product on chore execution (chore option "Consume product on chore execution"), no child products were used if the parent product itself is not in-stock
- Fixed that the upgrade to v3.2.0 failed when having any former "Dynamic Regular" chore with a "Period interval" of `0` (which makes absolutely no sense in reality)

### Calendar

- xxx

### Tasks

- Fixed that tasks without a due date were highlighted in red (like overdue tasks)

### Batteries

- Fixed that the batteries overview page was broken when there was any battery Userfields with enabled "Show as column in tables" option
- Fixed that grocycode label printer printing didn't work from the battery edit page (master data) (thanks @andreheuer)
- Fixed that undoing a battery charge cycle had no effect on "Last charged" and "Next planned charge cycle" of the corresponding battery

### Equipment

- xxx

### Userfields

- xxx

### General

- Optimized form validation: Save / submit buttons are now not disabled when the form is invalid, the invalid / missing fields are instead highlighted when trying to submit / save the form (making it more obvious which fields are invalid / missing exactly)
- Fixed an server error (on every page) when not having any quantity unit

### API

- Added a new endpoint `GET /stock/locations/{locationId}/entries` to get all stock entries of a given location (similar to the already existing endpoint `GET /stock/products/{productId}/entries`)
- Endpoint `/recipes/{recipeId}/consume`: Fixed that consuming partially fulfilled recipes was possible, although an error was already returned in that case (and potentially some of the in-stock ingredients were consumed in fact)
