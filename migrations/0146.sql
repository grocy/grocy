ALTER TABLE products
ADD should_not_be_frozen TINYINT NOT NULL DEFAULT 0 CHECK(should_not_be_frozen IN (0, 1));
