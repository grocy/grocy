<?php

namespace Grocy\Services;

use \Grocy\Services\DatabaseService;
use \Gettext\Translation;
use \Gettext\Translations;
use \Gettext\Translator;

class LocalizationService
{

	private static $instanceMap = array();

	public function __construct(string $culture)
	{
		$this->Culture = $culture;

		$this->LoadLocalizations($culture);
	}


	protected function getDatabaseService()
    {
        return DatabaseService::getInstance();
    }

    protected function getdatabase()
    {
        return $this->getDatabaseService()->GetDbConnection();
    }

    public static function getInstance(string $culture)
	{
		if (!in_array($culture, self::$instanceMap))
		{
			self::$instanceMap[$culture] = new self($culture);
		}

		return self::$instanceMap[$culture];
	}

	protected $Pot;
	protected $PotMain;
	protected $Po;
	protected $PoUserStrings;
	protected $Translator;

	private function LoadLocalizations()
	{
		$culture = $this->Culture;

		if (!(apcu_exists("grocy_LocalizationService_".$culture."_Pot") &&
			apcu_exists("grocy_LocalizationService_".$culture."_Po") &&
			apcu_exists("grocy_LocalizationService_".$culture."_PoUserStrings") &&
			apcu_exists("grocy_LocalizationService_".$culture."_PotMain")
		))
		{

			$Pot = null;
			$PotMain = null;

			if (GROCY_MODE === 'dev')
			{
				$PotMain = Translations::fromPoFile(__DIR__ . '/../localization/strings.pot');

				$Pot = Translations::fromPoFile(__DIR__ . '/../localization/chore_period_types.pot');
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/chore_assignment_types.pot'));
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/component_translations.pot'));
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/demo_data.pot'));
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/stock_transaction_types.pot'));
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/strings.pot'));
				$Pot = $Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/userfield_types.pot'));
			}

			$PoUserStrings = new Translations();
			$PoUserStrings->setDomain('grocy/userstrings');

			$Po = Translations::fromPoFile(__DIR__ . "/../localization/$culture/chore_period_types.po");
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/chore_assignment_types.po"));
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/component_translations.po"));
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/demo_data.po"));
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/stock_transaction_types.po"));
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/strings.po"));
			$Po = $Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/userfield_types.po"));

			$quantityUnits = null;
			try
			{
				$quantityUnits = $this->getDatabase()->quantity_units()->fetchAll();
			}
			catch (\Exception $ex)
			{
				// Happens when database is not initialised or migrated...
			}

			if ($quantityUnits !== null)
			{
				foreach ($quantityUnits as $quantityUnit)
				{
					$translation = new Translation('', $quantityUnit['name']);
					$translation->setTranslation($quantityUnit['name']);
					$translation->setPlural($quantityUnit['name_plural']);
					$translation->setPluralTranslations(preg_split('/\r\n|\r|\n/', $quantityUnit['plural_forms']));

					$PoUserStrings[] = $translation;
				}
				$Po = $Po->mergeWith($PoUserStrings);
			}
			apcu_store("grocy_LocalizationService_".$culture."_Po", $Po);
			apcu_store("grocy_LocalizationService_".$culture."_Pot", $Pot);
			apcu_store("grocy_LocalizationService_".$culture."_PoUserStrings", $PoUserStrings);
			apcu_store("grocy_LocalizationService_".$culture."_PotMain", $PotMain);
		}

		$this->Pot = apcu_fetch("grocy_LocalizationService_".$culture."_Pot");
		$this->PotMain = apcu_fetch("grocy_LocalizationService_".$culture."_PotMain");
		$this->Po = apcu_fetch("grocy_LocalizationService_".$culture."_Po");
		$this->PoUserStrings = apcu_fetch("grocy_LocalizationService_".$culture."_PoUserStrings");

		$this->Translator = new Translator();
		$this->Translator->loadTranslations($this->Po);
	}

	public function GetPoAsJsonString()
	{
		return $this->Po->toJsonString();
	}

	public function GetPluralCount()
	{
		if ($this->Po->getHeader(Translations::HEADER_PLURAL) !== null)
		{
			return $this->Po->getPluralForms()[0];
		}
		else
		{
			return 2;
		}
	}

	public function GetPluralDefinition()
	{
		if ($this->Po->getHeader(Translations::HEADER_PLURAL) !== null)
		{
			return $this->Po->getPluralForms()[1];
		}
		else
		{
			return '(n != 1)';
		}
	}

	public function __t($text, ...$placeholderValues)
	{
		$this->CheckAndAddMissingTranslationToPot($text);

		if (func_num_args() === 1)
		{
			return $this->Translator->gettext($text);
		}
		else
		{
			return vsprintf($this->Translator->gettext($text), ...$placeholderValues);
		}
	}

	public function __n($number, $singularForm, $pluralForm)
	{
		$this->CheckAndAddMissingTranslationToPot($singularForm);

		return sprintf($this->Translator->ngettext($singularForm, $pluralForm, $number), $number);
	}

	public function CheckAndAddMissingTranslationToPot($text)
	{
		if (GROCY_MODE === 'dev')
		{
			if ($this->Pot->find('', $text) === false && $this->PoUserStrings->find('', $text) === false)
			{
				$translation = new Translation('', $text);
				$this->PotMain[] = $translation;
				$this->PotMain->toPoFile(__DIR__ . '/../localization/strings.pot');
			}
		}
	}
}
