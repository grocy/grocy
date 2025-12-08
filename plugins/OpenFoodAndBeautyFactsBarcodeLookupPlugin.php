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
        // Function to perform the actual lookup
        function performLookup($apiUrl, $barcode, $productNameFieldLocalized, $webClient)
        {
            // Make the API request
            $response = $webClient->request(
                'GET',
                $apiUrl . preg_replace('/[^0-9]/', '', $barcode) . '?fields=product_name,image_url,' . $productNameFieldLocalized,
                ['headers' => ['User-Agent' => 'GrocyOpenFoodFactsBarcodeLookupPlugin/1.0 (https://grocy.info)']]
            );

            // Parse the response
            $statusCode = $response->getStatusCode();
            $data = json_decode($response->getBody());

            // Check if the product was not found
            if ($statusCode == 404 || $data->status != 1)
            {
                return null;
            }

            // Return the response data
            return $data;
        }

        $productNameFieldLocalized = 'product_name_' . substr(GROCY_LOCALE, 0, 2);
        $webClient = new Client(['http_errors' => false]);

        // First lookup on Open Food Facts
        $data = performLookup('https://world.openfoodfacts.org/api/v2/product/', $barcode, $productNameFieldLocalized, $webClient);

        // If the first lookup fails, try the Open Beauty Facts
        if ($data === null)
        {
            $data = performLookup('https://world.openbeautyfacts.org/api/v2/product/', $barcode, $productNameFieldLocalized, $webClient);

            // If the second lookup fails too, return null
            if ($data === null)
            {
                return null;
            }
        }

        // Initialize an empty image URL
        $imageUrl = '';
        if (isset($data->product->image_url) && !empty($data->product->image_url))
        {
            $imageUrl = $data->product->image_url;
        }

        // Retrieve the location ID from user settings or set to the first location in the list
        $locationId = $this->Locations[0]->id;
        if ($this->UserSettings['product_presets_location_id'] != -1)
        {
            $locationId = $this->UserSettings['product_presets_location_id'];
        }

        // Retrieve the quantity unit ID from user settings or set to the first quantity unit in the list
        $quId = $this->QuantityUnits[0]->id;
        if ($this->UserSettings['product_presets_qu_id'] != -1)
        {
            $quId = $this->UserSettings['product_presets_qu_id'];
        }

        // Use the localized product name if provided
        $name = $data->product->product_name;
        if (isset($data->product->$productNameFieldLocalized) && !empty($data->product->$productNameFieldLocalized))
        {
            $name = $data->product->$productNameFieldLocalized;
        }

        // Remove non-ASCII characters from the product name
        $name = preg_replace('/[^a-zA-Z0-9äöüÄÖÜß ]/', '', $name);

        // Return the structured product data
        return [
            'name' => $name,
            'location_id' => $locationId,
            'qu_id_purchase' => $quId,
            'qu_id_stock' => $quId,
            '__qu_factor_purchase_to_stock' => 1,
            '__barcode' => $barcode,
            '__image_url' => $imageUrl
        ];
    }
}