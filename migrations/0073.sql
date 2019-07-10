DROP TRIGGER create_internal_recipe;
CREATE TRIGGER create_internal_recipe AFTER INSERT ON meal_plan
BEGIN
	/* This contains practically the same logic as the trigger remove_internal_recipe */

	-- Create a recipe per day
	DELETE FROM recipes
	WHERE name = NEW.day
		AND type = 'mealplan-day';

	INSERT OR REPLACE INTO recipes
		(id, name, type)
	VALUES
		((SELECT MIN(id) - 1 FROM recipes), NEW.day, 'mealplan-day');
	
	-- Create a recipe per week
	DELETE FROM recipes
	WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0')
		AND type = 'mealplan-week';

	INSERT INTO recipes
		(id, name, type)
	VALUES
		((SELECT MIN(id) - 1 FROM recipes), LTRIM(STRFTIME('%Y-%W', NEW.day), '0'), 'mealplan-week');
	
	-- Delete all current nestings entries for the day and week recipe
	DELETE FROM recipes_nestings
	WHERE recipe_id IN (SELECT id FROM recipes WHERE name = NEW.day AND type = 'mealplan-day')
		OR recipe_id IN (SELECT id FROM recipes WHERE name = NEW.day AND type = 'mealplan-week');

	-- Add all recipes for this day as included recipes in the day-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = NEW.day AND type = 'mealplan-day'), recipe_id, SUM(servings)
	FROM meal_plan
	WHERE day = NEW.day
	GROUP BY recipe_id;

	-- Add all recipes for this week as included recipes in the week-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week'), recipe_id, SUM(servings)
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', NEW.day)
	GROUP BY recipe_id;
END;

CREATE TRIGGER remove_internal_recipe AFTER DELETE ON meal_plan
BEGIN
	/* This contains practically the same logic as the trigger create_internal_recipe */

	-- Create a recipe per day
	DELETE FROM recipes
	WHERE name = OLD.day
		AND type = 'mealplan-day';

	INSERT OR REPLACE INTO recipes
		(id, name, type)
	VALUES
		((SELECT MIN(id) - 1 FROM recipes), OLD.day, 'mealplan-day');
	
	-- Create a recipe per week
	DELETE FROM recipes
	WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0')
		AND type = 'mealplan-week';

	INSERT INTO recipes
		(id, name, type)
	VALUES
		((SELECT MIN(id) - 1 FROM recipes), LTRIM(STRFTIME('%Y-%W', OLD.day), '0'), 'mealplan-week');
	
	-- Delete all current nestings entries for the day and week recipe
	DELETE FROM recipes_nestings
	WHERE recipe_id IN (SELECT id FROM recipes WHERE name = OLD.day AND type = 'mealplan-day')
		OR recipe_id IN (SELECT id FROM recipes WHERE name = OLD.day AND type = 'mealplan-week');

	-- Add all recipes for this day as included recipes in the day-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = OLD.day AND type = 'mealplan-day'), recipe_id, SUM(servings)
	FROM meal_plan
	WHERE day = OLD.day
	GROUP BY recipe_id;

	-- Add all recipes for this week as included recipes in the week-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0') AND type = 'mealplan-week'), recipe_id, SUM(servings)
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', OLD.day)
	GROUP BY recipe_id;
END;
