DELETE FROM shopping_list
WHERE shopping_list_id NOT IN (SELECT id FROM shopping_lists);

CREATE TRIGGER remove_items_from_deleted_shopping_list AFTER DELETE ON shopping_lists
BEGIN
    DELETE FROM shopping_list WHERE shopping_list_id = OLD.id;
END;

CREATE TRIGGER prevent_infinite_nested_recipes_INS BEFORE INSERT ON recipes_nestings
BEGIN
    SELECT CASE WHEN((
        SELECT 1
        FROM recipes_nestings_resolved rnr
        WHERE NEW.recipe_id = rnr.includes_recipe_id
            AND NEW.includes_recipe_id = rnr.recipe_id
    ) NOTNULL) THEN RAISE(ABORT, "Recursive nested recipe detected") END;
END;

CREATE TRIGGER prevent_infinite_nested_recipes_UPD BEFORE UPDATE ON recipes_nestings
BEGIN
    SELECT CASE WHEN((
        SELECT 1
        FROM recipes_nestings_resolved rnr
        WHERE NEW.recipe_id = rnr.includes_recipe_id
            AND NEW.includes_recipe_id = rnr.recipe_id
    ) NOTNULL) THEN RAISE(ABORT, "Recursive nested recipe detected") END;
END;
