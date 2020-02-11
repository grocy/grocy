<?php

namespace Grocy\Controllers;

use \Grocy\Services\CalendarService;

class CalendarController extends BaseController
{
	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
		$this->CalendarService = new CalendarService();
	}

	protected $CalendarService;

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->View->render($response, 'calendar', [
			'fullcalendarEventSources' => $this->CalendarService->GetEvents()
		]);
	}
}
