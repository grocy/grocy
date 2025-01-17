UPDATE user_settings
SET value = '10'
WHERE key IN ('stock_decimal_places_amounts', 'stock_decimal_places_prices_input', 'stock_decimal_places_prices_display')
	AND CAST(value AS INT) > 10;
