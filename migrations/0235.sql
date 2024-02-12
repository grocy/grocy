CREATE VIEW shopping_lists_view
AS
SELECT
	*,
	(SELECT IFNULL(COUNT(*), 0) FROM shopping_list WHERE shopping_list_id = sl.id) AS item_count
FROM shopping_lists sl;
