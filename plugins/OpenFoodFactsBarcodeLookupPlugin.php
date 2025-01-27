<?php

use Grocy\Helpers\BaseBarcodeLookupPlugin;
use GuzzleHttp\Client;

/*
	To use this plugin, configure it in data/config.php like this:
	Setting('STOCK_BARCODE_LOOKUP_PLUGIN', 'OpenFoodFactsBarcodeLookupPlugin');
*/

class OpenFoodFactsBarcodeLookupPlugin extends BaseBarcodeLookupPlugin
{
	public const PLUGIN_NAME = 'Open Food Facts';

	protected function ExecuteLookup($barcode)
	{
		$webClient = new Client(['http_errors' => false]);
		$response = $webClient->request('GET', 'https://world.openfoodfacts.org/api/v2/product/' . preg_replace('/[^0-9]/', '', $barcode) . '?fields=product_name,image_url', ['headers' => ['User-Agent' => 'GrocyOpenFoodFactsBarcodeLookupPlugin/1.0 (https://grocy.info)']]);
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

			// Take the preset user setting or otherwise simply the first existing location
			$locationId = $this->Locations[0]->id;
			if ($this->UserSettings['product_presets_location_id'] != -1)
			{
				$locationId = $this->UserSettings['product_presets_location_id'];
			}

			// Take the preset user setting or otherwise simply the first existing quantity unit
			$quId = $this->QuantityUnits[0]->id;
			if ($this->UserSettings['product_presets_qu_id'] != -1)
			{
				$quId = $this->UserSettings['product_presets_qu_id'];
			}

			return [
				'name' => $data->product->product_name,
				'location_id' => $locationId,
				'qu_id_purchase' => $quId,
				'qu_id_stock' => $quId,
				'__qu_factor_purchase_to_stock' => 1,
				'__barcode' => $barcode,
				'__image_url' => $imageUrl
			];
		}
	}
}
