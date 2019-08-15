UPDATE products
SET description = REPLACE(description, CHAR(13) + CHAR(10), '<br>');

UPDATE products
SET description = REPLACE(description, CHAR(13), '<br>');

UPDATE products
SET description = REPLACE(description, CHAR(10), '<br>');
