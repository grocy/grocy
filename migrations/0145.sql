ALTER TABLE userfields
ADD input_required TINYINT NOT NULL DEFAULT 0 CHECK(input_required IN (0, 1));
