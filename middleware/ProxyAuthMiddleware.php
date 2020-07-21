<?php


namespace Grocy\Middleware;


use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProxyAuthMiddleware extends AuthMiddleware
{
    function authenticate(Request $request)
    {
        $db = DatabaseService::getInstance()->GetDbConnection();

        $username = $request->getHeader(GROCY_PROXY_AUTH_HEADER);
        error_log(var_dump($username));
        if (count($username) != 1) {
            // Invalid configuration of Proxy
            throw new \Exception("Invalid Username from Proxy " . var_dump($username));
        }

        $username = $username[0];

        $user = $db->users()->where('username', $username)->fetch();
        if ($user == null) {
            $user = UsersService::getInstance()->CreateUser(
                $username,
                '',
                '',
                ''
            );
        }
        return $user;
    }
}