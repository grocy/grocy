UPDATE user_settings
SET key = 'stock_decimal_places_prices_input'
WHERE key = 'stock_decimal_places_prices';

INSERT INTO user_settings
	(user_id, key, value)
SELECT user_id, 'stock_decimal_places_prices_display', value
FROM user_settings
WHERE key = 'stock_decimal_places_prices_input';
