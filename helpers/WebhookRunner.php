<?php

namespace Grocy\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\ExceptionRequestException;
use Psr\Http\Message\ResponseInterface;

class WebhookRunner
{
	public function __construct()
	{
		$this->client = new Client(['timeout' => 2.0]);
	}

	private $client;

	public function run($url, $args, $json = false)
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

	public function runAll($urls, $args)
	{
		foreach ($urls as $url)
		{
			$this->run($url, $args);
		}
	}
}
