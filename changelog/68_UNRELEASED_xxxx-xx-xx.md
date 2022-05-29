> ⚠️ xxxBREAKING CHANGESxxx

> ❗ xxxImportant upgrade informationXXX

### New feature: xxxx

- xxx

### Stock

- New product option "Move on open" (defaults to disabled)
  - When enabled, on marking the product as opened, the corresponding amount will be moved to the products default consume location
  - (Thanks @RosemaryOrchard)
- Optimized that when the plural form(s) of a quantity unit is/are not provided, the singular form is used to display plural amounts
- Fixed "Automatically add products that are below their defined min. stock amount to the shopping list" (stock setting) was only done when consuming products, not when opening them

### Shopping list

- xxx

### Recipes

- xxx

### Meal plan

- xxx

### Chores

- xxx

### Calendar

- Fixed that clicking on meal plan product and notes calendar entries redirected to an invalid page

### Tasks

- xxx

### Batteries

- xxx

### Equipment

- xxx

### Userfields

- xxx

### General

- LDAP authentication: Optimized that it's not required that LDAP accounts need to have a first-/lastname

### API

- Endpoint `/stock/products/{productId}`: New field/property `default_consume_location` (contains the products default consume location object)
- Endpoint `/stock/products/{productId}/add`: Fixed that the request body parameter `transaction_type` was ignored / always set to `purchase`
- Fixed that the endpoint `/stock/products/by-barcode/{barcode}/open` didn't handle stock entries provided by a grocycode (thanks @jtommi)
