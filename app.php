<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Grocy\Helpers\UrlManager;
use \Grocy\Controllers\LoginController;

// Definitions for embedded mode
if (file_exists(__DIR__ . '/embedded.txt'))
{
	define('GROCY_IS_EMBEDDED_INSTALL', true);
	define('GROCY_DATAPATH', file_get_contents(__DIR__ . '/embedded.txt'));
	define('GROCY_USER_ID', 1);
}
else
{
	define('GROCY_IS_EMBEDDED_INSTALL', false);
	define('GROCY_DATAPATH', __DIR__ . '/data');
}

// Definitions for demo mode
if (file_exists(GROCY_DATAPATH . '/demo.txt'))
{
	define('GROCY_IS_DEMO_INSTALL', true);
	if (!defined('GROCY_USER_ID'))
	{
		define('GROCY_USER_ID', 1);
	}
}
else
{
	define('GROCY_IS_DEMO_INSTALL', false);
}

// Load composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Load config files
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/config-dist.php'; //For not in own config defined values we use the default ones

// Definitions for disabled authentication mode
if (GROCY_DISABLE_AUTH === true)
{
	if (!defined('GROCY_USER_ID'))
	{
		define('GROCY_USER_ID', 1);
	}
}

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

// Load routes from separate file
require_once __DIR__ . '/routes.php';

$app->run();
