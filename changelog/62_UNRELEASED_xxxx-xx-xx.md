### Stock improvements/fixes
- Product barcodes are now enforced to be unique across products
- Fixed that editing stock entries was not possible
- Fixed that consuming with Scan Mode was not possible
- Fixed that the current stock total value (header of the stock overview page) didn't include decimal amounts (thanks @Ape)

### Shopping list fixes
- Fixed that shopping list prints had a grey background (thanks @Forceu)
- Fixed the form validation on the shopping list item page (thanks @Forceu)

### Recipe improvements
- Recipe printing improvements (thanks @Ape)

### Chores fixes
- Fixed that tracking chores with "Done by" a different user was not possible

### General & other improvements
- Some night mode style improvements (thanks @BlizzWave and @KTibow)

### API fixes
- Fixed that due soon products with `due_type` = "Expiration date" were missing in `due_products` of the `/stock/volatile` endpoint
