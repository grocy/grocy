<?php

namespace Grocy\Controllers;

use \Grocy\Services\SessionService;
use \Grocy\Services\DatabaseMigrationService;
use \Grocy\Services\DemoDataGeneratorService;

class LoginController extends BaseController
{
	public function __construct(\Slim\Container $container, string $sessionCookieName)
	{
        #$fp = fopen('/config/data/sql.log', 'a');
        #$time_start = microtime(true);
		parent::__construct($container);
        #fwrite($fp, "£££ Login controller - parent construstor time : " . round((microtime(true) - $time_start),6) . "\n");
		#$this->SessionService = SessionService::getInstance();
        #fwrite($fp, "£££ Login controller - got session service instance : " . round((microtime(true) - $time_start),6) . "\n");
		$this->SessionCookieName = $sessionCookieName;
        #fwrite($fp, "£££ Login controller - construction time : " . round((microtime(true) - $time_start),6) . "\n");
        #fclose($fp);
	}

	protected $SessionService = null;
	protected $SessionCookieName;

    private function getSessionService()
	{
		if($this->SessionsService == null)
		{
			$this->SessionService = SessionService::getInstance();
		}
		return $this->SessionService;
	}

	public function ProcessLogin(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
        #$fp = fopen('/config/data/sql.log', 'a');
        #fwrite($fp, "£££ Login controller - ProcessLogin called\n");
        #fclose($fp);
		$postParams = $request->getParsedBody();
		if (isset($postParams['username']) && isset($postParams['password']))
		{
			$user = $this->getDatabase()->users()->where('username', $postParams['username'])->fetch();
			$inputPassword = $postParams['password'];
			$stayLoggedInPermanently = $postParams['stay_logged_in'] == 'on';

			if ($user !== null && password_verify($inputPassword, $user->password))
			{
				$sessionKey = $this->getSessionService()->CreateSession($user->id, $stayLoggedInPermanently);
				setcookie($this->SessionCookieName, $sessionKey, PHP_INT_SIZE == 4 ? PHP_INT_MAX : PHP_INT_MAX>>32); // Cookie expires never, but session validity is up to SessionService

				if (password_needs_rehash($user->password, PASSWORD_DEFAULT))
				{
					$user->update(array(
						'password' => password_hash($inputPassword, PASSWORD_DEFAULT)
					));
				}

				return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/'));
			}
			else
			{
				return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login?invalid=true'));
			}
		}
		else
		{
			return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/login?invalid=true'));
		}
	}

	public function LoginPage(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		return $this->renderPage($response, 'login');
	}

	public function Logout(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$this->getSessionService()->RemoveSession($_COOKIE[$this->SessionCookieName]);
		return $response->withRedirect($this->AppContainer->UrlManager->ConstructUrl('/'));
	}

	public function GetSessionCookieName()
	{
		return $this->SessionCookieName;
	}
}
