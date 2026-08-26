<?php

namespace Grocy\Helpers;

class EInvalidConfig extends \Exception
{
	public function __construct(string $message)
	{
		$message .= '<br><br>----------<br>Check your "config.php" file (which is in your data directory: "' . realpath(GROCY_DATAPATH) . '").<br>Defaults can be found in "config-dist.php" (which is in your main Grocy directory: "' . realpath(__DIR__ . '/../') . '").';

		parent::__construct($message);
	}
}

class ConfigurationValidator
{
	public function validateConfig()
	{
		self::CheckMode();
		self::CheckDefaultLocale();
		self::CheckCurrencyFormat();
		self::CheckFirstDayOfWeek();
		self::CheckEntryPage();
		self::CheckMealplanFirstDayOfWeek();
		self::CheckAutoNightModeRange();
		self::CheckAuthClass();
	}

	private function CheckMode()
	{
		$allowedModes = ['production', 'dev', 'demo', 'prerelease'];
		if (!in_array(GROCY_MODE, $allowedModes))
		{
			throw new EInvalidConfig('Invalid mode "' . GROCY_MODE . '" set, only ' . implode(', ', $allowedModes) . ' allowed');
		}
	}

	private function CheckDefaultLocale()
	{
		if (!file_exists(__DIR__ . '/../localization/' . GROCY_DEFAULT_LOCALE))
		{
			throw new EInvalidConfig('Invalid locale "' . GROCY_DEFAULT_LOCALE . '" set, locale needs to exist in folder localization');
		}
	}

	private function CheckFirstDayOfWeek()
	{
		if (!(GROCY_CALENDAR_FIRST_DAY_OF_WEEK == '' ||
			(is_numeric(GROCY_CALENDAR_FIRST_DAY_OF_WEEK) && GROCY_CALENDAR_FIRST_DAY_OF_WEEK >= 0 && GROCY_CALENDAR_FIRST_DAY_OF_WEEK <= 6)))
		{
			throw new EInvalidConfig('Invalid value for CALENDAR_FIRST_DAY_OF_WEEK');
		}
	}

	private function CheckCurrencyFormat()
	{
		if (!(preg_match('/^([A-z]){3}$/', GROCY_CURRENCY)))
		{
			throw new EInvalidConfig('CURRENCY is not in ISO 4217 format (three letter code)');
		}
	}

	private function CheckEntryPage()
	{
		$allowedPages = ['stock', 'shoppinglist', 'recipes', 'chores', 'tasks', 'batteries', 'equipment', 'calendar', 'mealplan'];
		if (!in_array(GROCY_ENTRY_PAGE, $allowedPages))
		{
			throw new EInvalidConfig('Invalid entry page "' . GROCY_ENTRY_PAGE . '" set, only ' . implode(', ', $allowedPages) . ' allowed');
		}
	}

	private function CheckMealplanFirstDayOfWeek()
	{
		if (!(GROCY_MEAL_PLAN_FIRST_DAY_OF_WEEK == '' ||
			(is_numeric(GROCY_MEAL_PLAN_FIRST_DAY_OF_WEEK) && GROCY_MEAL_PLAN_FIRST_DAY_OF_WEEK >= -1 && GROCY_MEAL_PLAN_FIRST_DAY_OF_WEEK <= 6)))
		{
			throw new EInvalidConfig('Invalid value for MEAL_PLAN_FIRST_DAY_OF_WEEK');
		}
	}

	private function CheckAutoNightModeRange()
	{
		global $GROCY_DEFAULT_USER_SETTINGS;
		if (!(preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $GROCY_DEFAULT_USER_SETTINGS['auto_night_mode_time_range_from'])))
		{
			throw new EInvalidConfig('auto_night_mode_time_range_from is not in HH:mm format (' . $GROCY_DEFAULT_USER_SETTINGS['auto_night_mode_time_range_from'] . ')');
		}
		if (!(preg_match('/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/', $GROCY_DEFAULT_USER_SETTINGS['auto_night_mode_time_range_to'])))
		{
			throw new EInvalidConfig('auto_night_mode_time_range_to is not in HH:mm format (' . $GROCY_DEFAULT_USER_SETTINGS['auto_night_mode_time_range_to'] . ')');
		}
	}

	private function CheckAuthClass()
	{
		if (!class_exists(GROCY_AUTH_CLASS))
		{
			throw new EInvalidConfig('Configured AUTH_CLASS "' . GROCY_AUTH_CLASS . '" does not exist');
		}
	}
}
