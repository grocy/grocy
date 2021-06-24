<?php

namespace Grocy\Controllers;

class CalendarController extends BaseController
{
	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($request, $response, 'calendar', [
			'fullcalendarEventSources' => $this->getCalendarService()->GetEvents()
		]);
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
