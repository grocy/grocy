<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Grocy\Middleware\SessionAuthMiddleware;
use \Grocy\Services\ApplicationService;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/data/config.php';
require_once __DIR__ . '/extensions.php';

// Setup base application
if (PHP_SAPI !== 'cli')
{
	$appContainer = new \Slim\Container([
		'settings' => [
			'displayErrorDetails' => true,
			'determineRouteBeforeAppMiddleware' => true
		],
		'view' => function($container)
		{
			return new \Slim\Views\Blade(__DIR__ . '/views', __DIR__ . '/data/viewcache');
		}
	]);

	$app = new \Slim\App($appContainer);
}
else
{
	$app = new \Slim\App();
	$app->add(\pavlakis\cli\CliRequest::class);
}

// Add session handling if this is not a demo installation
$applicationService = new ApplicationService();
if (!$applicationService->IsDemoInstallation())
{
	$app->add(SessionAuthMiddleware::class);
}

require_once __DIR__ . '/routes.php';

$app->run();
