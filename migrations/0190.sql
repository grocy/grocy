ALTER TABLE products
ADD move_on_open TINYINT NOT NULL DEFAULT 0 CHECK(move_on_open IN (0, 1));
