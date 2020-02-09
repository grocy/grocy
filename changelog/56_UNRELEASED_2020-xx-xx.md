### Stock fixes
- Fixed purchase/consume/inventory problems when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was set to `false`

### Shopping list improvements/fixes
- Added an option to hide the month-calendar (in the shopping list settings / top right corner settings menu) (defaults to disabled, so please enable this option if you still want to have the month-calendar on the shopping list)
- Optimized the new compact view (there was a little too much white space at the sides of the page)
- Added an option to not switch to the new compact view on mobile devices automatically (in the shopping list settings / top right corner settings menu) (defaults to `false`, so no changed behavior when not configured) (thanks @Forceu)
- Fixed that the "Shopping list to stock workflow" did not work when `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING` was set to `false`

### Recipe improvements/fixes
- Optimized the ordering of the inputs on the recipe ingredient edit page (moved "Only check if a single unit is in stock" before the amount)
- Variable ingredient amounts are now marked accordingly on the renedered recipe
- After selecting a recipe on mobile devices, the page now automatically scrolls to the recipe card
- Added the recipes base servings to be displayed on the recipe card and properly named the servings column in the recipes list/table (thanks @kriddles)
- Fixed that when editing a recipe ingredient which had "Only check if a single unit is in stock" set, not any quantity unit could be picked and the amount stayed empty
- Fixed that when reloading the "new recipe"-page (or when it gets auto-reloaded due to "Auto reload on external changes" is enabled), for each reload a new recipe was created
- Fixed that the recipe "fullscreen card" was not correctly displayed
- Fixed that nested recipes showed all ingredients of the nested recipes twice

### Meal plan improvements
- Improved that all add-dialogs can be submitted by using `ENTER` and that the next input is automatically selected after selecting a recipe/product
- Added an edit button to all types of meal plan entries
- When adding a recipe, the serving amount is now prefilled with the one of the selected recipe (thanks @kriddles)
- Fixed that the meal plan not used the full height on mobile devices

### Calendar fixes
- Fixed to only include events when the corresponding feature flag is enabled (e. g. don't show expiring products when `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING` is set to `false`) (thanks @kriddles)
- Fixed that the calendar not used the full height on mobile devices

### General & other improvements/fixes
- Optimized the top navbar height and overall spacing to waste less space
- Replaced the scan-mode-switch-button by a native button because it's less disturbing
- Fixed that the "contextual time ago" of date/time pickers was not displayed
