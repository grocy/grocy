CREATE VIEW product_purchase_history
AS
SELECT
	1 AS id, -- Dummy, LessQL needs an id column
	p.id as product_id,
	p.name as product_name,
	g.id as product_group_id,
	g.name as product_group,
	s.amount as quantity,
	s.price as price,
	s.purchased_date as purchased_date
FROM
	product_groups as g
INNER JOIN products as p
	ON p.product_group_id = g.id
INNER JOIN stock_log as s
	ON s.product_id = p.id
WHERE
	s.transaction_type = 'purchase'
AND
	s.undone = 0
AND
    s.price is not null
ORDER BY p.name ASC
