<?php

use Grocy\Controllers\ExceptionController;
use Grocy\Helpers\UrlManager;
use Grocy\Middleware\LocaleMiddleware;
use Grocy\Middleware\CorsMiddleware;
use Psr\Container\ContainerInterface as Container;
use Slim\Factory\AppFactory;
use Slim\Views\Blade;

// Load composer dependencies
require_once __DIR__ . '/packages/autoload.php';

// Load config files
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/config-dist.php'; // For not in own config defined values we use the default ones
require_once __DIR__ . '/helpers/ConfigurationValidator.php';

// Error reporting definitions
if (GROCY_MODE === 'dev')
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
}

// Definitions for dev/demo/prerelease mode
if ((GROCY_MODE === 'dev' || GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease') && !defined('GROCY_USER_ID'))
{
	define('GROCY_USER_ID', 1);
}

// Definitions for disabled authentication mode
if (GROCY_DISABLE_AUTH === true)
{
	if (!defined('GROCY_USER_ID'))
	{
		define('GROCY_USER_ID', 1);
	}
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
$viewcachePath = GROCY_DATAPATH . '/viewcache';
if (!file_exists($viewcachePath))
{
	mkdir($viewcachePath);
}

// Empty data/viewcache when and trigger database migrations when:
// The version changed (so when an update was done)
// GROCY_BASE_URL OR GROCY_BASE_PATH changed
$hash = hash('sha256', file_get_contents(__DIR__ . '/version.json') . GROCY_BASE_URL . GROCY_BASE_PATH);
$hashCacheFile = $viewcachePath . "/$hash.txt";
if (!file_exists($hashCacheFile))
{
	EmptyFolder($viewcachePath);
	if!(touch($hashCacheFile))
	{
		// We failed to create the cache file, probably because of a missing write permission, bail!
		exit('Unable to write to the viewcache directory.');
	}

	if (function_exists('opcache_reset'))
	{
		opcache_reset();
	}

	// Schema migration happens on the root route, so redirect to there
	header('Location: ' . (new UrlManager(GROCY_BASE_URL))->ConstructUrl('/'));
	exit();
}

// Setup base application
AppFactory::setContainer(new DI\Container());
$app = AppFactory::create();

$container = $app->getContainer();
$container->set('view', function (Container $container)
{
	return new Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache');
});

$container->set('UrlManager', function (Container $container)
{
	return new UrlManager(GROCY_BASE_URL);
});

$container->set('ApiKeyHeaderName', function (Container $container)
{
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
	$app->add(new LocaleMiddleware($container));
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
	new ExceptionController($app, $container)
);

$app->add(new CorsMiddleware($app->getResponseFactory()));

$app->getRouteCollector()->setCacheFile(GROCY_DATAPATH . '/viewcache/route_cache.php');

ob_clean(); // No response output before here
$app->run();
