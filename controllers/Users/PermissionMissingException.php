<?php

namespace Grocy\Controllers\Users;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpForbiddenException;
use Throwable;

class PermissionMissingException extends HttpForbiddenException
{
	public function __construct(ServerRequestInterface $request, string $permission, ?Throwable $previous = null)
	{
		parent::__construct($request, 'Permission missing: ' . $permission, $previous);
	}
}
