<?php

namespace Grocy\Middleware;

use Grocy\Services\UsersService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class LocaleMiddleware extends BaseMiddleware
{
	public function __invoke(Request $request, RequestHandler $handler): Response
	{
		define('GROCY_LOCALE', $this->GetLocale($request));

		return $handler->handle($request);
	}

	private function GetLocale(Request $request)
	{
		// Demo and Prerelease modes are fixed to the default locale
		if (GROCY_MODE === 'demo' || GROCY_MODE === 'prerelease')
		{
			return GROCY_DEFAULT_LOCALE;
		}

		// Prefer user setting
		if (defined('GROCY_AUTHENTICATED') && GROCY_AUTHENTICATED)
		{
			$locale = UsersService::GetInstance()->GetUserSetting(GROCY_USER_ID, 'locale');
			if (isset($locale) && !empty($locale))
			{
				if (in_array($locale, scandir(__DIR__ . '/../localization')))
				{
					return $locale;
				}
			}
		}

		// Otherwise use Browser prefered locale
		$browserPreferedLocales = array_reduce(
			explode(',', implode(',', $request->getHeader('Accept-Language'))),
			function ($res, $el)
			{
				list($l, $q) = array_merge(explode(';q=', $el), [1]);
				$res[$l] = (float)$q;
				return $res;
			},
			[]
		);
		arsort($browserPreferedLocales);

		$availableLocales = scandir(__DIR__ . '/../localization');
		foreach ($browserPreferedLocales as $locale => $q)
		{
			if (in_array($locale, $availableLocales))
			{
				return $locale;
			}

			// e.g. en_GB
			if (in_array(substr($locale, 0, 5), $availableLocales))
			{
				return substr($locale, 0, 5);
			}

			// e.g. cs
			if (in_array(substr($locale, 0, 2), $availableLocales))
			{
				return substr($locale, 0, 2);
			}
		}

		// Falback to default locale
		return GROCY_DEFAULT_LOCALE;
	}
}
