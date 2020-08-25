<?php


namespace Grocy\Controllers;


use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpSpecializedException;
use Throwable;

class ExceptionController extends BaseController
{
    /**
     * @var \Slim\App
     */
    private $app;

    public function __construct(\Slim\App $app, \DI\Container $container)
    {
        parent::__construct($container);
        $this->app = $app;
    }

    public function __invoke(ServerRequestInterface $request,
                             Throwable $exception,
                             bool $displayErrorDetails,
                             bool $logErrors,
                             bool $logErrorDetails,
                             ?LoggerInterface $logger = null)
    {
        $response = $this->app->getResponseFactory()->createResponse();
        if ($exception instanceof HttpNotFoundException) {
            return $this->renderPage($response->withStatus(404), 'errors/404', [
                'exception' => $exception
            ]);
        }
        if ($exception instanceof HttpForbiddenException) {
            return $this->renderPage($response->withStatus(403), 'errors/403', [
                'exception' => $exception
            ]);
        }

        return $this->renderPage($response->withStatus(500), 'errors/500', [
            'exception' => $exception
        ]);

    }
}