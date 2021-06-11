### Stock improvements/fixes
- Product barcodes are now enforced to be unique across products
- Fixed that editing stock entries was not possible
- Fixed that consuming with Scan Mode was not possible
- Fixed that the current stock total value (header of the stock overview page) didn't include decimal amounts (thanks @Ape)
- Fixed that the transfer page was not fully populated when opening it from the stock entries page

### Shopping list improvements/fixes
- The amount now defaults to `1` for adding items quicker
- Fixed that shopping list prints had a grey background (thanks @Forceu)
- Fixed the form validation on the shopping list item page (thanks @Forceu)

### Recipe improvements/fixes
- Recipe printing improvements (thanks @Ape)
- Calories are now always displayed per single serving (on the recipe and meal plan page)
- Fixed that "Only check if any amount is in stock" (recipe ingredient option) didn't work for stock amounts < 1

### Chores fixes
- Fixed that tracking chores with "Done by" a different user was not possible

### Userfield fixes
- Fixed that numeric Userfields were initialised with `1.0`

### General & other improvements/fixes
- Some night mode style improvements (thanks @BlizzWave and @KTibow)
- Fixed that the number picker up/down buttons did not work when the input field was empty or contained an invalid number

### API fixes
- Fixed that due soon products with `due_type` = "Expiration date" were missing in `due_products` of the `/stock/volatile` endpoint
