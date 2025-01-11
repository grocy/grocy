<?php

use Grocy\Helpers\BaseBarcodeLookupPlugin;
use GuzzleHttp\Client;

/*
	To use this plugin, configure it in data/config.php like this:
	Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'OpenFoodFactsBarcodeLookupPlugin');
*/

class OpenFoodFactsBarcodeLookupPlugin extends BaseBarcodeLookupPlugin
{
	protected function ExecuteLookup($barcode)
	{
		$webClient = new Client(['http_errors' => false]);
		$response = $webClient->request('GET', "https://world.openfoodfacts.net/api/v2/product/$barcode?fields=product_name,image_url");
		$statusCode = $response->getStatusCode();

		// Guzzle throws exceptions for connection errors, so nothing to do on that here

		$data = json_decode($response->getBody());
		if ($statusCode == 404 || $data->status != 1)
		{
			// Nothing found for the given barcode
			return null;
		}
		else
		{
			$imageUrl = '';
			if (isset($data->product->image_url) && !empty($data->product->image_url))
			{
				$imageUrl = $data->product->image_url;
			}

			return [
				'name' => $data->product->product_name,
				'location_id' => $this->Locations[0]->id,
				'qu_id_purchase' => $this->QuantityUnits[0]->id,
				'qu_id_stock' => $this->QuantityUnits[0]->id,
				'qu_factor_purchase_to_stock' => 1,
				'barcode' => $barcode,
				'image_url' => $imageUrl
			];
		}
	}
}
