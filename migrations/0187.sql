ALTER TABLE products
ADD default_consume_location_id INTEGER;

DROP VIEW stock_next_use;
CREATE VIEW stock_next_use
AS

/*
	The default consume rule is:
	Opened first, then first due first, then first in first out
	Apart from that products at their default consume location should be consumed first

	This orders the stock entries by that
	=> Highest "priority" per product = the stock entry to use next
*/

SELECT
	(ROW_NUMBER() OVER(PARTITION BY s.product_id ORDER BY CASE WHEN IFNULL(p.default_consume_location_id, -1) = s.location_id THEN 0 ELSE 1 END ASC, s.open DESC, s.best_before_date ASC, s.purchased_date ASC)) * -1 AS priority,
	s.*
FROM stock s
JOIN products p
	ON p.id = s.product_id;

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
