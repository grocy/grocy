<?php

namespace Grocy\Services;

#use \Grocy\Services\StockService;
#use \Grocy\Services\TasksService;
#use \Grocy\Services\ChoresService;
#use \Grocy\Services\BatteriesService;
#use \Grocy\Services\UsersService;
use \Grocy\Helpers\UrlManager;

class CalendarService extends BaseService
{
	public function __construct()
	{
		parent::__construct();
		$this->UrlManager = new UrlManager(GROCY_BASE_URL);
	}

	public function GetEvents()
	{
		$stockEvents = array();
		if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		{
			$products = $this->getDatabase()->products();
			$titlePrefix = $this->getLocalizationService()->__t('Product expires') . ': ';
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
		}

		$taskEvents = array();
		if (GROCY_FEATURE_FLAG_TASKS)
		{
			$titlePrefix = $this->getLocalizationService()->__t('Task due') . ': ';
			foreach($this->getTasksService()->GetCurrent() as $currentTaskEntry)
			{
				$taskEvents[] = array(
					'title' => $titlePrefix . $currentTaskEntry->name,
					'start' => $currentTaskEntry->due_date,
					'date_format' => 'date'
				);
			}
		}

		$choreEvents = array();
		if (GROCY_FEATURE_FLAG_CHORES)
		{
			$users = $this->getUsersService()->GetUsersAsDto();

			$chores = $this->getDatabase()->chores();
			$titlePrefix = $this->getLocalizationService()->__t('Chore due') . ': ';
			foreach($this->getChoresService()->GetCurrent() as $currentChoreEntry)
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
		}

		$batteryEvents = array();
		if (GROCY_FEATURE_FLAG_BATTERIES)
		{
			$batteries = $this->getDatabase()->batteries();
			$titlePrefix = $this->getLocalizationService()->__t('Battery charge cycle due') . ': ';
			foreach($this->getBatteriesService()->GetCurrent() as $currentBatteryEntry)
			{
				$batteryEvents[] = array(
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name,
					'start' => $currentBatteryEntry->next_estimated_charge_time,
					'date_format' => 'datetime'
				);
			}
		}

		$mealPlanRecipeEvents = array();
		if (GROCY_FEATURE_FLAG_RECIPES)
		{
			$recipes = $this->getDatabase()->recipes();
			$mealPlanDayRecipes = $this->getDatabase()->recipes()->where('type', 'mealplan-day');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan recipe') . ': ';

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

			$mealPlanDayNotes = $this->getDatabase()->meal_plan()->where('type', 'note');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan note') . ': ';
			$mealPlanNotesEvents = array();
			foreach($mealPlanDayNotes as $mealPlanDayNote)
			{
				$mealPlanNotesEvents[] = array(
					'title' => $titlePrefix . $mealPlanDayNote->note,
					'start' => $mealPlanDayNote->day,
					'date_format' => 'date'
				);
			}

			$products = $this->getDatabase()->products();
			$mealPlanDayProducts = $this->getDatabase()->meal_plan()->where('type', 'product');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan product') . ': ';
			$mealPlanProductEvents = array();
			foreach($mealPlanDayProducts as $mealPlanDayProduct)
			{
				$mealPlanProductEvents[] = array(
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($products, 'id', $mealPlanDayProduct->product_id)->name,
					'start' => $mealPlanDayProduct->day,
					'date_format' => 'date'
				);
			}
		}

		return array_merge($stockEvents, $taskEvents, $choreEvents, $batteryEvents, $mealPlanRecipeEvents, $mealPlanNotesEvents, $mealPlanProductEvents);
	}
}
