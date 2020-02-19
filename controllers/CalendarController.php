<?php

namespace Grocy\Controllers;

class CalendarController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'calendar', [
			'fullcalendarEventSources' => $this->getCalendarService()->GetEvents()
		]);
	}
}
