<?php

namespace Grocy\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExportController extends BaseController
{
	public function DownloadSql(Request $request, Response $response, array $args) {
		$databasePath = GROCY_DATAPATH . '/grocy.db';

		if (file_exists($databasePath))
		{
			$dateTimeString = date('Y-m-d-H:i:s');

			$response = $response->withHeader('Content-Type', 'application/octet-stream');
			$response = $response->withHeader('Content-Disposition', "attachment; filename=\"grocy-$dateTimeString.db\"");
			$response = $response->withHeader('Content-Length', filesize($databasePath));
			$response->getBody()->write(file_get_contents($databasePath));
			return $response;
		}
		else
		{
			return $response->withStatus(404, 'Database file not found');
		}
	}
}
