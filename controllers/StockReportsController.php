<?php

namespace Grocy\Controllers;

class StockReportsController extends BaseController
{
	public function Spendings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['start_date']) and isset($request->getQueryParams()['end_date']))
		{
			$startDate = $request->getQueryParams()['start_date'];
			$endDate = $request->getQueryParams()['end_date'];
			$where = "purchased_date >= '$startDate' AND purchased_date <= '$endDate'";
		}
		else
		{
			// Default this month
			$where = "purchased_date >= DATE(DATE('now', 'localtime'), 'start of month')";
		}


		if (isset($request->getQueryParams()['byGroup']))
		{
			$sql = "
			SELECT product_group_id as id, product_group as name, sum(quantity * price) as total
			FROM product_purchase_history
			WHERE $where
			GROUP BY product_group
			ORDER BY product_group
			";
		}
		else
		{
			if (isset($request->getQueryParams()['product_group']) and $request->getQueryParams()['product_group'] != 'all')
			{
				$where = $where . ' AND product_group_id = ' . $request->getQueryParams()['product_group'];
			}

			$sql = "
			SELECT product_id as id, product_name as name, product_group_id as group_id, product_group as group_name, sum(quantity * price) as total
			FROM product_purchase_history
			WHERE $where
			GROUP BY product_name
			ORDER BY product_name
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
