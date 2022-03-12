<?php

namespace Grocy\Controllers;

use LessQL\Result;

class BaseApiController extends BaseController
{
	const PATTERN_FIELD = '[A-Za-z_][A-Za-z0-9_\.]+';

	const PATTERN_OPERATOR = '!?(=|~|<|>|(>=)|(<=)|(§))';

	const PATTERN_VALUE = '[A-Za-z\p{L}\p{M}0-9*_.$#^| -\\\]+';

	protected $OpenApiSpec = null;

	protected function ApiResponse(\Psr\Http\Message\ResponseInterface $response, $data, $cache = false)
	{
		if ($cache) {
			$response = $response->withHeader('Cache-Control', 'max-age=2592000');
		}

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
		// get total row count
		$response = $response->withHeader('x-rowcount-total', $data->count());

		// apply filter, get filtered row count
		$data = $this->applyQuery($data, $query);
		$response = $response->withHeader('x-rowcount-filtered', $data->count());

		// apply limit/order
		$data = $this->applyOrder($data, $query);
		$data = $this->applyLimit($data, $query);
		return $this->ApiResponse($response, $data);
	}

	protected function applyQuery(Result $data, array $query): Result
	{
		if (isset($query['query'])) {
			$data = $this->filter($data, $query['query']);
		}

		if (isset($query['search'])) {
			// get list of fields from table
			$stmt = $this->getDatabase()->prepare('SELECT `sql` FROM sqlite_master WHERE `name` = ? LIMIT 1');
			$stmt->execute([$data->getTable()]);
			$sql = $stmt->fetchColumn();
			$sql = substr($sql, strpos($sql, '(') + 1);
			$sql = substr($sql, 0, strrpos($sql, ')'));
			$sql = trim($sql);
			while (preg_match('/\(.*?\)/', $sql) === 1) {
				$sql = preg_replace('/\([^\(\)]*\)/', '', $sql);
			}
			$fields = array_map(function ($field) {
				preg_match('/\s*([^\s]+)/', $field, $matches);
				return $matches[1];
			}, explode(',', $sql));

			$join_info = match ($data->getTable()) {
				'products' => [
					'product_group_id'     => ['field' => 'name', 'table' => 'product_groups'],
					'location_id'          => ['field' => 'name', 'table' => 'locations'],
					'shopping_location_id' => ['field' => 'name', 'table' => 'shopping_locations'],
					'qu_id_purchase'       => ['field' => 'name', 'table' => 'quantity_units'],
					'qu_id_stock'          => ['field' => 'name', 'table' => 'quantity_units'],
					'parent_product_id'    => ['field' => 'name', 'table' => 'products'],
				],
				default => [],
			};

			// create search query that matches any field
			$fields_query = implode(' OR ', array_map(function ($field) use ($join_info) {
				$field_escaped = '`' . str_replace('`', '``', $field) . '`';
				if (array_key_exists($field, $join_info)) {
					$table_escaped = '`' . str_replace('`', '``', $join_info[$field]['table']) . '`';
					return $field_escaped . ' IN(SELECT id FROM ' . $table_escaped . ' WHERE `' . str_replace('`', '``', $join_info[$field]['field']) . '` LIKE ? ESCAPE \'\\\')';
				}
				return $field_escaped . ' LIKE ? ESCAPE \'\\\'';
			}, $fields));

			// apply search query
			$data = $data->where($fields_query, array_fill(0, count($fields), '%' . str_replace(['\\', '%', '_', '*'], ['\\\\', '\\%', '\\_', '%'], $query['search']) . '%'));
		}

		return $data;
	}

	protected function applyLimit(Result $data, array $query): Result
	{
		if (isset($query['limit'])) {
			$data = $data->limit(intval($query['limit']), intval($query['offset'] ?? 0));
		}

		return $data;
	}

