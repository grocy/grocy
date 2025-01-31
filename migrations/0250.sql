DROP TRIGGER shopping_list_qu_id_default;

CREATE TRIGGER shopping_list_defaults_INS AFTER INSERT ON shopping_list
BEGIN
	UPDATE shopping_list
	SET qu_id = (SELECT qu_id_purchase FROM products WHERE id = product_id)
	WHERE IFNULL(qu_id, '') = ''
		AND id = NEW.id;

	UPDATE shopping_list
	SET amount = 0
	WHERE TYPEOF(amount) NOT IN ('integer', 'real')
		AND id = NEW.id;
END;

CREATE TRIGGER shopping_list_defaults_UPD AFTER UPDATE ON shopping_list
BEGIN
	UPDATE shopping_list
	SET qu_id = (SELECT qu_id_purchase FROM products WHERE id = product_id)
	WHERE IFNULL(qu_id, '') = ''
		AND id = NEW.id;

	UPDATE shopping_list
	SET amount = 0
	WHERE TYPEOF(amount) NOT IN ('integer', 'real')
		AND id = NEW.id;
END;

UPDATE shopping_list
SET qu_id = (SELECT qu_id_purchase FROM products WHERE id = product_id)
WHERE IFNULL(qu_id, '') = '';

UPDATE shopping_list
SET amount = 0
WHERE TYPEOF(amount) NOT IN ('integer', 'real');
