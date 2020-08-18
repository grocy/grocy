CREATE INDEX ix_products_performance1 ON products (
    parent_product_id
);

CREATE INDEX ix_products_performance2 ON products (
    CASE WHEN parent_product_id IS NULL THEN id ELSE parent_product_id END,
    active
);

CREATE INDEX ix_stock_performance1 ON stock (
    product_id,
    open,
    best_before_date,
    amount
);

DROP VIEW products_resolved;
CREATE VIEW products_resolved
AS
SELECT
    CASE
        WHEN p.parent_product_id IS NULL THEN
            p.id
        ELSE
            p.parent_product_id
    END AS parent_product_id,
    p.id as sub_product_id
FROM products p
WHERE p.active = 1;
