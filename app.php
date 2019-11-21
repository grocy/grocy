<?php
$fp = fopen('/config/data/sql.log', 'a');
fwrite($fp, "!!!App starting up loading\n");
$time_start = microtime(true);

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
require_once __DIR__ . '/config-dist.php'; // For not in own config defined values we use the default ones

// Definitions for disabled authentication mode
if (GROCY_DISABLE_AUTH === true)
{
	if (!defined('GROCY_USER_ID'))
	{
		define('GROCY_USER_ID', 1);
	}
}
fwrite($fp, "!!!App - dep load time : " . round((microtime(true) - $time_start),6) . "\n");


// Setup base application
$appContainer = new \Slim\Container([
	'settings' => [
		'displayErrorDetails' => true,
		'determineRouteBeforeAppMiddleware' => true
	],
	'view' => function($container)
	{
		$view_time_start = microtime(true);
		#$view = new \Slim\Views\Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache');
        fwrite($fp, "!!!App - view load time : " . round((microtime(true) - $view_time_start),6) . "\n");
		if (!apcu_exists("views"))
		{
			apcu_store("views", new \Slim\Views\Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache'));
		}

		$view = apcu_fetch("views");
        fwrite($fp, "!!!App - view load time : " . round((microtime(true) - $view_time_start),6) . "\n");
        return $view;
	},
	'LoginControllerInstance' => function($container)
	{
		return new LoginController($container, 'grocy_session');
	},
	'UrlManager' => function($container)
	{
		if (!apcu_exists("UrlManager"))
		{
			apcu_store("UrlManager", new UrlManager(GROCY_BASE_URL));
		}

		return apcu_fetch("UrlManager");
	},
	'ApiKeyHeaderName' => function($container)
	{
		return 'GROCY-API-KEY';
	}
]);

$app = new \Slim\App($appContainer);

#$fp = fopen('/www/data/sql.log', 'a');
#fwrite($fp, "!!!Starting up loading app\n");
#fwrite($fp, "!!!".print_r(ini_get_all(),True)."\n");
#fwrite($fp, "!!!".print_r(opcache_get_status(),True)."\n");
#fclose($fp);

#phpinfo();

// Load routes from separate file
require_once __DIR__ . '/routes.php';

$fp = fopen('/config/data/sql.log', 'a');
fwrite($fp, "!!!App starting run\n");
$run_time_start = microtime(true);
$app->run();
fwrite($fp, "!!!App - Total run time in seconds: " . round((microtime(true) - $run_time_start),6) . "\n");
fwrite($fp, "!!!App - Total execution time in seconds: " . round((microtime(true) - $time_start),6) . "\n");
#fwrite($fp, "!!!APP - ini: ".print_r(ini_get_all(),TRUE)."\n");
#fwrite($fp, "!!!APP - opcache status: ".print_r(opcache_get_status(),TRUE)."\n");
#fwrite($fp, "!!!APP - opcache config: ".print_r(opcache_get_configuration(),TRUE)."\n");
fclose($fp);
