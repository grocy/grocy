ALTER TABLE chores_log
ADD scheduled_execution_time DATETIME;

DELETE FROM user_settings
WHERE key IN (
	'datatables_state_chores-journal-table',
	'datatables_rowGroup_chores-journal-table',
	'datatables_state_products-table',
	'datatables_rowGroup_products-table'
);
