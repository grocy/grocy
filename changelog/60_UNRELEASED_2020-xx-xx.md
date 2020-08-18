> !!! The major version bump is due to breaking API changes, please see below if you use the API

### New feature: Link product of different sizes
- Imagine you buy for example eggs in different pack sizes
- The "Factor purchase to stock quantity unit" can now be set during purchase (if the products purchase and stock QU are different)
- Additionally, each product barcode can be assigned a different "Factor purchase to stock quantity unit" (on the product edit page), which is then automatically prefilled on the purchase page
- (Thanks @kriddles)

### Stock improvements/fixes
- When creating a quantity unit conversion it's now possible to automatically create the inverse conversion (thanks @kriddles)
- Optimized/clarified what the total/unit price is on the purchase page (thanks @kriddles)
- On the purchase page the amount field is now displayed above/before the best before date for better `TAB` handling (thanks @kriddles)
- Changed that when `FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING` is disabled, products now get internally a best before of "never expires" (aka `2999-12-31`) instead of today (thanks @kriddles)
- Products can now be hidden instead of deleted to prevent problems / missing information on existing references (new checkbox on the product edit page) (thanks @kriddles)
- Improved/fixed that changing the products "Factor purchase to stock quantity unit" not longer messes up historical prices (which results for example in wrong recipe costs) (thanks @kriddles)
- Fixed that it was not possible to leave the "Barcode(s)" on the product edit page by `TAB`
- Fixed that when adding products through a product picker workflow and when the created products contains special characters, the product was not preselected on the previous page (thanks @Forceu)
- Fixed that when editing a product the default store was not visible / always empty regardless if the product had one set (thanks @kriddles)

### Recipe improvements/fixes
- It's now possible to print recipes (button next to the recipe title) (thanks @zsarnett)
- Improved recipe card presentation (thanks @zsarnett)
- Improved the recipe adding workflow (a recipe called "New recipe" is now not automatically created when starting to add a recipe) (thanks @zsarnett)
- Fixed that images on the recipe gallery view were not scaled correctly on largers screens (thanks @zsarnett)

### Chores improvements/fixes
- Changed that not assigned chores on the chores overview page display now just a dash instead of an ellipsis in the "Assigned to" column to make this more clear (thanks @Germs2004)
- Fixed (again) that weekly chores, where the next execution should be in the same week, were scheduled (not) always (but sometimes) for the next week only (thanks @shadow7412)

### Calendar improvements/fixes
- Events are now links to the corresponding page (thanks @zsarnett)
- Fixed a PHP warning when using the "Share/Integrate calendar (iCal)" button (thanks @tsia)

### API improvements/fixes
- Breaking changes:
  - All prices are now related to the products **stock** quantity unit (instead the purchase QU)
  - The product object no longer has a field `barcodes` with a comma separated barcode list, instead barcodes are now stored in a separate table/entity `product_barcodes` (use the existing "Generic entity interactions" endpoints to access them)
- Fixed (again) that CORS was broken

### General & other improvements/fixes
- UI refresh / style improvements (thanks @zsarnett)
- The data path (previously fixed to the `data` folder) is now configurable, making it possible to run multiple grocy instances from the same directory (with different `config.php` files / different database, etc.) (thanks @fgrsnau)
  - Via an environment variable `GROCY_DATAPTH` (higher priority)
  - Via an FastCGI parameter (lower priority)
- The prerequisites checker now also checks for the minimum required SQLite version (thanks @Forceu)
- Some localization string fixes (thanks @duckfullstop)
- New translations: (thanks all the translators)
  - Greek (demo available at https://el.demo.grocy.info)
  - Korean (demo available at https://ko.demo.grocy.info)
