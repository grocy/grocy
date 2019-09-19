### New feature: Custom entities / objects / lists
- Custom entities are based on Userfields and can be used to add any custom lists you want to have in grocy
- They can have an own menu entry in the sidebar
- => See "Manage master data" -> "Userentities" or try it on the demo: https://demo.grocy.info/userobjects/exampleuserentity

### New feature: Use the device camera for barcode scanning
- Available on any barcode-enabled field (so currently only for picking products) - a new camera button at the right of side the text field
- Implemented using [QuaggaJS](https://github.com/serratus/quaggaJS) - camera stream processing happens totally offline / client-side
- Please note due to browser security restrictions, this only works when serving grocy via a secure connection (`https://`)
- There is also a `config.php` setting `DISABLE_BROWSER_BARCODE_CAMERA_SCANNING` to disable this, if you don't need it at all

### Stock improvements
- Products can now have variations (nested products)
  - Define the parent product for a product on the product edit page (only one level is possible, means a product which is used as a parent product in another product, cannot have a parent product itself)
  - Parent and sub products can have stock (both are regular products, no difference from that side)
  - On the stock overview page the aggregated amount is displayed next to the amount (sigma sign)
  - When a recipe needs a parent product, the need is also fulfilled when enough sub product(s) are in stock
- Quantity units can now be linked (related measurements / unit conversion)
  - On the quantity unit edit page default conversion can be defined for each unit
  - Products "inherit" the default conversion and additionally can have their own / override the default ones
- It's now possible to print a "Location Content Sheet" with the current stock per location - new button at the top of the stock overview page (thought to hang it at the location, note used amounts on paper and track it in grocy later)
- The product description now can have formattings (HTML/WYSIWYG editor like for recipes)
- "Factor purchase to stock quantity unit" (product option) can now also be a decimal number when "Allow partial units in stock" is enabled

### Recipe improvements
- Based on the new linked quantity units, recipe ingredients can now use any product related unit, the amount is calculated according to the cnoversion factor of the unit relation

### Chores improvements
- Chores can now be assigned to users
  - Option per chore, different "assignment types" like "Random", "Who least did first", etc.
  - On the chores overview page the list can be filterd to only show chores assigned to the currently logged in user (or to any other user)
- New option "Due date rollover" per chore which means the chore can never be overdue, the due date will shift forward each day when due
- New option "Consume product on chore execution" per chore to automatically consume a product when a chore execution is tracked
- When tracking an execution from the chores overview page, filters are re-applied afterwards (means when have filtered the page to only show overdue chores and after the execution the chore is not overdue anymore, it will now immediately hide id)

### Equipment improvements/fixes
- Fixed that the delete button not always deleted the currently selected equipment item

### Userfield improvements/fixes
- New Userfield type "Select list" for a list of predefined values where a single or also multiple values can then be selected on the entity object
- New Userfield type "Link" - a single-line-textbox where the content will be rendered as a clickable link
- Userfields of type "checkbox" are rendered as a checkmark in tables when checked (instead of "1" as till now)
- Product Userfields are now also rendered on the shopping list (for items which have a product referenced)
- Fixed that the Userfield type "Preset list" had always the caption "Product group" instead of the configured one (thanks @oncleben31)

### General & other improvements/fixes
- Added a new `config.php` setting `CALENDAR_SHOW_WEEK_OF_YEAR` to configure if calendars should show week numbers (defaults to `true`)
- Fixed that datetimepickers not considered the `config.php` setting `CALENDAR_FIRST_DAY_OF_WEEK`
- Improved the handling which entry page to use with disabled feature flags (thanks @nielstholenaar)
- Boolean settings provided via environment variables (so the strings `true` and `false`) are now parsed correctly (thanks @mduret)
- All uploaded pictures (currently for products and recipes) are now automatically downscaled to the appropriate size when serving them to improve page load times
- It's now possible to test plural forms of quantity units (button on the quantity unit edit page)

### API improvements & non-breaking changes
  - New endpoint `/stock/shoppinglist/add-product` to add a product to a shopping list (thanks @Forceu)
  - New endpoint `/stock/shoppinglist/remove-product` to remove a product from a shopping list (thanks @Forceu)
  - New endpoint `/chores/executions/calculate-next-assignments` to (re)calculate next user assignments for a single or all chores
  - New endpoint `/objects/{entity}/search/{searchString}` search for objects by name (contains search)
  - Endpoint `GET /files/{group}/{fileName}` can now also downscale pictures (see API documentation on [/api](https://demo-en.grocy.info/api))
  - When adding a product (through `stock/product/{productId}/add` or `stock/product/{productId}/inventory`) with omitted best before date and if the given product has "Default best before days" set, the best before date is calculated based on that (so far always today was used which is still the case when no date is supplied and also the product has no "Default best before days set) (thanks @Forceu)
  - Field `stock_amount` of endpoint `/stock/products/{productId}´ now returns `0` instead of `null` when the given product is not in stock (thanks @Forceu)
  - Fixed that `/system/db-changed-time` always returned the current time (more or less) due to that that time is the database file modification time and the database is effectively changed on each request because of session information tracking - which now explicitly does not change the database file modification time, so this should work again to determine if any data changes happened
  - It's now also possible to provide the API key via a query parameter (same name as the header, so `GROCY-API-KEY`)
