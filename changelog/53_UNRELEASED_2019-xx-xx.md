### Stock improvements/fixes
- Fixed that barcode lookups now compare the whole barcode, not parts of it (e. g. when you have two products with the barcodes `$1` and `$10` and scan `$1` maybe the product of `$10` was found till now)
- Fixed that the "X products are already expired" count on the stock overview page was wrong
- Fixed that after product actions (consume/purchase/etc.) on the stock overview page the highlighting of the row was maybe wrong
- After product actions (consume/purchase/etc.) on the stock overview page on a sub product, now also the parent product (row) is refreshed
- It's now possible to accumulate min. stock amounts on parent product level (new option per product, means the sub product will never be "missing" then, only the parent product)
- When adding a product to the shopping list from the new context/more menu from the stock overview page and if the product is already on the shopping list, the amount of that entry will be updated acccordingly instead of adding a new (double) shopping list item

### Recipe improvements/fixes
- Fixed a problem regarding quantity unit conversion handling for recipe ingredients of products with no unit relations, but only a different purchase/stock quantity unit
- It's now possible to display a recipe directly from the meal plan (new "eye button") (thanks @kriddles)
- Improved the responsiveness of the meal plan and calendar page by automatically switching to a day calendar view on smaller screens (thanks for the idea @kriddles)

### Chores improvements
- There is now a new sub feature flag `FEATURE_FLAG_CHORES_ASSIGNMENTS` to disable chore assignments if you don't need them (defaults to `true`, so no changed behavior when not configured)

### Calendar improvements
- The calendar now also contains all planned recipes from the meal plan on the corresponding day
- Improved that dates in the iCal calendar export now include the server timezone

### General & other improvements/fixes
- Fixed that the browser barcode scanner button was not clickable on iOS Safari & other small styles fixes/improvements for iOS Safari (thanks @DeeeeLAN)
- It's now also possible to set the meal plan page as the default/entry page (`config.php` setting `ENTRY_PAGE`) (thanks @lwis)
- Some UI detail-refinements

### API improvements/fixes
- The API Endpoint `GET /files/{group}/{fileName}` now also returns a `Cache-Control` header (defaults fixed to 30 days) to further increase page load times
- Fixed that the API endpoint `/stock/shoppinglist/add-product` failed when a product should be added which was not already on the shopping list (thanks @Forceu)
