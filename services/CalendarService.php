<?php

namespace Grocy\Services;

use \Grocy\Services\StockService;
use \Grocy\Services\TasksService;
use \Grocy\Services\ChoresService;
use \Grocy\Services\BatteriesService;
use \Grocy\Services\UsersService;
use \Grocy\Helpers\UrlManager;

class CalendarService extends BaseService
{
	public function __construct()
	{
		parent::__construct();
		$this->UrlManager = new UrlManager(GROCY_BASE_URL);
	}

	protected function getStockservice()
	{
		return StockService::getInstance();
	}

	protected function getTasksService()
	{
		return TasksService::getInstance();
	}

	protected function getChoresService()
	{
		return ChoresService::getInstance();
	}

	protected function getBatteriesService()
	{
		return BatteriesService::getInstance();
	}

	protected function getUsersService()
	{
		return UsersService::getInstance();
	}

	public function GetEvents()
	{
		$products = $this->getDatabase()->products();
		$titlePrefix = $this->getLocalizationService()->__t('Product expires') . ': ';
		$stockEvents = array();
		foreach($this->getStockService()->GetCurrentStock() as $currentStockEntry)
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

		$titlePrefix = $this->getLocalizationService()->__t('Task due') . ': ';
		$taskEvents = array();
		foreach($this->getTasksService()->GetCurrent() as $currentTaskEntry)
		{
			$taskEvents[] = array(
				'title' => $titlePrefix . $currentTaskEntry->name,
				'start' => $currentTaskEntry->due_date,
				'date_format' => 'date'
			);
		}

		$users = $this->getUsersService()->GetUsersAsDto();

		$chores = $this->getDatabase()->chores();
		$titlePrefix = $this->getLocalizationService()->__t('Chore due') . ': ';
		$choreEvents = array();
		foreach($this->ChoresService->GetCurrent() as $currentChoreEntry)
		{
			$chore = FindObjectInArrayByPropertyValue($chores, 'id', $currentChoreEntry->chore_id);

			$assignedToText = '';
			if (!empty($currentChoreEntry->next_execution_assigned_to_user_id))
			{
				$assignedToText = ' (' . $this->getLocalizationService()->__t('assigned to %s', FindObjectInArrayByPropertyValue($users, 'id', $currentChoreEntry->next_execution_assigned_to_user_id)->display_name) . ')';
			}

			$choreEvents[] = array(
				'title' => $titlePrefix . $chore->name . $assignedToText,
				'start' => $currentChoreEntry->next_estimated_execution_time,
				'date_format' => 'datetime'
			);
		}

		$batteries = $this->getDatabase()->batteries();
		$titlePrefix = $this->getLocalizationService()->__t('Battery charge cycle due') . ': ';
		$batteryEvents = array();
		foreach($this->getBatteriesService()->GetCurrent() as $currentBatteryEntry)
		{
			$batteryEvents[] = array(
				'title' => $titlePrefix . FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name,
				'start' => $currentBatteryEntry->next_estimated_charge_time,
				'date_format' => 'datetime'
			);
		}

		$recipes = $this->getDatabase()->recipes();
		$mealPlanDayRecipes = $this->getDatabase()->recipes()->where('type', 'mealplan-day');
		$titlePrefix = $this->getLocalizationService()->__t('Meal plan') . ': ';
		$mealPlanRecipeEvents = array();
		foreach($mealPlanDayRecipes as $mealPlanDayRecipe)
		{
			$recipesOfCurrentDay = $this->getDatabase()->recipes_nestings_resolved()->where('recipe_id = :1 AND includes_recipe_id != :1', $mealPlanDayRecipe->id);
			foreach ($recipesOfCurrentDay as $recipeOfCurrentDay)
			{
				$mealPlanRecipeEvents[] = array(
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->includes_recipe_id)->name,
					'start' => FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->recipe_id)->name,
					'date_format' => 'date',
					'description' => $this->UrlManager->ConstructUrl('/mealplan' . '?week=' . FindObjectInArrayByPropertyValue($recipes, 'id', $recipeOfCurrentDay->recipe_id)->name)
				);
			}
		}

		return array_merge($stockEvents, $taskEvents, $choreEvents, $batteryEvents, $mealPlanRecipeEvents);
	}
}
