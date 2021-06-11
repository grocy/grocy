-- Duplicate product barcodes were most probably not created on purpose,
-- so just keep the newer one for any duplicates
DELETE FROM product_barcodes
WHERE id IN (
	SELECT MIN(id)
	FROM product_barcodes
	GROUP BY barcode
	HAVING COUNT(*) > 1
);

CREATE UNIQUE INDEX ix_product_barcodes ON product_barcodes (
	barcode
);
