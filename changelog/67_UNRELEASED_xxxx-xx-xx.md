> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

### New feature: Notes and Userfields for stock entries

- Stock entries can now have notes
  - For example to distinguish between same, yet different products (e.g. having only a generic product "Chocolate" and note in that field what special one it is exactly this time - an alternative to have sub products)
  - => New field on the purchase and inventory page
  - => New column on the stock entries and stock journal page
  - => Visible also in the "Use a specific stock item" dropdown on the consume and transfer page
- Additionally it's also possible to add arbitrary own fields by using Userfields
  - => Configure the desired Userfields for the entity `stock`
  - => Those Userfields are then visible on the same places as mentioned above for the built-in "Note" field

### New feature: Recipes "Due score"

- A number (new column on the recipes page) which represents a score which is higher the more ingredients, of the corresponding recipe, currently in stock are due soon, overdue or already expired
  - Or in other words: A score to see which recipes to cook to not waste already overdue/expired or due soon products
- The score is in detail based on:
  - 1 point for each due soon ingredient (based on the stock setting "Due soon days")
  - 10 points per overdue ingredient
  - 20 points per expired ingredient
  - (or else 0)
- The corresponding ingredient is also highlighted in red/yellow/grey (same colors as on the stock overview page)

### Stock

- It's now possible to change a products stock QU, even after it was once added to stock
  - When the product was once added to stock, there needs to exist a corresponding unit conversion for the new QU
- New product option "Disable own stock" (defaults to disabled)
  - When enabled, the corresponding product can't have own stock, means it will not be selectable on purchase (useful for parent products which are just used as a summary/total view of the child products)
- The location content sheet can now optionally list also out of stock products (at the products default location, new checkbox "Show only in-stock products " at the top of the page, defaults to enabled)
- Added the product grocycode as a (hidden by default) column to the products list (master data)
- Fixed that consuming via the consume page was not possible when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was disabled

### Shopping list

- Fixed that when using "Add products that are below defined min. stock amount", the calculated missing amount was wrong for products which had the new product option `Treat opened as out of stock` set and when having at least one opened stock entry

### Recipes

- Added a new recipes setting (top right corner settings menu) "Show a little checkbox next to each ingredient to mark it as done" (defaults to disabled)
  - When enabled, next to each ingredient a little checkbox will be shown, when clicked, the ingredient is crossed out (the status is not saved, means reset when the page is reloaded)
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
- It's now possible to manually reschedule chores
  - New entry "Reschedule next execution" in the context/more menu on the chores overview page
  - If you have rescheduled a chore and want to continue the normal schedule instead, use the "Clear" button in the dialog
  - Rescheduled chores will be highlighted with an corresponding icon next to the "next estiamted tracking date"
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

- It's now possible to add multiple files (PDFs / manuals) to each equipment
  - Define as many Userfields for the entity `equipment` and use the type `File`
  - => Each of those File-Userfields will be shown as a separate tab on the equipment page

### Userfields

- xxx

### General

- Optimized form validation: Save / submit buttons are now not disabled when the form is invalid, the invalid / missing fields are instead highlighted when trying to submit / save the form (making it more obvious which fields are invalid / missing exactly)
- Fixed an server error (on every page) when not having any quantity unit

### API

- Added a new endpoint `GET /stock/locations/{locationId}/entries` to get all stock entries of a given location (similar to the already existing endpoint `GET /stock/products/{productId}/entries`)
- Endpoint `/recipes/{recipeId}/consume`: Fixed that consuming partially fulfilled recipes was possible, although an error was already returned in that case (and potentially some of the in-stock ingredients were consumed in fact)
- Endpoint `/stock/products/{productId}`:
  - New field/property `current_price` which returns the current price of the corresponding product, based on the stock entry to use next (defined by the default consume rule "Opened first, then first due first, then first in first out") or on the last price if the product is currently not in stock
  - The field/property  `oldest_price` is deprecated and will be removed in a future version (this had no real sense, currently returns the same as `current_price`)
