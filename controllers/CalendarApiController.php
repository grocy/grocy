<?php

namespace Grocy\Controllers;

use \Grocy\Services\CalendarService;
use \Grocy\Services\ApiKeyService;

class CalendarApiController extends BaseApiController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
	}

	protected $CalendarService = null;

	protected function getCalendarService()
	{
		if($this->CalendarService == null)
		{
			$this->CalendarService = new CalendarService();
		}
		return $this->CalendarService;
	}

	protected $ApiKeyService = null;

	protected function getApiKeyService()
	{
		if($this->ApiKeyService == null)
		{
			$this->ApiKeyService = new ApiKeyService();
		}
		return $this->ApiKeyService;
	}

	public function Ical(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		try
		{
			$vCalendar = new \Eluceo\iCal\Component\Calendar('grocy');

			$events = $this->getCalendarService()->GetEvents();
			foreach($events as $event)
			{
				$date = new \DateTime($event['start']);
				$date->setTimezone(date_default_timezone_get());

				if ($event['date_format'] === 'date')
				{
					$date->setTime(23, 59, 59);
				}

				$vEvent = new \Eluceo\iCal\Component\Event();
				$vEvent->setDtStart($date)
					->setDtEnd($date)
					->setSummary($event['title'])
					->setDescription($event['description'])
					->setNoTime($event['date_format'] === 'date')
					->setUseTimezone(true);

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
				'url' => $this->AppContainer->UrlManager->ConstructUrl('/api/calendar/ical?secret=' . $this->getApiKeyService()->GetOrCreateApiKey(ApiKeyService::API_KEY_TYPE_SPECIAL_PURPOSE_CALENDAR_ICAL))
			));
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}
}
