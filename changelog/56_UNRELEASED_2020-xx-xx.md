### Stock fixes
- Fixed purchase/consume/inventory problems when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was set to `false`

### Shopping list improvements/fixes
- Added an option to hide the month-calendar (in the shopping list settings / top right corner settings menu) (defaults to disabled, so please enable this option if you still want to have the month-calendar on the shopping list)
- Optimized the new compact view (there was a little too much white space at the sides of the page)
- Fixed that the "Shopping list to stock workflow" did not work when `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING` was set to `false`

### Recipe improvements/fixes
- Optimized the ordering of the inputs on the recipe ingredient edit page (moved "Only check if a single unit is in stock" before the amount)
- Variable ingredient amounts are now marked accordingly on the renedered recipe
- Fixed that when editing a recipe ingredient which had "Only check if a single unit is in stock" set, not any quantity unit could be picked and the amount stayed empty
- Fixed that when reloading the "new recipe"-page (or when it gets auto-reloaded due to "Auto reload on external changes" is enabled), for each reload a new recipe was created

### Meal plan improvements
- Improved that all add-dialogs can be submitted by using `ENTER` and that the next input is automatically selected after selecting a recipe/product

### General & other improvements/fixes
- Optimized the top navbar height and overall spacing to waste less space
- Replaced the scan-mode-switch-button by a native button because it's less disturbing
- Fixed that the "contextual time ago" of date/time pickers was not displayed
