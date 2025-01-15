<?php

namespace Grocy\Controllers;

use Grocy\Controllers\Users\User;
use Grocy\Helpers\Grocycode;
use Grocy\Services\WebhookService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class BatteriesApiController extends BaseApiController
{
	public function BatteryDetails(Request $request, Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, $this->getBatteriesService()->GetBatteryDetails($args['batteryId']));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function Current(Request $request, Response $response, array $args)
	{
		return $this->FilteredApiResponse($response, $this->getBatteriesService()->GetCurrent(), $request->getQueryParams());
	}

	public function TrackChargeCycle(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_BATTERIES_TRACK_CHARGE_CYCLE);

		$requestBody = $this->GetParsedAndFilteredRequestBody($request);

		try
		{
			$trackedTime = date('Y-m-d H:i:s');
			if (array_key_exists('tracked_time', $requestBody) && IsIsoDateTime($requestBody['tracked_time']))
			{
				$trackedTime = $requestBody['tracked_time'];
			}

			$chargeCycleId = $this->getBatteriesService()->TrackChargeCycle($args['batteryId'], $trackedTime);
			return $this->ApiResponse($response, $this->getDatabase()->battery_charge_cycles($chargeCycleId));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function UndoChargeCycle(Request $request, Response $response, array $args)
	{
		User::checkPermission($request, User::PERMISSION_BATTERIES_UNDO_CHARGE_CYCLE);

		try
		{
			$this->ApiResponse($response, $this->getBatteriesService()->UndoChargeCycle($args['chargeCycleId']));
			return $this->EmptyApiResponse($response);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function BatteryPrintLabel(Request $request, Response $response, array $args)
	{
		try
		{
			$battery = $this->getDatabase()->batteries()->where('id', $args['batteryId'])->fetch();

			$webhookData = [
				'battery' => $battery->name,
				'grocycode' => (string)(new Grocycode(Grocycode::BATTERY, $args['batteryId'])),
			];

			$this->getWebhookService()->run(WebhookService::EVENT_BATTERY_PRINT_LABEL, $webhookData);

			return $this->ApiResponse($response, $webhookData);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
