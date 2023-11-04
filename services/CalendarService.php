<?php

namespace Grocy\Services;

use Grocy\Helpers\UrlManager;

class CalendarService extends BaseService
{
	public function __construct()
	{
		$this->UrlManager = new UrlManager(GROCY_BASE_URL);
	}

	private $UrlManager;

	public function GetEvents()
	{
		$usersService = $this->getUsersService();

		$stockEvents = [];
		if (GROCY_FEATURE_FLAG_STOCK_BEST_BEFORE_DATE_TRACKING)
		{
			$products = $this->getDatabase()->products();
			$titlePrefix = $this->getLocalizationService()->__t('Product due') . ': ';

			foreach ($this->getStockService()->GetCurrentStock() as $currentStockEntry)
			{
				if ($currentStockEntry->amount > 0)
				{
					$stockEvents[] = [
						'title' => $titlePrefix . FindObjectInArrayByPropertyValue($products, 'id', $currentStockEntry->product_id)->name,
						'start' => $currentStockEntry->best_before_date,
						'date_format' => 'date',
						'link' => $this->UrlManager->ConstructUrl('/stockoverview'),
						'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_products']
					];
				}
			}
		}

		$taskEvents = [];
		if (GROCY_FEATURE_FLAG_TASKS)
		{
			$titlePrefix = $this->getLocalizationService()->__t('Task due') . ': ';

			foreach ($this->getTasksService()->GetCurrent() as $currentTaskEntry)
			{
				$taskEvents[] = [
					'title' => $titlePrefix . $currentTaskEntry->name,
					'start' => $currentTaskEntry->due_date,
					'date_format' => 'date',
					'link' => $this->UrlManager->ConstructUrl('/tasks'),
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_tasks']
				];
			}
		}

		$choreEvents = [];
		if (GROCY_FEATURE_FLAG_CHORES)
		{
			$users = $this->getUsersService()->GetUsersAsDto();
			$chores = $this->getDatabase()->chores()->where('active = 1');
			$titlePrefix = $this->getLocalizationService()->__t('Chore due') . ': ';

			foreach ($this->getChoresService()->GetCurrent() as $currentChoreEntry)
			{
				$chore = FindObjectInArrayByPropertyValue($chores, 'id', $currentChoreEntry->chore_id);

				$assignedToText = '';
				if (!empty($currentChoreEntry->next_execution_assigned_to_user_id))
				{
					$assignedToText = ' (' . $this->getLocalizationService()->__t('assigned to %s', FindObjectInArrayByPropertyValue($users, 'id', $currentChoreEntry->next_execution_assigned_to_user_id)->display_name) . ')';
				}

				$choreEvents[] = [
					'title' => $titlePrefix . $chore->name . $assignedToText,
					'start' => $currentChoreEntry->next_estimated_execution_time,
					'date_format' => 'datetime',
					'link' => $this->UrlManager->ConstructUrl('/choresoverview'),
					'allDay' => $chore->track_date_only == 1,
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_chores']
				];
			}
		}

		$batteryEvents = [];
		if (GROCY_FEATURE_FLAG_BATTERIES)
		{
			$batteries = $this->getDatabase()->batteries()->where('active = 1');
			$titlePrefix = $this->getLocalizationService()->__t('Battery charge cycle due') . ': ';

			foreach ($this->getBatteriesService()->GetCurrent() as $currentBatteryEntry)
			{
				$batteryEvents[] = [
					'title' => $titlePrefix . FindObjectInArrayByPropertyValue($batteries, 'id', $currentBatteryEntry->battery_id)->name,
					'start' => $currentBatteryEntry->next_estimated_charge_time,
					'date_format' => 'datetime',
					'link' => $this->UrlManager->ConstructUrl('/batteriesoverview'),
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_batteries']
				];
			}
		}

