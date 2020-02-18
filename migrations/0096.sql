PRAGMA legacy_alter_table = ON;

ALTER TABLE meal_plan RENAME TO meal_plan_old;

CREATE TABLE meal_plan (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	day DATE NOT NULL,
	type TEXT DEFAULT 'recipe',
	recipe_id INTEGER,
	recipe_servings INTEGER DEFAULT 1,
	note TEXT,
	product_id INTEGER,
	product_amount REAL DEFAULT 0,
	product_qu_id INTEGER,
	row_created_timestamp DATETIME DEFAULT (datetime('now', 'localtime'))
);

INSERT INTO meal_plan
	(day, recipe_id, recipe_servings, row_created_timestamp, type)
SELECT day, recipe_id, servings, row_created_timestamp, 'recipe'
FROM meal_plan_old;

DROP TABLE meal_plan_old;

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
	SELECT (SELECT id FROM recipes WHERE name = NEW.day AND type = 'mealplan-day'), recipe_id, SUM(recipe_servings)
	FROM meal_plan
	WHERE day = NEW.day
		AND type = 'recipe'
		AND recipe_id IS NOT NULL
	GROUP BY recipe_id;

	-- Add all recipes for this week as included recipes in the week-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week'), recipe_id, SUM(recipe_servings)
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', NEW.day)
		AND type = 'recipe'
		AND recipe_id IS NOT NULL
	GROUP BY recipe_id;

	-- Add all products for this day as ingredients in the day-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = NEW.day AND type = 'mealplan-day'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE day = NEW.day
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;

	-- Add all products for this week as ingredients recipes in the week-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', NEW.day)
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;
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
	SELECT (SELECT id FROM recipes WHERE name = OLD.day AND type = 'mealplan-day'), recipe_id, SUM(recipe_servings)
	FROM meal_plan
	WHERE day = OLD.day
		AND type = 'recipe'
		AND recipe_id IS NOT NULL
	GROUP BY recipe_id;

	-- Add all recipes for this week as included recipes in the week-recipe
	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0') AND type = 'mealplan-week'), recipe_id, SUM(recipe_servings)
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', OLD.day)
		AND type = 'recipe'
		AND recipe_id IS NOT NULL
	GROUP BY recipe_id;

	-- Add all products for this day as ingredients in the day-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = OLD.day AND type = 'mealplan-day'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE day = OLD.day
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;

	-- Add all products for this week as ingredients recipes in the week-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0') AND type = 'mealplan-week'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', OLD.day)
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;
END;
