<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Grocy\Helpers\UrlManager;
use \Grocy\Controllers\LoginController;

if (file_exists(__DIR__ . '/embedded.txt'))
{
	define('GROCY_DATAPATH', file_get_contents(__DIR__ . '/embedded.txt'));
}
else
{
	define('GROCY_DATAPATH', __DIR__ . '/data');
}

require_once __DIR__ . '/vendor/autoload.php';
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/config-dist.php'; //For not in own config defined values we use the default ones

// Setup base application
$appContainer = new \Slim\Container([
	'settings' => [
		'displayErrorDetails' => true,
		'determineRouteBeforeAppMiddleware' => true
	],
	'view' => function($container)
	{
		return new \Slim\Views\Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache');
	},
	'LoginControllerInstance' => function($container)
	{
		return new LoginController($container, 'grocy_session');
	},
	'UrlManager' => function($container)
	{
		return new UrlManager(GROCY_BASE_URL);
	},
	'ApiKeyHeaderName' => function($container)
	{
		return 'GROCY-API-KEY';
	}
]);
$app = new \Slim\App($appContainer);

if (PHP_SAPI === 'cli')
{
	$app->add(\pavlakis\cli\CliRequest::class);
}

require_once __DIR__ . '/routes.php';

$app->run();
