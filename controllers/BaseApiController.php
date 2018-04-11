<?php

namespace Grocy\Controllers;

class BaseApiController extends BaseController
{
	protected function ApiEncode($response)
	{
		return json_encode($response);
	}
}
