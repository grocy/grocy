CREATE VIEW stock_splits
AS

/*
Helper view which shows splitted stock rows which could be compacted
*/

SELECT
	product_id,
	SUM(amount) AS total_amount,
	MIN(stock_id) AS stock_id_to_keep,
	MAX(id) AS id_to_keep,
	GROUP_CONCAT(id) AS id_group,
	GROUP_CONCAT(stock_id) AS stock_id_group,
	id -- Dummy
FROM stock
GROUP BY product_id, best_before_date, purchased_date, price, open, opened_date, location_id, shopping_location_id
HAVING COUNT(*) > 1;
