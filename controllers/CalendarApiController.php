<?php

namespace Grocy\Controllers;

class CalendarApiController extends BaseApiController
{
	public function Ical(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			$vCalendar = new \Eluceo\iCal\Component\Calendar('grocy');

			$events = $this->getCalendarService()->GetEvents();

			foreach ($events as $event)
			{
				$date = new \DateTime($event['start']);
				$date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

				if ($event['date_format'] === 'date')
				{
					$date->setTime(23, 59, 59);
				}

				$description = '';
				if (isset($event['description']))
				{
					$description = $event['description'];
				}

				$vEvent = new \Eluceo\iCal\Component\Event();
				$vEvent->setDtStart($date)
					->setDtEnd($date)
					->setSummary($event['title'])
					->setDescription($description)
					->setNoTime($event['date_format'] === 'date' || (isset($event['allDay']) && $event['allDay']))
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

	public function IcalSharingLink(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		try
		{
			return $this->ApiResponse($response, [
				'url' => $this->AppContainer->get('UrlManager')->ConstructUrl('/api/calendar/ical?secret=' . $this->getApiKeyService()->GetOrCreateApiKey(\Grocy\Services\ApiKeyService::API_KEY_TYPE_SPECIAL_PURPOSE_CALENDAR_ICAL))
			]);
		}
		catch (\Exception $ex)
		{
			return $this->GenericErrorResponse($response, $ex->getMessage());
		}
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
