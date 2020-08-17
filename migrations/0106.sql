DROP INDEX IF EXISTS _index_products_parents;
DROP INDEX IF EXISTS _index_stock_products_amount;

CREATE INDEX _index_products_parents ON products (parent_product_id);
CREATE INDEX _index_stock_products_amount ON stock (product_id, open, best_before_date, amount);


DROP INDEX IF EXISTS _index_product_parents_2;
CREATE INDEX _index_product_parents_2
    ON products (
                 /* see products_resolved */
                 CASE WHEN parent_product_id IS NULL THEN id ELSE parent_product_id END,
                 active
        );

/*
Old version is in 0103.sql
*/
DROP VIEW products_resolved;
CREATE VIEW products_resolved
AS
SELECT CASE
           WHEN p.parent_product_id IS NULL
               THEN p.id
           ELSE
               p.parent_product_id
           END
            AS parent_product_id,
       p.id as sub_product_id
FROM products p WHERE active = 1;

