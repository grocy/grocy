<?php

namespace Grocy\Helpers;

use Jenssegers\Blade\Blade;
use Psr\Http\Message\ResponseInterface;

class SlimBladeView
{
	public function __construct(string $viewPaths, string $cachePath)
	{
		$this->ViewPaths = $viewPaths;
		$this->CachePath = $cachePath;
	}

	protected $ViewPaths;
	protected $CachePath;
	protected $ViewData = [];

	public function render(ResponseInterface $response, string $template, array $data = [])
	{
		$data = array_merge($this->ViewData, $data);
		$renderer = new Blade($this->ViewPaths, $this->CachePath, null);
		$output = $renderer->make($template, $data)->render();

		$response->getBody()->write($output);
		return $response;
	}

	public function set(string $key, mixed $value)
	{
		$this->ViewData[$key] = $value;
	}
}
