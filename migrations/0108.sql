DELETE
FROM shopping_list
WHERE shopping_list_id NOT IN (SELECT id FROM shopping_lists);

CREATE TRIGGER remove_items_from_deleted_shopping_list
    AFTER DELETE
    ON shopping_lists
BEGIN
    DELETE FROM shopping_list WHERE shopping_list_id = OLD.id;
END;