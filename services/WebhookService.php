<?php

namespace Grocy\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Get these from a database table later
const WEBHOOKS = [
	[
		'url' => GROCY_LABEL_PRINTER_WEBHOOK,
		'default_args' => GROCY_LABEL_PRINTER_PARAMS,
		'json' => GROCY_LABEL_PRINTER_HOOK_JSON,
		'include_events' => [
			WebhookService::EVENT_BATTERY_PRINT_LABEL,
			WebhookService::EVENT_CHORE_PRINT_LABEL,
			WebhookService::EVENT_RECIPE_PRINT_LABEL,
			WebhookService::EVENT_PRODUCT_PRINT_LABEL,
			WebhookService::EVENT_STOCK_ENTRY_PRINT_LABEL,
			WebhookService::EVENT_ADD_PRODUCT,
			WebhookService::EVENT_OPEN_PRODUCT,
			WebhookService::EVENT_TRANSFER_PRODUCT,
		],
		'exclude_events' => [],
		'enabled' => GROCY_FEATURE_FLAG_LABEL_PRINTER && GROCY_LABEL_PRINTER_RUN_SERVER,
	],
];

class WebhookService extends BaseService
{
	const EVENT_BATTERY_PRINT_LABEL = 'battery_print_label';
	const EVENT_CHORE_PRINT_LABEL = 'chore_print_label';
	const EVENT_RECIPE_PRINT_LABEL = 'recipe_print_label';
	const EVENT_PRODUCT_PRINT_LABEL = 'product_print_label';
	const EVENT_STOCK_ENTRY_PRINT_LABEL = 'stock_entry_print_label';
	const EVENT_ADD_PRODUCT = 'add_product';
	const EVENT_OPEN_PRODUCT = 'open_product';
	const EVENT_TRANSFER_PRODUCT = 'transfer_product';

	public function __construct()
	{
		$this->client = new Client(['timeout' => 2.0]);
	}

	private $client;

	private function shouldFire($webhook, $event)
	{
		if (!$webhook['enabled'])
		{
			return false;
		}

		$includeEvents = $webhook['include_events'] ?? [];
		$excludeEvents = $webhook['exclude_events'] ?? [];

		// No restrictions
		if (empty($includeEvents) && empty($excludeEvents))
		{
			return true;
		}

		// Only include events
		if (!empty($includeEvents) && in_array($event, $includeEvents))
		{
			return true;
		}

		// Only exclude events
		if (!empty($excludeEvents) && !in_array($event, $excludeEvents))
		{
			return true;
		}

		// No events match
		return false;
	}

	public function fire($event, $args)
	{
		foreach (WEBHOOKS as $webhook)
		{
			if ($this->shouldFire($webhook, $event))
			{
				$webhookData = array_merge($webhook['default_args'], $args);
				$this->run($webhook['url'], $webhookData, $webhook['json']);
			}
		}
	}

	private function run($url, $args, $json = false)
	{
		$reqArgs = [];
		if ($json)
		{
			$reqArgs = ['json' => $args];
		}
		else
		{
			$reqArgs = ['form_params' => $args];
		}
		try
		{
			file_put_contents('php://stderr', 'Running Webhook: ' . $url . "\n" . print_r($reqArgs, true));

			$this->client->request('POST', $url, $reqArgs);
		}
		catch (RequestException $e)
		{
			file_put_contents('php://stderr', 'Webhook failed: ' . $url . "\n" . $e->getMessage());
		}
	}
}
