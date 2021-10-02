<?php

// This is executed inside DatabaseMigrationService class/context

use Grocy\Services\StockService;

$PRODUCTS = [3, 4, 5, 6, 7, 8];

$i = 1;
$days = -1;
while ($i <= 500)
{
	$productId = $PRODUCTS[array_rand($PRODUCTS)];
	$transactionId1 = $this->getStockService()->AddProduct($productId, 1, date('Y-m-d', strtotime('+180 days')), StockService::TRANSACTION_TYPE_PURCHASE, date('Y-m-d', strtotime("$days days")), XRandomPrice());
	$transactionId2 = $this->getStockService()->ConsumeProduct($productId, 1, false, StockService::TRANSACTION_TYPE_CONSUME);

	$this->getDatabaseService()->ExecuteDbStatement("UPDATE stock_log SET row_created_timestamp = DATETIME(row_created_timestamp, '$days days') WHERE transaction_id = '$transactionId1'");
	$this->getDatabaseService()->ExecuteDbStatement("UPDATE stock_log SET row_created_timestamp = DATETIME(row_created_timestamp, '$days days') WHERE transaction_id = '$transactionId2'");

	$days--;
	$i++;
}

function XRandomPrice()
{
	return mt_rand(2 * 100, 25 * 100) / 100 / 4;
}
