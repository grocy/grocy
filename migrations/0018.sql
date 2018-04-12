INSERT INTO shopping_list
	(product_id, amount, amount_autoadded, row_created_timestamp)
SELECT product_id, amount, amount_autoadded, row_created_timestamp
FROM shopping_list_old
