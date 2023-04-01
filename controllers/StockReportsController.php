<?php

namespace Grocy\Controllers;

class StockReportsController extends BaseController
{
	public function Spendings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['start_date']) && isset($request->getQueryParams()['end_date']) && IsIsoDate($request->getQueryParams()['start_date']) && IsIsoDate($request->getQueryParams()['end_date']))
		{
			$startDate = $request->getQueryParams()['start_date'];
			$endDate = $request->getQueryParams()['end_date'];
			$where = "pph.purchased_date BETWEEN '$startDate' AND '$endDate'";
		}
		else
		{
			// Default to this month
			$where = "pph.purchased_date >= DATE(DATE('now', 'localtime'), 'start of month')";
		}


		if (isset($request->getQueryParams()['byGroup']))
		{
			$sql = "
			SELECT
				pg.id AS id,
				pg.name AS name,
				SUM(pph.amount * pph.price) AS total
			FROM product_price_history pph
			JOIN products p
				ON pph.product_id = p.id
			JOIN product_groups pg
				ON p.product_group_id = pg.id
			WHERE $where
			GROUP BY pg.id
			ORDER BY pg.NAME COLLATE NOCASE
			";
		}
		else
		{
			if (isset($request->getQueryParams()['product_group']) and $request->getQueryParams()['product_group'] != 'all')
			{
				$where .= ' AND pg.id = ' . $request->getQueryParams()['product_group'];
			}

			$sql = "
			SELECT
				p.id AS id,
				p.name AS name,
				pg.id AS group_id,
				pg.name AS group_name,
				SUM(pph.amount * pph.price) AS total
			FROM product_price_history pph
			JOIN products p
				ON pph.product_id = p.id
			JOIN product_groups pg
				ON p.product_group_id = pg.id
			WHERE $where
			GROUP BY p.id
			ORDER BY p.NAME COLLATE NOCASE
			";
		}

		return $this->renderPage($response, 'stockreportspendings', [
			'metrics' => $this->getDatabaseService()->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ),
			'productGroups' => $this->getDatabase()->product_groups()->orderBy('name', 'COLLATE NOCASE'),
			'selectedGroup' => isset($request->getQueryParams()['product_group']) ? $request->getQueryParams()['product_group'] : null,
			'byGroup' => isset($request->getQueryParams()['byGroup']) ? $request->getQueryParams()['byGroup'] : null
		]);
	}
}
