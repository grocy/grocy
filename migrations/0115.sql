ALTER TABLE stock_log
    RENAME TO stock_log_old;

CREATE TABLE stock_log(
    id                          INTEGER        not null        primary key autoincrement unique,
    product_id                  INTEGER        not null,
    amount                      DECIMAL(15, 2) not null,
    best_before_date            DATE,
    purchased_date              DATE,
    used_date                   DATE,
    spoiled                     INTEGER  default 0 not null,
    stock_id                    TEXT           not null,
    transaction_type            TEXT           not null,
    price                       DECIMAL(15, 2),
    undone                      TINYINT  default 0 not null     CHECK(undone IN (0, 1)),
    undone_timestamp            DATETIME,
    opened_date                 DATETIME,
    row_created_timestamp       DATETIME DEFAULT (datetime('now', 'localtime')),
    location_id                 INTEGER,
    recipe_id                   INTEGER,
    correlation_id              TEXT,
    transaction_id              TEXT,
    stock_row_id                INTEGER,
    shopping_location_id        INTEGER,
    qu_factor_purchase_to_stock REAL     default 1.0 not null,
    user_id                     INTEGER        NOT NULL
                      );


INSERT INTO stock_log
(product_id, amount, best_before_date, purchased_date, used_date, spoiled, stock_id, transaction_type, price, undone,
 undone_timestamp, opened_date, row_created_timestamp, user_id)
SELECT product_id,
       amount,
       best_before_date,
       purchased_date,
       used_date,
       spoiled,
       stock_id,
       transaction_type,
       price,
       undone,
       undone_timestamp,
       opened_date,
       row_created_timestamp,
       (SELECT id FROM users WHERE username = 'admin')
FROM stock_log_old;

DROP TABLE stock_log_old;


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
