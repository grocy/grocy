CREATE TRIGGER recipes_desired_servings_default AFTER INSERT ON recipes
BEGIN
	UPDATE recipes
	SET desired_servings = base_servings
	WHERE id = NEW.id;
END;
