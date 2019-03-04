<?php

namespace Grocy\Controllers;

use \Grocy\Services\CalendarService;
use \Grocy\Services\ApiKeyService;

class CalendarApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->CalendarService = new CalendarService();
		$this->ApiKeyService = new ApiKeyService();
	}

	protected $CalendarService;
	protected $ApiKeyService;

	public function Ical(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$vCalendar = new \Eluceo\iCal\Component\Calendar('grocy');

			$events = $this->CalendarService->GetEvents();
			foreach($events as $event)
			{
				$vEvent = new \Eluceo\iCal\Component\Event();
				$vEvent->setDtStart(new \DateTime($event['start']))
					->setDtEnd(new \DateTime($event['start']))
					->setSummary($event['title'])
					->setNoTime($event['date_format'] === 'date')
					->setUseUtc(false);
				
				$vCalendar->addComponent($vEvent);
			}

			$response->write($vCalendar->render());
			$response = $response->withHeader('Content-Type', 'text/calendar; charset=utf-8');
			return $response->withHeader('Content-Disposition', 'attachment; filename="grocy.ics"');
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function IcalSharingLink(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			return $this->ApiResponse(array(
				'url' => $this->AppContainer->UrlManager->ConstructUrl('/api/calendar/ical?secret=' . $this->ApiKeyService->GetOrCreateApiKey(ApiKeyService::API_KEY_TYPE_SPECIAL_PURPOSE_CALENDAR_ICAL))
			));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
