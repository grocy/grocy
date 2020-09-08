> !!! The major version bump is due to breaking API changes, please see below if you use the API

### New feature: Link product of different sizes
- Imagine you buy for example eggs in different pack sizes
- The "Factor purchase to stock quantity unit" can now be set during purchase (if the products purchase and stock QU are different)
- Additionally, each product barcode can be assigned a different "Factor purchase to stock quantity unit" (on the product edit page), which is then automatically prefilled on the purchase page
- (Thanks @kriddles)

## New feature: Permissions
- Users can now have permissions, can be configured per user on the "Manage users" page (lock icon)
- Default permissions for new users can be set via a new `config.php` setting `DEFAULT_PERMISSIONS` (defaults to `ADMIN`, so no changed behavior when not configured)
- All currently existing users will get all permissions (`ADMIN`) during the update/migration
- Creating API keys on the "Manage API keys"-page (top right corner settings menu) now requires the `ADMIN` permission
  - Other users only see their API keys on that page
- (Thanks @fipwmaqzufheoxq92ebc)

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
- On the stock journal page, it's now visible if a consume-booking was spoiled
- Added a "Clear filter"-button on the stock overview page to quickly reset applied filters
- It's now tracked who made a stock change (currently logged in user, visible on the stock journal page) (thanks @fipwmaqzufheoxq92ebc)
- Product edit page improvements ("Save & continue" button, deleting and adding a product picuture is now possible in one go) (thanks @Ma27)
- Fixed that it was not possible to leave the "Barcode(s)" on the product edit page by `TAB`
- Fixed that when adding products through a product picker workflow and when the created products contains special characters, the product was not preselected on the previous page (thanks @Forceu)
- Fixed that when editing a product the default store was not visible / always empty regardless if the product had one set (thanks @kriddles)
- Fixed that `FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT` (option to configure if opened products should be considered for minimum stock amounts) was not handled correctly (thanks @teddybeermaniac)
- Fixed that the "Expiring soon" sum (yellow header-button) on the stock overview page didn't include products which expire today (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the shopping cart icon on the stock overview page was also shown if the product was on an already deleted shopping list (if enabled) (thanks @fipwmaqzufheoxq92ebc)
- Fixed that when editing a stock entry without a price, the price field was prefilled with `1`
- Fixed that location & product groups filter on the stock overview page did a contains search instead an exact search
- Fixed that the amount on the success popup was wrong when consuming a product with "Tare weight handling" enabled
- Fixed that the aggregated amount of parent products was wrong on the stock overview page when the child products had not the same stock quantity units
- Fixed that edited stock entries were not considered for the price history chart on the product card

### Shopping list improvements
- Decimal amounts are now allowed (for any product, rounded by two decimal places)
- "Add products that are below defined min. stock amount" always rounded up the missing amount to an integral number, this now allows decimal numbers

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

### Userfield improvements/fixes
- New Userfield type "File" to attach any file, will be rendered as a link to the file in tables (if enabled) (thanks @fipwmaqzufheoxq92ebc)
- New Userfield type "Picture" to attach a picture, the picture will be rendered (small) in tables (if enabled) (thanks @fipwmaqzufheoxq92ebc)

### API improvements/fixes
- Breaking changes:
  - All prices are now related to the products **stock** quantity unit (instead the purchase QU)
  - The product object no longer has a field `barcodes` with a comma separated barcode list, instead barcodes are now stored in a separate table/entity `product_barcodes` (use the existing "Generic entity interactions" endpoints to access them)
- For better integration (apps), it's now possible to show a QR-Code for API keys (thanks @fipwmaqzufheoxq92ebc)
  - New QR-Code button on the "Manage API keys"-page (top right corner settings menu), the QR-Codes contains `<API-Url>|<API-Key>`
  - And on the calendar page when using the button "Share/Integrate calendar (iCal)", there the QR-Codes contains the Share-URL (which is displayed in the textbox above)
- The output of the following endpoints can now be filtered (by any field), ordered and paginated (thanks @fipwmaqzufheoxq92ebc)
  - `/objects/{entity}/search`
  - `/stock/products/{productId}/entries`
  - `/stock/products/{productId}/locations`
  - `/recipes/fulfillment`
  - `/users`
  - `/tasks`
  - `/chores`
  - `/batteries`
  - There are 4 new (optional) query parameters to use that
    - `order` The field to order by
    - `limit` The maximum number of objects to return
    - `offset` The number of objects to skip
    - `query[]` An array of conditions, each of them is a string in the form of `<field><condition><value>`, where
      - `<field>` is a field name
      - `<condition>` is a comparison operator, one of
        - `=` equal
        - `~` LIKE
        - `<` less
        - `>` greater
        - `>=` greater or equal
        - `<=` less or equal
      - `<value>` is the value to search for
- New endpoints `/stock/journal` and `/stock/journal/summary` to get the stock journal (thanks @fipwmaqzufheoxq92ebc)
- Performance improvements of the `/stock/products/*` endpoints (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the endpoint `/objects/{entity}/{objectId}` always returned successfully, even when the given object not exists (now returns `404` when the object is not found) (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the endpoint `/stock/volatile` didn't include products which expire today (thanks @fipwmaqzufheoxq92ebc)
- Fixed that the endpoint `/objects/{entity}` did not include Userfields for Userentities (so the effective endpoint `/objects/userobjects`)
- Endpoint `/calendar/ical`: Fixed that "Track date only"-chores were always set to happen at 12am (are treated as all-day events now)
- Fixed (again) that CORS was broken

### General & other improvements/fixes
- UI refresh / style improvements (thanks @zsarnett)
- The data path (previously fixed to the `data` folder) is now configurable, making it possible to run multiple grocy instances from the same directory (with different `config.php` files / different database, etc.) (thanks @fgrsnau)
  - Via an environment variable `GROCY_DATAPTH` (higher priority)
  - Via an FastCGI parameter (lower priority)
- The language can now be set per user (see the new user settings page / top right corner settings menu) (thanks @fipwmaqzufheoxq92ebc)
  - Additionally, the language is now also auto-guessed based on the browser locale (HTTP-Header `Accept-Language`)
  - The `config.php` option `CULTURE` was renamed to `DEFAULT_LOCALE`
  - So the used language is based on (in that order)
    - The user setting
    - If not set, then based on browser locale
    - If no matching localizaton was found, `DEFAULT_LOCALE` from `config.php` is used
- Performance improvements (page loading time) of the stock overview page (thanks @fipwmaqzufheoxq92ebc)
- The prerequisites checker now also checks for the minimum required SQLite version (thanks @Forceu)
- Replaced (again, added before in v2.7.0, then reverted in v2.7.1 due to some problems) [QuaggaJS](https://github.com/serratus/quaggaJS) (seems to be unmaintained) by [Quagga2](https://github.com/ericblade/quagga2)
- More `config.php` settings (see the section `Component configuration for Quagga2`) to tweak Quagga2 (this is the component used for device camera for barcode scanning) (thanks @andrelam)
- Some localization string fixes (thanks @duckfullstop)
- New translations: (thanks all the translators)
  - Greek (demo available at https://el.demo.grocy.info)
  - Korean (demo available at https://ko.demo.grocy.info)
