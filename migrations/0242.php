<?php

// This is executed inside DatabaseMigrationService class/context

// Assign a new stock_id to all opened stock entries where there is also an unopened one with the same stock_id
$db = $this->getDatabaseService();

$sql = 'SELECT s1.id
FROM stock s1
WHERE IFNULL(s1.open, 0) = 1
 AND EXISTS (
	SELECT 1
	FROM stock s2
	WHERE s2.stock_id = s1.stock_id
		AND IFNULL(s2.open, 0) = 0
	)';

$rows = $db->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
foreach ($rows as $row)
{
	$db->ExecuteDbStatement('UPDATE stock SET stock_id = \'' . uniqid() . '\' WHERE id = ' . $row->id);
}
