<?php

class GrocyLogicStock
{
	const TRANSACTION_TYPE_PURCHASE = 'purchase';
	const TRANSACTION_TYPE_CONSUME = 'consume';
	const TRANSACTION_TYPE_INVENTORY_CORRECTION = 'inventory-correction';

	public static function GetCurrentStock()
	{
		$sql = 'SELECT * from stock_current';
		return Grocy::ExecuteDbQuery(Grocy::GetDbConnectionRaw(), $sql)->fetchAll(PDO::FETCH_OBJ);
	}

	public static function GetMissingProducts()
	{
		$sql = 'SELECT * from stock_missing_products';
		return Grocy::ExecuteDbQuery(Grocy::GetDbConnectionRaw(), $sql)->fetchAll(PDO::FETCH_OBJ);
	}

	public static function GetProductDetails(int $productId)
	{
		$db = Grocy::GetDbConnection();

		$product = $db->products($productId);
		$productStockAmount = $db->stock()->where('product_id', $productId)->sum('amount');
		$productLastPurchased = $db->stock()->where('product_id', $productId)->max('purchased_date');
		$productLastUsed = $db->stock_log()->where('product_id', $productId)->where('transaction_type', self::TRANSACTION_TYPE_CONSUME)->max('used_date');
		$quPurchase = $db->quantity_units($product->qu_id_purchase);
		$quStock = $db->quantity_units($product->qu_id_stock);

		return array(
			'product' => $product,
			'last_purchased' => $productLastPurchased,
			'last_used' => $productLastUsed,
			'stock_amount' => $productStockAmount,
			'quantity_unit_purchase' => $quPurchase,
			'quantity_unit_stock' => $quStock
		);
	}

	public static function AddProduct(int $productId, int $amount, string $bestBeforeDate, $transactionType)
	{
		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			$db = Grocy::GetDbConnection();
			$stockId = uniqid();

			$logRow = $db->stock_log()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => date('Y-m-d'),
				'stock_id' => $stockId,
				'transaction_type' => $transactionType
			));
			$logRow->save();

			$stockRow = $db->stock()->createRow(array(
				'product_id' => $productId,
				'amount' => $amount,
				'best_before_date' => $bestBeforeDate,
				'purchased_date' => date('Y-m-d'),
				'stock_id' => $stockId,
			));
			$stockRow->save();

			return true;
		}
		else
		{
			throw new Exception("Transaction type $transactionType is not valid (GrocyLogicStock.AddProduct)");
		}
	}

	public static function ConsumeProduct(int $productId, int $amount, bool $spoiled, $transactionType)
	{
		if ($transactionType === self::TRANSACTION_TYPE_CONSUME || $transactionType === self::TRANSACTION_TYPE_PURCHASE || $transactionType === self::TRANSACTION_TYPE_INVENTORY_CORRECTION)
		{
			$db = Grocy::GetDbConnection();

			$productStockAmount = $db->stock()->where('product_id', $productId)->sum('amount');
			$potentialStockEntries = $db->stock()->where('product_id', $productId)->orderBy('purchased_date', 'ASC')->fetchAll(); //FIFO

			if ($amount > $productStockAmount)
			{
				return false;
			}

			foreach ($potentialStockEntries as $stockEntry)
			{
				if ($amount == 0)
				{
					break;
				}

				if ($amount >= $stockEntry->amount) //Take the whole stock entry
				{
					$logRow = $db->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $stockEntry->amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType
					));
					$logRow->save();

					$amount -= $stockEntry->amount;
					$stockEntry->delete();
				}
				else //Stock entry amount is > than needed amount -> split the stock entry resp. update the amount
				{
					$logRow = $db->stock_log()->createRow(array(
						'product_id' => $stockEntry->product_id,
						'amount' => $amount * -1,
						'best_before_date' => $stockEntry->best_before_date,
						'purchased_date' => $stockEntry->purchased_date,
						'used_date' => date('Y-m-d'),
						'spoiled' => $spoiled,
						'stock_id' => $stockEntry->stock_id,
						'transaction_type' => $transactionType
					));
					$logRow->save();

					$restStockAmount = $stockEntry->amount - $amount;
					$amount = 0;

					$stockEntry->update(array(
						'amount' => $restStockAmount
					));
				}
			}

			return true;
		}
		else
		{
			throw new Exception("Transaction type $transactionType is not valid (GrocyLogicStock.ConsumeProduct)");
		}
	}

	public static function InventoryProduct(int $productId, int $newAmount, string $bestBeforeDate)
	{
		$db = Grocy::GetDbConnection();
		$productStockAmount = $db->stock()->where('product_id', $productId)->sum('amount');

		if ($newAmount > $productStockAmount)
		{
			$amountToAdd = $newAmount - $productStockAmount;
			self::AddProduct($productId, $amountToAdd, $bestBeforeDate, self::TRANSACTION_TYPE_INVENTORY_CORRECTION);
		}
		else if ($newAmount < $productStockAmount)
		{
			$amountToRemove = $productStockAmount - $newAmount;
			self::ConsumeProduct($productId, $amountToRemove, false, self::TRANSACTION_TYPE_INVENTORY_CORRECTION);
		}

		return true;
	}
}
