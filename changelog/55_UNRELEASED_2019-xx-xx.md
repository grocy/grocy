### Calendar improvements
- Improved that meal plan events in the iCal calendar export now contain a link to the appropriate meal plan week in the body of the event (thanks @kriddles)

### API fixes
- Fixed that the route `/stock/barcodes/external-lookup/{barcode}` did not work, because the `barcode` argument was expected as a route argument but the route was missing it (thanks @Mikhail5555 and @beetle442002)

### General & other improvements/fixes
- Fixed that the meal plan menu entry (sidebar) was not visible when the calendar was disabled (`FEATURE_FLAG_CALENDAR`) (thanks @lwis)
- Slightly optimized table loading & search performance (thanks @lwis)
- For integration: If a `GET` parameter `closeAfterCreation` is passed to the product edit page, the window will be closed on save (due to Browser restrictions, this only works when the window was opened from JavaScript) (thanks @Forceu)
- The `update.sh` file had wrong line endings (DOS instead of Unix)
- New translations: (thanks all the translators)
  - Portuguese (Brazil) (demo available at https://pt-br.demo.grocy.info)
