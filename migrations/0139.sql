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
		OR recipe_id IN (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week');

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

	-- Add all products for this week as ingredients in the week-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', NEW.day)
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;

	-- Create a shadow recipe per meal plan recipe
	INSERT INTO recipes
		(id, name, type)
	SELECT (SELECT MIN(id) - 1 FROM recipes), CAST(NEW.day AS TEXT) || '#' || CAST(id AS TEXT), 'mealplan-shadow'
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;

	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = CAST(NEW.day AS TEXT) || '#' || CAST(meal_plan.id AS TEXT) AND type = 'mealplan-shadow'), recipe_id, recipe_servings
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;

	-- Enforce "when empty then null" for certain columns
	UPDATE meal_plan
	SET recipe_id = NULL
	WHERE id = NEW.id
		AND IFNULL(recipe_id, '') = '';

	UPDATE meal_plan
	SET product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(product_id, '') = '';

	UPDATE meal_plan
	SET product_qu_id = NULL
	WHERE id = NEW.id
		AND IFNULL(product_qu_id, '') = '';
END;

DROP TRIGGER remove_internal_recipe;
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
		OR recipe_id IN (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0') AND type = 'mealplan-week');

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

	-- Add all products for this week as ingredients in the week-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', OLD.day), '0') AND type = 'mealplan-week'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', OLD.day)
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;

	-- Remove shadow recipes per meal plan recipe
	DELETE FROM recipes
	WHERE type = 'mealplan-shadow'
		AND name NOT IN (SELECT CAST(day AS TEXT) || '#' || CAST(id AS TEXT) FROM meal_plan WHERE type = 'recipe');
END;

CREATE TRIGGER TEMPORARY_update_internal_recipe AFTER UPDATE ON meal_plan
BEGIN
	/*
	Temporary trigger (only needed for migration, will be dropped again below),
	basically the same as update_internal_recipe,
	but only contains the part for generating the new mealplan-shadow recipes
	*/

	-- Create a shadow recipe per meal plan recipe
	INSERT INTO recipes
		(id, name, type)
	SELECT (SELECT MIN(id) - 1 FROM recipes), CAST(NEW.day AS TEXT) || '#' || CAST(id AS TEXT), 'mealplan-shadow'
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;

	DELETE FROM recipes_nestings
	WHERE recipe_id IN (SELECT id FROM recipes WHERE name IN (SELECT CAST(NEW.day AS TEXT) || '#' || CAST(id AS TEXT) FROM meal_plan WHERE day = NEW.day) AND type = 'mealplan-shadow');

	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = CAST(NEW.day AS TEXT) || '#' || CAST(meal_plan.id AS TEXT) AND type = 'mealplan-shadow'), recipe_id, recipe_servings
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;
END;

/*
Dummy update over all existing meal-plan recipe entries
to generate the new internal mealplan-shadow recipes
(by the trigger TEMPORARY_update_internal_recipe)
*/
UPDATE meal_plan
SET day = day
WHERE type = 'recipe'
	AND recipe_id IS NOT NULL;

DROP TRIGGER TEMPORARY_update_internal_recipe;

CREATE TRIGGER update_internal_recipe AFTER UPDATE ON meal_plan
BEGIN
	/* This contains practically the same logic as the trigger create_internal_recipe */

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
		OR recipe_id IN (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week');

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

	-- Add all products for this week as ingredients in the week-recipe
	INSERT INTO recipes_pos
		(recipe_id, product_id, amount, qu_id)
	SELECT (SELECT id FROM recipes WHERE name = LTRIM(STRFTIME('%Y-%W', NEW.day), '0') AND type = 'mealplan-week'), product_id, SUM(product_amount), product_qu_id
	FROM meal_plan
	WHERE STRFTIME('%Y-%W', day) = STRFTIME('%Y-%W', NEW.day)
		AND type = 'product'
		AND product_id IS NOT NULL
	GROUP BY product_id, product_qu_id;

	-- Create a shadow recipe per meal plan recipe
	DELETE FROM recipes_nestings
	WHERE recipe_id IN (SELECT id FROM recipes WHERE name IN (SELECT CAST(NEW.day AS TEXT) || '#' || CAST(NEW.id AS TEXT) FROM meal_plan WHERE day = NEW.day) AND type = 'mealplan-shadow');

	DELETE FROM recipes
	WHERE type = 'mealplan-shadow'
		AND name = CAST(NEW.day AS TEXT) || '#' || CAST(NEW.id AS TEXT);

	INSERT INTO recipes
		(id, name, type)
	SELECT (SELECT MIN(id) - 1 FROM recipes), CAST(NEW.day AS TEXT) || '#' || CAST(id AS TEXT), 'mealplan-shadow'
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;

	INSERT INTO recipes_nestings
		(recipe_id, includes_recipe_id, servings)
	SELECT (SELECT id FROM recipes WHERE name = CAST(NEW.day AS TEXT) || '#' || CAST(meal_plan.id AS TEXT) AND type = 'mealplan-shadow'), recipe_id, recipe_servings
	FROM meal_plan
	WHERE id = NEW.id
		AND type = 'recipe'
		AND recipe_id IS NOT NULL;

	-- Enforce "when empty then null" for certain columns
	UPDATE meal_plan
	SET recipe_id = NULL
	WHERE id = NEW.id
		AND IFNULL(recipe_id, '') = '';

	UPDATE meal_plan
	SET product_id = NULL
	WHERE id = NEW.id
		AND IFNULL(product_id, '') = '';

	UPDATE meal_plan
	SET product_qu_id = NULL
	WHERE id = NEW.id
		AND IFNULL(product_qu_id, '') = '';
END;
