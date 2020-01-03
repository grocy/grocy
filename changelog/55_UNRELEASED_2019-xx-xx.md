### New feature: Transfer products between locations and edit stock entries
- New menu entry in the sidebar to transfer products (or as a shortcut in the more/context menu per line on the stock overview page)
- New menu entry in the more/context menu of stock overview page lines to show the detail stock entries behind the corresponding product
  - From there you can also edit the stock entries
- A huge THANK YOU goes to @kriddles for the work on this feature

### Stock fixes
- Fixed that entering partial amounts was not possible on the inventory page (only applies if the product option "Allow partial units in stock" is enabled)
- Fixed that on purchase a wrong minimum amount was enforced for products with enabled tare weight handling in combination with different purchase/stock quantity units

### Shopping list fixes
- Fixed that when `FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS` was set to `false`, the shopping list appeared empty after some actions

### Recipe improvements
- When adding or editing a recipe ingredient, a dialog is now used instead of switching between pages (thanks @kriddles)

### Meal plan fixes
- Fixed that when `FEATURE_FLAG_STOCK_PRICE_TRACKING` was set to `false`, prices were still shown (thanks @kriddles)

### Calendar improvements
- Improved that meal plan events in the iCal calendar export now contain a link to the appropriate meal plan week in the body of the event (thanks @kriddles)

### Task fixes
- Fixed that a due date was required when editing an existing task

### API improvements/fixes
- Fixed that the route `/stock/barcodes/external-lookup/{barcode}` did not work, because the `barcode` argument was expected as a route argument but the route was missing it (thanks @Mikhail5555 and @beetle442002)
- New endpoints for the stock transfer & stock entry edit capabilities mentioned above

### General & other improvements/fixes
- Fixed that the meal plan menu entry (sidebar) was not visible when the calendar was disabled (`FEATURE_FLAG_CALENDAR`) (thanks @lwis)
- Slightly optimized table loading & search performance (thanks @lwis)
- For integration: If a `GET` parameter `closeAfterCreation` is passed to the product edit page, the window will be closed on save (due to Browser restrictions, this only works when the window was opened from JavaScript) (thanks @Forceu)
- The `update.sh` file had wrong line endings (DOS instead of Unix)
- New translations: (thanks all the translators)
  - Portuguese (Brazil) (demo available at https://pt-br.demo.grocy.info)
