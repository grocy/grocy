CREATE TRIGGER remove_recipe_from_meal_plans AFTER DELETE ON recipes
BEGIN
	DELETE FROM meal_plan
	WHERE recipe_id = OLD.id;
END;

-- Delete all recipes from the meal plan which doesn't exist anymore
DELETE FROM meal_plan
WHERE recipe_id NOT IN (SELECT id FROM recipes);
