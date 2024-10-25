<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StockReportsController extends BaseController
{
	public function Spendings(Request $request, Response $response, array $args)
	{
		$where = "pph.transaction_type != 'self-production'";

		if (isset($request->getQueryParams()['start_date']) && isset($request->getQueryParams()['end_date']) && IsIsoDate($request->getQueryParams()['start_date']) && IsIsoDate($request->getQueryParams()['end_date']))
		{
			$startDate = $request->getQueryParams()['start_date'];
			$endDate = $request->getQueryParams()['end_date'];
			$where .= " AND pph.purchased_date BETWEEN '$startDate' AND '$endDate'";
		}
		else
		{
			// Default to this month
			$where .= " AND pph.purchased_date >= DATE(DATE('now', 'localtime'), 'start of month')";
		}

		$groupBy = 'product';
		if (isset($request->getQueryParams()['group-by']) && in_array($request->getQueryParams()['group-by'], ['product', 'productgroup', 'store']))
		{
			$groupBy = $request->getQueryParams()['group-by'];
		}

		if ($groupBy == 'product')
		{
			if (isset($request->getQueryParams()['product-group']))
			{
				if ($request->getQueryParams()['product-group'] == 'ungrouped')
				{
					$where .= ' AND pg.id IS NULL';
				}
				elseif ($request->getQueryParams()['product-group'] != 'all')
				{
					$where .= ' AND pg.id = ' . $request->getQueryParams()['product-group'];
				}
			}

			$sql = "
			SELECT
				p.id AS id,
				p.name AS name,
				pg.id AS group_id,
				pg.name AS group_name,
				SUM(pph.amount * pph.price) AS total
			FROM products_price_history pph
			JOIN products p
				ON pph.product_id = p.id
			LEFT JOIN product_groups pg
				ON p.product_group_id = pg.id
			WHERE $where
			GROUP BY p.id, p.name, pg.id, pg.name
			ORDER BY p.name COLLATE NOCASE
			";
		}
		elseif ($groupBy == 'productgroup')
		{
			$sql = "
			SELECT
				pg.id AS id,
				pg.name AS name,
				SUM(pph.amount * pph.price) AS total
			FROM products_price_history pph
			JOIN products p
				ON pph.product_id = p.id
			LEFT JOIN product_groups pg
				ON p.product_group_id = pg.id
			WHERE $where
			GROUP BY pg.id, pg.name
			ORDER BY pg.name COLLATE NOCASE
			";
		}
		elseif ($groupBy == 'store')
		{
			$sql = "
			SELECT
				sl.id AS id,
				sl.name AS name,
				SUM(pph.amount * pph.price) AS total
			FROM products_price_history pph
			JOIN products p
				ON pph.product_id = p.id
			LEFT JOIN shopping_locations sl
				ON pph.shopping_location_id = sl.id
			WHERE $where
			GROUP BY sl.id, sl.name
			ORDER BY sl.NAME COLLATE NOCASE
			";
		}

		return $this->renderPage($response, 'stockreportspendings', [
			'metrics' => $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ),
			'productGroups' => $this->getDatabase()->product_groups()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'selectedGroup' => isset($request->getQueryParams()['product-group']) ? $request->getQueryParams()['product-group'] : null,
			'groupBy' => $groupBy
		]);
	}
}
