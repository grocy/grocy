ALTER TABLE meal_plan
ADD servings INTEGER DEFAULT 1;

ALTER TABLE recipes
ADD type TEXT DEFAULT 'normal';

CREATE INDEX ix_recipes ON recipes (
	name,
	type
);

CREATE TRIGGER create_internal_recipe AFTER INSERT ON meal_plan
BEGIN
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