	protected function applyOrder(Result $data, array $query): Result
	{
		if (isset($query['order'])) {
			$parts = explode(',', $query['order']);
			foreach ($parts as $part) {
				$col_dir = explode(':', $part, 2);
				if (count($col_dir) == 1) {
					$data = $data->orderBy($col_dir[0]);
				} else {
					if ($col_dir[1] != 'asc' && $col_dir[1] != 'desc' && $col_dir[1] != 'collate nocase') {
						throw new \Exception('Invalid sort order ' . $col_dir[1]);
					}
					$data = $data->orderBy($col_dir[0], $col_dir[1]);
				}
			}
		}

		return $data;
	}

	protected function filter(Result $data, array $query): Result
	{
		foreach ($query as $q) {
			$matches = [];
			preg_match(
				'/(?P<field>' . self::PATTERN_FIELD . ')'
					. '(?P<op>' . self::PATTERN_OPERATOR . ')'
					. '(?P<value>' . self::PATTERN_VALUE . ')/u',
				$q,
				$matches
			);

			if (!array_key_exists('field', $matches) || !array_key_exists('op', $matches) || !array_key_exists('value', $matches) || !in_array($matches['op'], ['=', '!=', '~', '!~', '<', '>', '>=', '<=', '§'], true)) {
				throw new \Exception('Invalid query');
			}
			list('field' => $field, 'op' => $op, 'value' => $value) = $matches;

			$params = match ($op) {
				'=' => [$value],
				'!=' => [$value],
				'~' => ['%' . $value . '%'],
				'!~' => ['%' . $value . '%'],
				'<' => [$value],
				'>' => [$value],
				'>=' => [$value],
				'<=' => [$value],
				'§' => [$value],
				default => [],
			};

			$where_prefix = '';
			$where_suffix = '';
			if (strpos($field, '.') !== false) {
				list($join, $field) = explode('.', $field, 2);
				$join_info = match ($data->getTable()) {
					'products' => [
						'product_group'     => ['id_field' => 'product_group_id',     'table' => 'product_groups'],
						'location'          => ['id_field' => 'location_id',          'table' => 'locations'],
						'shopping_location' => ['id_field' => 'shopping_location_id', 'table' => 'shopping_locations'],
						'qu_purchase'       => ['id_field' => 'qu_id_purchase',       'table' => 'quantity_units'],
						'qu_stock'          => ['id_field' => 'qu_id_stock',          'table' => 'quantity_units'],
						'parent_product'    => ['id_field' => 'parent_product_id',    'table' => 'products'],
					],
					default => [],
				};
				if (!array_key_exists($join, $join_info)) {
					throw new \Exception('Invalid query');
				}
				$field_escaped = '`' . str_replace('`', '``', $join_info[$join]['id_field']) . '`';
				$table_escaped = '`' . str_replace('`', '``', $join_info[$join]['table']) . '`';
				$where_prefix = $field_escaped . ' IN(SELECT id FROM ' . $table_escaped . ' WHERE ';
				$where_suffix = ')';
			}

			$field_escaped = '`' . str_replace('`', '``', $field) . '`';
			$where = match ($op) {
				'=' => $field_escaped . ' = ?' . (strtolower($value) === 'null' ? ' OR ' . $field_escaped . ' IS NULL' : ''),
				'!=' => $field_escaped . ' != ?' . (strtolower($value) === 'null' ? ' OR ' . $field_escaped . ' IS NULL' : ''),
				'~' => $field_escaped . ' LIKE ?',
				'!~' => $field_escaped . ' NOT LIKE ?',
				'<' => $field_escaped . ' < ?',
				'>' => $field_escaped . ' > ?',
				'>=' => $field_escaped . ' >= ?',
				'<=' => $field_escaped . ' <= ?',
				'§' => $field_escaped . ' REGEXP ?',
				default => '',
			};

			$data = $data->where($where_prefix . $where . $where_suffix, $params);
		}

		return $data;
	}

	protected function getOpenApispec()
	{
		if ($this->OpenApiSpec == null) {
			$this->OpenApiSpec = json_decode(file_get_contents(__DIR__ . '/../grocy.openapi.json'));
		}

		return $this->OpenApiSpec;
	}
}
