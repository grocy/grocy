DELETE FROM userfield_values
WHERE IFNULL(value, '') = '';

CREATE TRIGGER prevent_empty_userfields_INS AFTER INSERT ON userfield_values
BEGIN
	DELETE FROM userfield_values
	WHERE id = NEW.id
		AND IFNULL(value, '') = '';
END;

CREATE TRIGGER prevent_empty_userfields_UPD AFTER UPDATE ON userfield_values
BEGIN
	DELETE FROM userfield_values
	WHERE id = NEW.id
		AND IFNULL(value, '') = '';
END;
