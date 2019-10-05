### Stock fixes
- Fixed that product specific quantity unit conversions (product overrides) were also displayed on the product edit page of other products with the same stock quantity unit

### Recipe fixes
- Fixed that recipes were displayed without ingredients if the total recipe count was > 100

### Shopping list improvements
- Added a new sub feature flag `FEATURE_FLAG_SHOPPINGLIST_MULTIPLE_LISTS` to disable multiple shopping lists if you only need one (defaults to `true`, so no changed behavior when not configured)

### Chores improvements
- Added a new period type "yearly" (for yearly schedules)
- Added a "period interval" option per chore to have more flexible schedules (possible for the daily/weekly/monthly/yearly schedules, means "schedule this chore only every x days/weeks/months" to have for example biweekly schedules)

### General & other improvements
- New Input shorthands for date fields to increase/decrease the date by 1 month/year (shift + arrow keys, see the full list [here](https://github.com/grocy/grocy#input-shorthands-for-date-fields))
