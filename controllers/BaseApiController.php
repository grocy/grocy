<?php

namespace Grocy\Controllers;

use LessQL\Result;

class BaseApiController extends BaseController
{
	protected $OpenApiSpec = null;

	const PATTERN_FIELD = '[A-Za-z_][A-Za-z0-9_]+';
	const PATTERN_OPERATOR = '!?(=|~|<|>|(>=)|(<=))';
	const PATTERN_VALUE = '[A-Za-z_0-9.]+';

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}

	protected function ApiResponse(\Psr\Http\Message\ResponseInterface $response, $data)
	{
		$response->getBody()->write(json_encode($data));
		return $response;
	}

	protected function EmptyApiResponse(\Psr\Http\Message\ResponseInterface $response, $status = 204)
	{
		return $response->withStatus($status);
	}

	protected function GenericErrorResponse(\Psr\Http\Message\ResponseInterface $response, $errorMessage, $status = 400)
	{
		return $response->withStatus($status)->withJson([
			'error_message' => $errorMessage
		]);
	}

	public function FilteredApiResponse(\Psr\Http\Message\ResponseInterface $response, Result $data, array $query)
	{
		$data = $this->queryData($data, $query);
		return $this->ApiResponse($response, $data);
	}

	protected function queryData(Result $data, array $query)
	{
		if (isset($query['query']))
			$data = $this->filter($data, $query['query']);
		if (isset($query['limit']))
			$data = $data->limit(intval($query['limit']), intval($query['offset'] ?? 0));
		if (isset($query['order']))
			$data = $data->orderBy($query['order']);
		return $data;
	}

	protected function filter(Result $data, array $query): Result
	{
		foreach ($query as $q) {
			$matches = array();
			preg_match('/(?P<field>' . self::PATTERN_FIELD . ')'
				. '(?P<op>' . self::PATTERN_OPERATOR . ')'
				. '(?P<value>' . self::PATTERN_VALUE . ')/',
				$q, $matches
			);
			error_log(var_export($matches, true));
			switch ($matches['op']) {
				case '=':
					$data = $data->where($matches['field'], $matches['value']);
					break;
				case '!=':
					$data = $data->whereNot($matches['field'], $matches['value']);
					break;
				case '~':
					$data = $data->where($matches['field'] . ' LIKE ?', '%' . $matches['value'] . '%');
					break;
				case '!~':
					$data = $data->where($matches['field'] . ' NOT LIKE ?', '%' . $matches['value'] . '%');
					break;
				case '!>=':
				case '<':
					$data = $data->where($matches['field'] . ' < ?', $matches['value']);
					break;
				case '!<=':
				case '>':
					$data = $data->where($matches['field'] . ' > ?', $matches['value']);
					break;
				case '!<':
				case '>=':
					$data = $data->where($matches['field'] . ' >= ?', $matches['value']);
					break;
				case '!>':
				case '<=':
					$data = $data->where($matches['field'] . ' <= ?', $matches['value']);
					break;

			}
		}
		return $data;
	}

	protected function getOpenApispec()
	{
		if ($this->OpenApiSpec == null)
		{
			$this->OpenApiSpec = json_decode(file_get_contents(__DIR__ . '/../grocy.openapi.json'));
		}

		return $this->OpenApiSpec;
	}

}
