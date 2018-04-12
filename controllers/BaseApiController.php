<?php

namespace Grocy\Controllers;

class BaseApiController extends BaseController
{
	protected function ApiResponse($response)
	{
		return json_encode($response);
	}
}
