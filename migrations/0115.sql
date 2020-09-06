ALTER TABLE stock_log ADD COLUMN
    user_id INTEGER NOT NULL DEFAULT 1;

CREATE VIEW uihelper_stock_journal
AS
SELECT stock_log.id,
       stock_log.row_created_timestamp,
       stock_log.correlation_id,
       stock_log.undone,
       stock_log.undone_timestamp,
       stock_log.row_created_timestamp,
       stock_log.transaction_type,
       stock_log.spoiled,
       stock_log.amount,
       stock_log.location_id,
       l.name         AS location_name,
       p.name         AS product_name,
       qu.name        AS qu_name,
       qu.name_plural AS qu_name_plural,
       u.display_name AS user_display_name

FROM stock_log
         JOIN users_dto u on stock_log.user_id = u.id
         JOIN products p on stock_log.product_id = p.id
         JOIN locations l on p.location_id = l.id
         JOIN quantity_units qu ON p.qu_id_stock = qu.id;


CREATE VIEW uihelper_stock_journal_summary
AS
SELECT user_id AS id, -- dummy, LessQL needs an id column
       user_id, u.display_name AS user_display_name, p.name AS product_name, product_id, transaction_type,
       qu.name        AS qu_name,
       qu.name_plural AS qu_name_plural,
       SUM(amount) AS amount
FROM stock_log
    JOIN users_dto u on stock_log.user_id = u.id
    JOIN products p on stock_log.product_id = p.id
    JOIN quantity_units qu ON p.qu_id_stock = qu.id
WHERE undone = 0
    GROUP BY user_id, product_id,transaction_type;
