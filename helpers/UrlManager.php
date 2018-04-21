<?php

namespace Grocy\Helpers;

class UrlManager
{
	public function __construct(string $basePath)
	{
		if ($basePath === '/')
		{
			$this->BasePath = $this->GetBaseUrl();
		}
		else
		{
			$this->BasePath = $basePath;
		}
	}

	protected $BasePath;

	public function ConstructUrl($relativePath)
	{
		return rtrim($this->BasePath, '/') . $relativePath;
	}

	private function GetBaseUrl()
	{
		return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
	}
}
