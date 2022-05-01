<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CalendarController extends BaseController
{
	public function Overview(ServerRequestInterface $request, ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'calendar', [
			'fullcalendarEventSources' => $this->getCalendarService()->GetEvents()
		]);
	}
}
