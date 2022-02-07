<?php

// This is executed inside DatabaseMigrationService class/context

// Migrate the old config.php setting FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT
// to the new product option treat_opened_as_out_of_stock
// New and old default was/is enabled, so only disable it for all existing products when it was disabled

if (!defined('GROCY_FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT'))
{
	define('GROCY_FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT', true);
}

if (!GROCY_FEATURE_SETTING_STOCK_COUNT_OPENED_PRODUCTS_AGAINST_MINIMUM_STOCK_AMOUNT)
{
	$this->getDatabaseService()->ExecuteDbStatement('UPDATE products SET treat_opened_as_out_of_stock = 0');
	$this->getDatabaseService()->ExecuteDbStatement("INSERT INTO user_settings (user_id, key, value) SELECT id, 'product_presets_treat_opened_as_out_of_stock', '0' FROM users");
}
