<?php

namespace Grocy\Controllers;

use \Grocy\Services\StockService;
use \Grocy\Services\TasksService;
use \Grocy\Services\ChoresService;
use \Grocy\Services\BatteriesService;

class CalendarController extends BaseController
{
	public function __construct(\Slim\Container $container)
	{
		parent::__construct($container);
		$this->StockService = new StockService();
		$this->TasksService = new TasksService();
		$this->ChoresService = new ChoresService();
		$this->BatteriesService = new BatteriesService();
	}

	protected $StockService;
	protected $TasksService;
	protected $ChoresService;
	protected $BatteriesService;

	public function Overview(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args)
	{
		$products = $this->Database->products();
		$titlePrefix = $this->LocalizationService->Localize('Product expires') . ': ';
		$stockEvents = array();
		foreach($this->StockService->GetCurrentStock() as $currentStockEntry)
		{
			$stockEvents[] = array(
				'title' => $titlePrefix . FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name,
				'start' => $currentStockEntry->best_before_date
			);
		}

		$titlePrefix = $this->LocalizationService->Localize('Task due') . ': ';
		$taskEvents = array();
		foreach($this->TasksService->GetCurrent() as $currentTaskEntry)
		{
			$taskEvents[] = array(
				'title' => $titlePrefix . $currentTaskEntry->name,
				'start' => $currentTaskEntry->due_date
			);
		}

		$chores = $this->Database->chores();
		$titlePrefix = $this->LocalizationService->Localize('Chore due') . ': ';
		$choreEvents = array();
		foreach($this->ChoresService->GetCurrent() as $currentChoreEntry)
		{
			$choreEvents[] = array(
				'title' => $titlePrefix . FindObjectInArrayByPropertyValue($chores, 'id', $currentChoreEntry->chore_id)->name,
				'start' => $currentChoreEntry->next_estimated_execution_time
			);
		}

		$batteries = $this->Database->batteries();
		$titlePrefix = $this->LocalizationService->Localize('Battery charge cycle due') . ': ';
		$batteryEvents = array();
		foreach($this->BatteriesService->GetCurrent() as $currentBatteryEntry)
		{
			$batteryEvents[] = array(
				'title' => $titlePrefix . FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name,
				'start' => $currentBatteryEntry->next_estimated_charge_time
			);
		}

		$fullcalendarEventSources = array();
		$fullcalendarEventSources[] = $stockEvents;
		$fullcalendarEventSources[] = $taskEvents;
		$fullcalendarEventSources[] = $choreEvents;
		$fullcalendarEventSources[] = $batteryEvents;

		return $this->AppContainer->view->render($response, 'calendar', [
			'fullcalendarEventSources' => $fullcalendarEventSources
		]);
	}
}
