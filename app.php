<?php

use Grocy\Controllers\LoginController;
use Grocy\Helpers\UrlManager;
use Grocy\Middleware\CorsMiddleware;
use Psr\Container\ContainerInterface as Container;
use Slim\Factory\AppFactory;

// Load composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Load config files
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/config-dist.php'; // For not in own config defined values we use the default ones
require_once __DIR__ . '/helpers/ConfigurationValidator.php';

// Definitions for dev/demo/prerelease mode
if ((GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease') && !defined('GROCY_USER_ID'))
{
	define('GROCY_USER_ID', 1);
	define('GROCY_SHOW_AUTH_VIEWS', true);
}

// Definitions for disabled authentication mode
if (GROCY_DISABLE_AUTH === true)
{
	if (!defined('GROCY_USER_ID'))
	{
		define('GROCY_USER_ID', 1);
	}

	define('GROCY_SHOW_AUTH_VIEWS', false);
}

// Check if any invalid entries in config.php have been made
try
{
	(new ConfigurationValidator())->validateConfig();
}
catch (EInvalidConfig $ex)
{
	exit('Invalid setting in config.php: ' . $ex->getMessage());
}

// Create data/viewcache folder if it doesn't exist
if (!file_exists(GROCY_DATAPATH . '/viewcache'))
{
	mkdir(GROCY_DATAPATH . '/viewcache');
}

// Setup base application
AppFactory::setContainer(new DI\Container());
$app = AppFactory::create();

$container = $app->getContainer();
$container->set('view', function (Container $container) {
	return new Slim\Views\Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache');
});
$container->set('UrlManager', function (Container $container) {
	return new UrlManager(GROCY_BASE_URL);
});
$container->set('ApiKeyHeaderName', function (Container $container) {
	return 'GROCY-API-KEY';
});

// Load routes from separate file
require_once __DIR__ . '/routes.php';

// Set base path if defined
if (!empty(GROCY_BASE_PATH))
{
	$app->setBasePath(GROCY_BASE_PATH);
}

if (GROCY_MODE === 'production' || GROCY_MODE === 'dev')
{
	$app->add(new \Grocy\Middleware\LocaleMiddleware($container));
}
else
{
	define('GROCY_LOCALE', GROCY_DEFAULT_LOCALE);
}

$authMiddlewareClass = GROCY_AUTH_CLASS;
$app->add(new $authMiddlewareClass($container, $app->getResponseFactory()));
// Add default middleware
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, false, false);
$errorMiddleware->setDefaultErrorHandler(
	new \Grocy\Controllers\ExceptionController($app, $container)
);

$app->add(new CorsMiddleware($app->getResponseFactory()));
$app->run();
