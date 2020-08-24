> !!! The major version bump is due to breaking API changes, please see below if you use the API

### New feature: Link product of different sizes
- Imagine you buy for example eggs in different pack sizes
- The "Factor purchase to stock quantity unit" can now be set during purchase (if the products purchase and stock QU are different)
- Additionally, each product barcode can be assigned a different "Factor purchase to stock quantity unit" (on the product edit page), which is then automatically prefilled on the purchase page
- (Thanks @kriddles)

### New feature: Reverse proxy authenticaton support
- New `config.php` settings `AUTH_CLASS` and `REVERSE_PROXY_AUTH_HEADER`
- If you set `AUTH_CLASS` to `Grocy\Middleware\ReverseProxyAuthMiddleware` and your reverse proxy sends a username in the HTTP header `REMOTE_USER` (header name can be changed by the setting `REVERSE_PROXY_AUTH_HEADER`), the user is automatically authenticated (and will also be created, if not already present)
- (Thanks @fipwmaqzufheoxq92ebc for the initial work on this)

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
- Fixed that `FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT` (option to configure if opened products should be considered for minimum stock amounts) was not handled correctly (thanks @teddybeermaniac)
- Fixed that the "Expiring soon" sum (yellow header-button) on the stock overview page didn't include products which expire today (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the shopping cart icon on the stock overview page was also shown if the product was on an already deleted shopping list (if enabled) (thanks @fipwmaqzufheoxq92ebc)
- Fixed that when editing a stock entry without a price, the price field was prefilled with `1`
- Fixed that location & product groups filter on the stock overview page did a contains search instead an exact search

### Recipe improvements/fixes
- It's now possible to print recipes (button next to the recipe title) (thanks @zsarnett)
- Improved recipe card presentation (thanks @zsarnett)
- Improved the recipe adding workflow (a recipe called "New recipe" is now not automatically created when starting to add a recipe) (thanks @zsarnett)
- Fixed that images on the recipe gallery view were not scaled correctly on largers screens (thanks @zsarnett)
- Fixed that decimal ingredient amounts maybe resulted in wrong conversions truncated decimal places if your locale does not use a dot as the decimal separator (thanks @m-byte)
- Fixed that a recipe cannot be included in itself (because this will cause an infinite loop) (thanks @fipwmaqzufheoxq92ebc)
- Fixed that when editing a recipe ingredient the checkbox "Disable stock fulfillment checking for this ingredient" was not initaliased with the saved value
- Fixed that when using the "+"-symbol (which appears on small screens only to expand columns which don't fit the screen) the page reloaded

### Chores improvements/fixes
- Changed that not assigned chores on the chores overview page display now just a dash instead of an ellipsis in the "Assigned to" column to make this more clear (thanks @Germs2004)
- Fixed (again) that weekly chores, where the next execution should be in the same week, were scheduled (not) always (but sometimes) for the next week only (thanks @shadow7412)
- Fixed that the assignment type "In alphabetic order" did not work correctly (the last person in the list was always assigned next once reached) (thanks @fipwmaqzufheoxq92ebc)

### Calendar improvements/fixes
- Events are now links to the corresponding page (thanks @zsarnett)
- Fixed a PHP warning when using the "Share/Integrate calendar (iCal)" button (thanks @tsia)
- Fixed that "Track date only"-chores were always displayed at 12am (are now displayed as all-day events)

### API improvements/fixes
- Breaking changes:
  - All prices are now related to the products **stock** quantity unit (instead the purchase QU)
  - The product object no longer has a field `barcodes` with a comma separated barcode list, instead barcodes are now stored in a separate table/entity `product_barcodes` (use the existing "Generic entity interactions" endpoints to access them)
- Performance improvements of the `/stock/products/*` endpoints (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the endpoint `/objects/{entity}/{objectId}` always returned successfully, even when the given object not exists (now returns `404` when the object is not found) (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the endpoint `/stock/volatile` didn't include products which expire today (thanks @fipwmaqzufheoxq92ebc)
- Fixed (again) that CORS was broken

### General & other improvements/fixes
- UI refresh / style improvements (thanks @zsarnett)
- The data path (previously fixed to the `data` folder) is now configurable, making it possible to run multiple grocy instances from the same directory (with different `config.php` files / different database, etc.) (thanks @fgrsnau)
  - Via an environment variable `GROCY_DATAPTH` (higher priority)
  - Via an FastCGI parameter (lower priority)
- Performance improvements (page loading time) of the stock overview page (thanks @fipwmaqzufheoxq92ebc)
- The prerequisites checker now also checks for the minimum required SQLite version (thanks @Forceu)
- Replaced (again, added before in v2.7.0, then reverted in v2.7.1 due to some problems) [QuaggaJS](https://github.com/serratus/quaggaJS) (seems to be unmaintained) by [Quagga2](https://github.com/ericblade/quagga2)
- More `config.php` settings (see the section `Component configuration for Quagga2`) to tweak Quagga2 (this is the component used for device camera for barcode scanning) (thanks @andrelam)
- Some localization string fixes (thanks @duckfullstop)
- New translations: (thanks all the translators)
  - Greek (demo available at https://el.demo.grocy.info)
  - Korean (demo available at https://ko.demo.grocy.info)
