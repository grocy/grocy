<?php

namespace Grocy\Helpers;

class UrlManager
{
	public function __construct(string $basePath) {
		$this->BasePath = $basePath;
	}

	protected $BasePath;

	public function ConstructUrl($relativePath)
	{
		return rtrim($this->BasePath, '/') . $relativePath;
	}
}
