<?php

namespace Grocy\Controllers;

use Grocy\Helpers\Grocycode;
use jucksearm\barcode\lib\BarcodeFactory;
use jucksearm\barcode\lib\DatamatrixFactory;

class BatteriesController extends BaseController
{
	public function BatteriesList(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['include_disabled']))
		{
			$batteries = $this->getDatabase()->batteries()->orderBy('name', 'COLLATE NOCASE');
		}
		else
		{
			$batteries = $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		}

		return $this->renderPage($response, 'batteries', [
			'batteries' => $batteries,
			'userfields' => $this->getUserfieldsService()->GetFields('batteries'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('batteries')
		]);
	}

	public function BatteriesSettings(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'batteriessettings');
	}

	public function BatteryEditForm(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if ($args['batteryId'] == 'new')
		{
			return $this->renderPage($response, 'batteryform', [
				'mode' => 'create',
				'userfields' => $this->getUserfieldsService()->GetFields('batteries')
			]);
		}
		else
		{
			return $this->renderPage($response, 'batteryform', [
				'battery' => $this->getDatabase()->batteries($args['batteryId']),
				'mode' => 'edit',
				'userfields' => $this->getUserfieldsService()->GetFields('batteries')
			]);
		}
	}

	public function Journal(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		if (isset($request->getQueryParams()['months']) && filter_var($request->getQueryParams()['months'], FILTER_VALIDATE_INT) !== false)
		{
			$months = $request->getQueryParams()['months'];
			$where = "tracked_time > DATE(DATE('now', 'localtime'), '-$months months')";
		}
		else
		{
			// Default 2 years
			$where = "tracked_time > DATE(DATE('now', 'localtime'), '-24 months')";
		}

		if (isset($request->getQueryParams()['battery']) && filter_var($request->getQueryParams()['battery'], FILTER_VALIDATE_INT) !== false)
		{
			$batteryId = $request->getQueryParams()['battery'];
			$where .= " AND battery_id = $batteryId";
		}

		return $this->renderPage($response, 'batteriesjournal', [
			'chargeCycles' => $this->getDatabase()->battery_charge_cycles()->where($where)->orderBy('tracked_time', 'DESC'),
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function Overview(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$usersService = $this->getUsersService();
		$nextXDays = $usersService->GetUserSettings(GROCY_USER_ID)['batteries_due_soon_days'];

		return $this->renderPage($response, 'batteriesoverview', [
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE'),
			'current' => $this->getBatteriesService()->GetCurrent(),
			'nextXDays' => $nextXDays,
			'userfields' => $this->getUserfieldsService()->GetFields('batteries'),
			'userfieldValues' => $this->getUserfieldsService()->GetAllValues('batteries')
		]);
	}

	public function TrackChargeCycle(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		return $this->renderPage($response, 'batterytracking', [
			'batteries' => $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE')
		]);
	}

	public function BatteryGrocycodeImage(\Psr\Http\Message\ServerRequestInterface $request, \Psr\Http\Message\ResponseInterface $response, array $args)
	{
		$size = $request->getQueryParam('size', null);
		$gc = new Grocycode(Grocycode::BATTERY, $args['batteryId']);

		if (GROCY_GROCYCODE_TYPE == '2D')
		{
			$png = (new DatamatrixFactory())->setCode((string) $gc)->setSize($size)->getDatamatrixPngData();
		}
		else
		{
			$png = (new BarcodeFactory())->setType('C128')->setCode((string) $gc)->setHeight($size)->getBarcodePngData();
		}

		$isDownload = $request->getQueryParam('download', false);
		if ($isDownload)
		{
			$response = $response->withHeader('Content-Type', 'application/octet-stream')
			->withHeader('Content-Disposition', 'attachment; filename=grocycode.png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		else
		{
			$response = $response->withHeader('Content-Type', 'image/png')
			->withHeader('Content-Length', strlen($png))
			->withHeader('Cache-Control', 'no-cache')
			->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
		}
		$response->getBody()->write($png);
		return $response;
	}

	public function __construct(\DI\Container $container)
	{
		parent::__construct($container);
	}
}
