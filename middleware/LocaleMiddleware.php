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
		$locale = $this->getLocale($request);
		define('GROCY_LOCALE', $locale);
		return $handler->handle($request);
	}

	protected function getLocale(Request $request)
	{
		if (defined('GROCY_AUTHENTICATED') && GROCY_AUTHENTICATED)
		{
			$locale = UsersService::getInstance()->GetUserSetting(GROCY_USER_ID, 'locale');
			if (isset($locale) && !empty($locale))
			{
				if (in_array($locale, scandir(__DIR__ . '/../localization')))
				{
					return $locale;
				}
			}
		}

		$langs = implode(',', $request->getHeader('Accept-Language'));

		// Src: https://gist.github.com/spolischook/0cde9c6286415cddc088
		$prefLocales = array_reduce(
			explode(',', $langs),
			function ($res, $el)
			{
				list($l, $q) = array_merge(explode(';q=', $el), [1]);
				$res[$l] = (float)$q;
				return $res;
			},
			[]
		);
		arsort($prefLocales);

		$availableLocales = scandir(__DIR__ . '/../localization');
		foreach ($prefLocales as $locale => $q)
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

		return GROCY_DEFAULT_LOCALE;
	}
}
