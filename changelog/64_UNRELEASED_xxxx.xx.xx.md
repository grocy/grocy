- Fixed that the "Add all list items to stock" shopping list workflow did not work for more than ~6 items (thanks @tjhowse)
- Fixed that plural form handling (e.g. for quantity units) was wrong for negative numbers
- Fixed that the context menu entries Consume and Transfer on the stock overview page were disabled when the amount in stock was < 1
- The product and chore edit pages now have bottom-sticky save buttons
- A product picture can now be added when creating a product (was currently only possible when editing a product)

### API
- Fixed that international characters were not allowed in API query filters
