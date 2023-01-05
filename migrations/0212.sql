ALTER TABLE products
ADD auto_reprint_stock_label TINYINT NOT NULL DEFAULT 0 CHECK(auto_reprint_stock_label IN (0, 1));
