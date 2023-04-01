CREATE VIEW product_purchase_history
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	p.id AS product_id,
	p.name AS product_name,
	g.id AS product_group_id,
	g.name AS product_group,
	s.amount AS quantity,
	s.price AS price,
	s.purchased_date AS purchased_date
FROM product_groups g
JOIN products p
	ON p.product_group_id = g.id
JOIN stock_log s
	ON s.product_id = p.id
WHERE s.transaction_type = 'purchase'
	AND s.undone = 0
	AND s.price IS NOT NULL
ORDER BY p.name ASC;
