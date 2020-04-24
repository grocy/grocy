### Stock improvements/fixes
- When creating a quantity unit conversion it's now possible to automatically create the inverse conversion (thanks @kriddles)
- Optimized/clarified what the total/unit price is on the purchase page (thanks @kriddles)
- On the purchase page the amount field is now displayed above/before the best before date for better `TAB` handling (thanks @kriddles)
- Changed that when `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING` is disabled, products now get internally a best before of "never expires" (aka `2999-12-31`) instead of today (thanks @kriddles)
- Fixed that it was not possible to leave the "Barcode(s)" on the product edit page by `TAB`
- Fixed that when adding products through a product picker workflow and when the created products contains special characters, the product was not preselected on the previous page (thanks @Forceu)

### Recipe improvements/fixes
- It's now possible to print recipes (button next to the recipe title) (thanks @zsarnett)
- Improved recipe card presentation (thanks @zsarnett)
- Improved the recipe adding workflow (a recipe called "New recipe" is now not automatically created when starting to add a recipe) (thanks @zsarnett)
- Fixed that images on the recipe gallery view were not scaled correctly on largers screens (thanks @zsarnett)

### Calendar improvements/fixes
- Events are now links to the corresponding page (thanks @zsarnett)
- Fixed a PHP warning when using the "Share/Integrate calendar (iCal)" button (thanks @tsia)

### API fixes
- Fixed (again) that CORS was broken

### General & other improvements
- UI refresh / style improvements (thanks @zsarnett)
