<?php

namespace Grocy\Services;

use \Grocy\Services\StockService;
use \Grocy\Services\TasksService;
use \Grocy\Services\ChoresService;
use \Grocy\Services\BatteriesService;
use \Grocy\Services\UsersService;

class CalendarService extends BaseService
{
	public function __construct()
	{
		parent::__construct();
		$this->StockService = new StockService();
		$this->TasksService = new TasksService();
		$this->ChoresService = new ChoresService();
		$this->BatteriesService = new BatteriesService();
	}

	protected $StockService;
	protected $TasksService;
	protected $ChoresService;
	protected $BatteriesService;

	public function GetEvents()
	{
		$products = $this->Database->products();
		$titlePrefix = $this->LocalizationService->__t('Product expires') . ': ';
		$stockEvents = array();
		foreach($this->StockService->GetCurrentStock() as $currentStockEntry)
		{
			if ($currentStockEntry->amount > 0)
			{
				$stockEvents[] = array(
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name,
					'start' => $currentStockEntry->best_before_date,
					'date_format' => 'date'
				);
			}
		}

		$titlePrefix = $this->LocalizationService->__t('Task due') . ': ';
		$taskEvents = array();
		foreach($this->TasksService->GetCurrent() as $currentTaskEntry)
		{
			$taskEvents[] = array(
				'title' => $titlePrefix . $currentTaskEntry->name,
				'start' => $currentTaskEntry->due_date,
				'date_format' => 'date'
			);
		}

		$usersService = new UsersService();
		$users = $usersService->GetUsersAsDto();

		$chores = $this->Database->chores();
		$titlePrefix = $this->LocalizationService->__t('Chore due') . ': ';
		$choreEvents = array();
		foreach($this->ChoresService->GetCurrent() as $currentChoreEntry)
		{
			$chore = FindObjectInArrayByPropertyValue($chores, 'id', $currentChoreEntry->chore_id);

			$assignedToText = '';
			if (!empty($currentChoreEntry->next_execution_assigned_to_user_id))
			{
				$assignedToText = ' (' . $this->LocalizationService->__t('assigned to %s', FindObjectInArrayByPropertyValue($users, 'id', $currentChoreEntry->next_execution_assigned_to_user_id)->display_name) . ')';
			}

			$choreEvents[] = array(
				'title' => $titlePrefix . $chore->name . $assignedToText,
				'start' => $currentChoreEntry->next_estimated_execution_time,
				'date_format' => 'datetime'
			);
		}

		$batteries = $this->Database->batteries();
		$titlePrefix = $this->LocalizationService->__t('Battery charge cycle due') . ': ';
		$batteryEvents = array();
		foreach($this->BatteriesService->GetCurrent() as $currentBatteryEntry)
		{
			$batteryEvents[] = array(
				'title' => $titlePrefix . FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name,
				'start' => $currentBatteryEntry->next_estimated_charge_time,
				'date_format' => 'datetime'
			);
		}

		$recipes = $this->Database->recipes();
		$mealPlanDayRecipes = $this->Database->recipes()->where('type', 'mealplan-day');
		$titlePrefix = $this->LocalizationService->__t('Meal plan') . ': ';
		$mealPlanRecipeEvents = array();
		foreach($mealPlanDayRecipes as $mealPlanDayRecipe)
		{
			$recipesOfCurrentDay = $this->Database->recipes_nestings_resolved()->where('recipe_id = :1 AND includes_recipe_id != :1', $mealPlanDayRecipe->id);
			foreach ($recipesOfCurrentDay as $recipeOfCurrentDay)
			{
				$mealPlanRecipeEvents[] = array(
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->includes_recipe_id)->name,
					'start' => FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->recipe_id)->name,
					'date_format' => 'date',
					'description' => GROCY_BASE_URL . '/mealplan' . '?week=' . FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->recipe_id)->name
				);
			}
		}

		return array_merge($stockEvents, $taskEvents, $choreEvents, $batteryEvents, $mealPlanRecipeEvents);
	}
}
