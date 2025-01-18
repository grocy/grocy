DROP VIEW stock_next_use;
CREATE VIEW stock_next_use
AS

/*
	The default consume rule is:
	Opened first, then first due first, then first in first out
	Apart from that products at their default consume location should be consumed first

	This orders the stock entries by that
	=> Highest "priority" per product = the stock entry to use next
	=> ORDER BY clause = ORDER BY priority DESC, open DESC, best_before_date ASC, purchased_date ASC
*/

SELECT
	(ROW_NUMBER() OVER(PARTITION BY s.product_id ORDER BY CASE WHEN IFNULL(p.default_consume_location_id, -1) = s.location_id THEN 0 ELSE 1 END ASC, s.open DESC, s.best_before_date ASC, s.purchased_date ASC)) * -1 AS priority,
	s.*
FROM stock s
JOIN products p
	ON p.id = s.product_id
ORDER BY CASE WHEN IFNULL(p.default_consume_location_id, -1) = s.location_id THEN 0 ELSE 1 END ASC, s.open DESC, s.best_before_date ASC, s.purchased_date ASC;

CREATE TRIGGER stock_next_use_INS INSTEAD OF INSERT ON stock_next_use
BEGIN
	INSERT INTO stock
		(product_id, amount, best_before_date, purchased_date, stock_id,
		price, open, opened_date, location_id, shopping_location_id, note)
	VALUES
		(NEW.product_id, NEW.amount, NEW.best_before_date, NEW.purchased_date, NEW.stock_id,
		NEW.price, NEW.open, NEW.opened_date, NEW.location_id, NEW.shopping_location_id, NEW.note);
END;

CREATE TRIGGER stock_next_use_UPD INSTEAD OF UPDATE ON stock_next_use
BEGIN
	UPDATE stock
	SET product_id = NEW.product_id,
	amount = NEW.amount,
	best_before_date = NEW.best_before_date,
	purchased_date = NEW.purchased_date,
	stock_id = NEW.stock_id,
	price = NEW.price,
	open = NEW.open,
	opened_date = NEW.opened_date,
	location_id = NEW.location_id,
	shopping_location_id = NEW.shopping_location_id,
	note = NEW.note
	WHERE id = NEW.id;
END;

CREATE TRIGGER stock_next_use_DEL INSTEAD OF DELETE ON stock_next_use
BEGIN
	DELETE FROM stock
	WHERE id = OLD.id;
END;

DROP VIEW products_current_substitutions;
CREATE VIEW products_current_substitutions
AS

/*
	When a parent product is not in stock itself,
	any sub product (the next based on the default consume rule) should be used

	This view lists all parent products and in the column "product_id_effective" either itself,
	when the corresponding parent product is currently in stock itself, or otherwise the next sub product to use
*/

SELECT
	-1, -- Dummy
	p_sub.id AS parent_product_id,
	CASE WHEN p_sub.has_sub_products = 1 THEN
		CASE WHEN IFNULL(sc.amount, 0) = 0 THEN -- Parent product itself is currently not in stock => use the next sub product
			(
			SELECT x_snu.product_id
			FROM products_resolved x_pr
			JOIN stock_next_use x_snu
				ON x_pr.sub_product_id = x_snu.product_id
			WHERE x_pr.parent_product_id = p_sub.id
				AND x_pr.parent_product_id != x_pr.sub_product_id
			ORDER BY x_snu.priority DESC, x_snu.open DESC, x_snu.best_before_date ASC, x_snu.purchased_date ASC
			LIMIT 1
			)
		ELSE -- Parent product itself is currently in stock => use it
			p_sub.id
		END
	END AS product_id_effective
FROM products_view p
JOIN products_resolved pr
	ON p.id = pr.parent_product_id
JOIN products_view p_sub
	ON pr.sub_product_id = p_sub.id
JOIN stock_current sc
	ON p_sub.id = sc.product_id
WHERE p_sub.has_sub_products = 1;

DROP VIEW products_current_price;
CREATE VIEW products_current_price
AS

/*
	Current price per product,
	based on the stock entry to use next,
	or on the last price if the product is currently not in stock
*/

SELECT
	-1 AS id, -- Dummy,
	p.id AS product_id,
	IFNULL(snu.price, plp.price) AS price
FROM products p
LEFT JOIN (
	SELECT
		product_id,
		MAX(priority),
		price -- Bare column, ref https://www.sqlite.org/lang_select.html#bare_columns_in_an_aggregate_query
	FROM stock_next_use
	GROUP BY product_id
	ORDER BY priority DESC, open DESC, best_before_date ASC, purchased_date ASC
	) snu
	ON p.id = snu.product_id
LEFT JOIN products_last_purchased plp
	ON p.id = plp.product_id;
