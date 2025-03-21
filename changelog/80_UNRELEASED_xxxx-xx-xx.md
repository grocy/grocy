> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

> 💡 xxxMinor upgrade informationXXX

### New Feature: xxxx

- xxx

### Stock

- Optimizations in the built-in Open Food Facts external barcode lookup plugin:
  - A provided but empty localized product name is now ignored
  - Non-ASCII characters in product names are now ignored (e.g. line breaks caused problems)
- Optimized that when an external barcode lookup plugin returned an image URL with query parameters, an invalid picture (file name) was added to the created product
- Added a new column "Default store" on the stock overview page (hidden by default)

### Shopping list

- xxx

### Recipes

- xxx

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- xxx

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- xxx

### General

- Optimized that the default font of the web frontend is now also used for non-latin characters
- Label printer WebHooks now include a new property/field `details` (that's the full product/chore/battery/etc. object)
  - And also `stock_entry` (containing the full stock entry object) when printing a stock entry label
- The component used (so far [Quagga2](https://github.com/ericblade/quagga2)) that powers the camera barcode scanner was replaced by [ZXing](https://github.com/zxing-js/library) which should perform overall better and it also supports 2D barcodes (QRCode/DataMatrix) (thanks @gergof)

### API

- xxx
