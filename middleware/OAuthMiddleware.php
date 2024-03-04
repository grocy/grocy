<?php

namespace Grocy\Middleware;

use DI\Container;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Grocy\Services\DatabaseService;
use Grocy\Services\UsersService;
use Grocy\Services\SessionService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * minimalistic OAuth middleware
 */
class OAuthMiddleware extends AuthMiddleware
{
	private Client $client;

	public function __construct(Container $container, ResponseFactoryInterface $responseFactory)
	{
		parent::__construct($container, $responseFactory);

		$this->client = new Client(['timeout' => 2.0]);
	}

	public function authenticate(Request $request)
	{
		define('GROCY_EXTERNALLY_MANAGED_AUTHENTICATION', true);

		// First try to authenticate by API key
		$auth = new ApiKeyAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);
		if ($user !== null)
		{
			return $user;
		}

		// Then by session cookie
		$auth = new SessionAuthMiddleware($this->AppContainer, $this->ResponseFactory);
		$user = $auth->authenticate($request);
		if ($user !== null)
		{
			return $user;
		}

		// no active session -> start OAuth flow
		// 1. redirect to auth URL (only if code parameter not already set)
		$code = $request->getQueryParam('code');
		if ($code === null) {
			$response = $this->ResponseFactory->createResponse();
			if (string_starts_with($request->getUri()->getPath(), '/api/')) {
				// no OAuth for API calls
				return $response->withStatus(401);
			} else {
				return $response->withRedirect(
					GROCY_OAUTH_AUTH_URL .
					"?response_type=code" .
					"&client_id=" . GROCY_OAUTH_CLIENT_ID .
					"&redirect_uri=" . $request->getUri() .
					"&scope=" . GROCY_OAUTH_SCOPES
				);
			}
		}

		// 2. handle callback from auth server
		// -> code parameter given to get token from aut server
		$tokenResponse = $this->client->request('POST', GROCY_OAUTH_TOKEN_URL, [
			RequestOptions::AUTH => [GROCY_OAUTH_CLIENT_ID, GROCY_OAUTH_CLIENT_SECRET],
			RequestOptions::FORM_PARAMS => [
				"grant_type" => "authorization_code",
				"code" => $code,
				"redirect_uri" => (string)$request->getUri(),
			],
		]);
		if ($tokenResponse->getStatusCode() != 200) {
			throw new \Exception('token request failed. Status code: ' . $response->getStatusCode());
		}
		$tokenResponseJson = json_decode($tokenResponse->getBody(), true);

		// auth successful -> start collection user information
		$infoResponse = $this->client->request('POST', GROCY_OAUTH_USERINFO_URL, [
			RequestOptions::HEADERS => [
				"Authorization" => "Bearer " . $tokenResponseJson["access_token"]
			],
		]);
		if ($infoResponse->getStatusCode() != 200) {
			throw new \Exception('user info request failed error: ' . $response->getStatusCode());
		}
		$infoResponseJson = json_decode($infoResponse->getBody(), true);

		// get user from database or create one if needed
		$db = DatabaseService::getInstance()->GetDbConnection();
		$user = $db->users()->where('username', $infoResponseJson[GROCY_OAUTH_USERNAME_CLAIM])->fetch();
		if ($user == null) {
			$user = UsersService::getInstance()->CreateUser($infoResponseJson[GROCY_OAUTH_USERNAME_CLAIM], '', '', '');
		}

		self::SetSessionCookie(SessionService::getInstance()->CreateSession($user->id, false));

		return $user;
	}

	public static function ProcessLogin(array $postParams)
	{
		throw new \Exception('Not implemented');
	}
}
