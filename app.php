<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Grocy\Helpers\UrlManager;
use \Grocy\Controllers\LoginController;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/data/config.php';

// Setup base application
$appContainer = new \Slim\Container([
	'settings' => [
		'displayErrorDetails' => true,
		'determineRouteBeforeAppMiddleware' => true
	],
	'view' => function($container)
	{
		return new \Slim\Views\Blade(__DIR__ . '/views', __DIR__ . '/data/viewcache');
	},
	'LoginControllerInstance' => function($container)
	{
		return new LoginController($container, 'grocy_session');
	},
	'UrlManager' => function($container)
	{
		return new UrlManager(BASE_URL);
	}
]);
$app = new \Slim\App($appContainer);

if (PHP_SAPI === 'cli')
{
	$app->add(\pavlakis\cli\CliRequest::class);
}

require_once __DIR__ . '/routes.php';

$app->run();