		$mealPlanRecipeEvents = [];
		$mealPlanNotesEvents = [];
		$mealPlanProductEvents = [];
		if (GROCY_FEATURE_FLAG_RECIPES_MEALPLAN)
		{
			$mealPlanSections = $this->getDatabase()->meal_plan_sections();

			$recipes = $this->getDatabase()->recipes()->where('type', 'normal');
			$mealPlanDayRecipes = $this->getDatabase()->meal_plan()->where('type', 'recipe');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan recipe') . ': ';
			foreach ($mealPlanDayRecipes as $mealPlanDayRecipe)
			{
				$start = $mealPlanDayRecipe->day;
				$dateFormat = 'date';
				$section = FindObjectInArrayByPropertyValue($mealPlanSections, 'id', $mealPlanDayRecipe->section_id);
				if (!empty($section->time_info))
				{
					$start = $mealPlanDayRecipe->day . ' ' . $section->time_info . ':00';
					$dateFormat = 'datetime';
				}

				$titlePrefix2 = '';
				if (!empty($section->name))
				{
					$titlePrefix2 = $section->name . ': ';
				}

				$mealPlanRecipeEvents[] = [
					'title' => $titlePrefix . $titlePrefix2 . FindObjectInArrayByPropertyValue($recipes, 'id', $mealPlanDayRecipe->recipe_id)->name,
					'start' => $start,
					'date_format' => $dateFormat,
					'description' => $this->UrlManager->ConstructUrl('/mealplan' . '?week=' . $mealPlanDayRecipe->day),
					'link' => $this->UrlManager->ConstructUrl('/recipes' . '?recipe=' . $mealPlanDayRecipe->recipe_id . '#fullscreen'),
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_meal_plan']
				];
			}

			$mealPlanDayNotes = $this->getDatabase()->meal_plan()->where('type', 'note');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan note') . ': ';
			foreach ($mealPlanDayNotes as $mealPlanDayNote)
			{
				$start = $mealPlanDayNote->day;
				$dateFormat = 'date';
				$section = FindObjectInArrayByPropertyValue($mealPlanSections, 'id', $mealPlanDayNote->section_id);
				if (!empty($section->time_info))
				{
					$start = $mealPlanDayNote->day . ' ' . $section->time_info . ':00';
					$dateFormat = 'datetime';
				}

				$titlePrefix2 = '';
				if (!empty($section->name))
				{
					$titlePrefix2 = $section->name . ': ';
				}


				$mealPlanNotesEvents[] = [
					'title' => $titlePrefix . $titlePrefix2 . $mealPlanDayNote->note,
					'start' => $start,
					'date_format' => $dateFormat,
					'link' => $this->UrlManager->ConstructUrl('/mealplan' . '?start=' . $start),
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_meal_plan']
				];
			}

			$products = $this->getDatabase()->products();
			$mealPlanDayProducts = $this->getDatabase()->meal_plan()->where('type', 'product');
			$titlePrefix = $this->getLocalizationService()->__t('Meal plan product') . ': ';
			foreach ($mealPlanDayProducts as $mealPlanDayProduct)
			{
				$start = $mealPlanDayProduct->day;
				$dateFormat = 'date';
				$section = FindObjectInArrayByPropertyValue($mealPlanSections, 'id', $mealPlanDayProduct->section_id);
				if (!empty($section->time_info))
				{
					$start = $mealPlanDayProduct->day . ' ' . $section->time_info . ':00';
					$dateFormat = 'datetime';
				}

				$titlePrefix2 = '';
				if (!empty($section->name))
				{
					$titlePrefix2 = $section->name . ': ';
				}

				$mealPlanProductEvents[] = [
					'title' => $titlePrefix . $titlePrefix2 . FindObjectInArrayByPropertyValue($products, 'id', $mealPlanDayProduct->product_id)->name,
					'start' => $start,
					'date_format' => $dateFormat,
					'link' => $this->UrlManager->ConstructUrl('/mealplan' . '?start=' . $start),
					'color' => $usersService->GetUserSettings(GROCY_USER_ID)['calendar_color_meal_plan']
				];
			}
		}

		return array_merge($stockEvents, $taskEvents, $choreEvents, $batteryEvents, $mealPlanRecipeEvents, $mealPlanNotesEvents, $mealPlanProductEvents);
	}
}
