<?php

namespace Grocy\Helpers;

class KrogerToGrocyConverter
{
	// Fields returned: 'name', 'location_id', 'qu_id_purchase', 'qu_id_stock',
	// 'qu_factor_purchase_to_stock', 'barcode', 'default_best_before_days'
	// 'quantity', 'transaction_date', 'price_paid'
	public static function ConvertJson($data, $default_quantity_units, $default_location_id) 
	{
		if (array_key_exists("data", $data))
		{
			$data = $data['data'];
		}
		
		$products = array();
		foreach ($data as &$receipt) 
		{
			if (!array_key_exists("items", $receipt))
			{
				continue;
			}

			$dateArray = date_parse($receipt['transactionTime']) ?: getdate();
			$transactionDate = sprintf("%04d-%02d-%02d", $dateArray["year"], $dateArray["month"], $dateArray["day"]);
			foreach ($receipt['items'] as &$item)
			{
				if (!array_key_exists("detail", $item))
				{
					continue;
				}

				$product = null;
				$barcode = KrogerToGrocyConverter::ConvertUpcToBarcode($item["baseUpc"]);

				$product = array(
					'name' => $item["detail"]["description"],
					'location_id' => $default_location_id,
					'qu_id_purchase' => $default_quantity_units,
					'qu_id_stock' => $default_quantity_units,
					'qu_factor_purchase_to_stock' => 1,
					'barcode' => $barcode,
					'default_best_before_days' => 21,
					'quantity' => $item['quantity'],
					'transaction_date' => $transactionDate,
					'price_paid' => $item['pricePaid']
				);

				array_push($products, $product);
			}
		}
		return $products;
	}
	
	// Anatomy of a barcode: 0		07222526014		 2
	//						 ^			 ^			 ^
	//				   country code		upc	 computed check digit
	public static function ConvertUpcToBarcode($upc)
	{
		$upclen = strlen($upc);
		if ($upclen < 11 && intval($upc) > 0) 
		{
			throw new \Exception('UPC code should be at least 11 digits');
		}

		$countryCode = $upclen > 11 ? $upc[$upclen - 12] : 0 /*US/Canda*/;
		$upc = substr($upc, $upclen - 11);

		$checkSum = (intval($upc[0]) + intval($upc[2]) + intval($upc[4]) + intval($upc[6]) + intval($upc[8]) + $upc[10]) * 3;
		$checkSum = $checkSum + intval($upc[1]) + intval($upc[3]) + intval($upc[5]) + intval($upc[7]) + intval($upc[9]);
		
		$checkDigit = 10 - ($checkSum % 10);
		
		return $countryCode . $upc . strval($checkDigit);
	}
}

?>