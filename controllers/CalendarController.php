<?php

namespace Grocy\Controllers;

use \Grocy\Services\CalendarService;

class CalendarController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->CalendarService = new CalendarService();
	}

	protected $CalendarService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->AppContainer->view->render($response, 'calendar', [
			'fullcalendarEventSources' => $this->CalendarService->GetEvents()
		]);
	}
}
