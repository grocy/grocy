<?php


namespace Grocy\Middleware;


use Psr\Http\Message\ServerRequestInterface as Request;

class DefaultAuthMiddleware extends AuthMiddleware
{

    protected function authenticate(Request $request)
    {
        $auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
        $user = $auth->authenticate($request);
        if ($user !== null)
            return $user;

        $auth = new SessionAuthMiddleware($this->AppContainer, $this->ResponseFactory);
        $user = $auth->authenticate($request);

        return $user;

    }
}