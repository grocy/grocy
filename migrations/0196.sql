DROP TRIGGER userfield_values_special_handling_INS;
CREATE TRIGGER userfield_values_special_handling_INS AFTER INSERT ON userfield_values
BEGIN
	-- Entity stock:
	-- object_id is the transaction_id on insert -> replace it by the corresponding stock_id
	INSERT OR REPLACE INTO userfield_values
		(field_id, object_id, value)
	SELECT uv.field_id, sl.stock_id, uv.value
	FROM userfield_values uv
	JOIN stock_log sl
		ON uv.object_id = sl.transaction_id
		AND sl.transaction_type IN ('purchase', 'inventory-correction', 'stock-edit-new')
	WHERE uv.field_id IN (SELECT id FROM userfields WHERE entity = 'stock')
		AND uv.field_id = NEW.field_id
		AND uv.object_id = NEW.object_id;

	DELETE FROM userfield_values
	WHERE field_id IN (SELECT id FROM userfields WHERE entity = 'stock')
		AND field_id = NEW.field_id
		AND object_id = NEW.object_id;
END;
