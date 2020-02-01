### Stock fixes
- Fixed purchase/consume/inventory problems when `FEATURE_FLAG_STOCK_LOCATION_TRACKING` was set to `false`

### Shopping list fixes
- Optimized the new compact view (there was a little too much white space at the sides of the page)

### Recipe improvements/fixes
- Fixed that when editing a recipe ingredient which had "Only check if a single unit is in stock" set, not any quantity unit could be picked and the amount stayed empty
- Optimized the ordering of the inputs on the recipe ingredient edit page (moved "Only check if a single unit is in stock" before the amount)
