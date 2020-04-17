<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface as Container;
use Slim\Factory\AppFactory;

use Grocy\Helpers\UrlManager;
use Grocy\Controllers\LoginController;

// Load composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

// Load config files
require_once GROCY_DATAPATH . '/config.php';
require_once __DIR__ . '/config-dist.php'; // For not in own config defined values we use the default ones

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

// Setup base application
AppFactory::setContainer(new DI\Container());
$app = AppFactory::create();

$container = $app->getContainer();
$container->set('view', function(Container $container)
{
	return new Slim\Views\Blade(__DIR__ . '/views', GROCY_DATAPATH . '/viewcache');
});
$container->set('LoginControllerInstance', function(Container $container)
{
	return new LoginController($container, 'grocy_session');
});
$container->set('UrlManager', function(Container $container)
{
	return new UrlManager(GROCY_BASE_URL);
});
$container->set('ApiKeyHeaderName', function(Container $container)
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

// Add default middleware
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, false, false);

$app->run();
