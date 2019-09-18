ALTER TABLE chores
ADD consume_product_on_execution TINYINT NOT NULL DEFAULT 0;

ALTER TABLE chores
ADD product_id TINYINT;

ALTER TABLE chores
ADD product_amount REAL;
