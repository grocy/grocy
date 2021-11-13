- The "Below min. stock amount" filter on the stock overview page now also includes due-soon, overdue or already expired products
- The default shopping list (named "Shopping list"; localized) can now be renamed
- Added the products average price as a (hidden by default) column on the stock overview page
- Added a new "Presets for new products" stock setting for the "Default due days" option of new products
- Fixed that the "Stay logged in permanently" checkbox on the login page had no effect (thanks @0)
- Fixed that the labels of context-/more-menu items were not readable in Night Mode (thanks @corbolais)
- Fixed that auto night mode over midnight did not always work
- Fixed that the "Add as new product" productpicker workflow, started from the shopping list item form, always selected the default shopping list after finishing the flow
- Fixed that when undoing a product opened transaction and when the product has "Default due days after opened", the original due date wasn't restored
- Fixed that "Track date only"-chores were shown as overdue on the due day on the chores overview page
- Fixed that dropdown filters for tables maybe did not work after reordering columns

### API
- Fixed that backslashes were not allowed in API query filters
