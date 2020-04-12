<?php

namespace Grocy\Services;

#use \Grocy\Services\DatabaseService;
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

		if (GROCY_MODE === 'dev')
		{
			$this->PotMain = Translations::fromPoFile(__DIR__ . '/../localization/strings.pot');

			$this->Pot = Translations::fromPoFile(__DIR__ . '/../localization/chore_period_types.pot');
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/chore_assignment_types.pot'));
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/component_translations.pot'));
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/stock_transaction_types.pot'));
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/strings.pot'));
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/userfield_types.pot'));

			if (GROCY_MODE !== 'production')
			{
				$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/demo_data.pot'));
			}
		}

		$this->PoUserStrings = new Translations();
		$this->PoUserStrings->setDomain('grocy/userstrings');

		$this->Po = Translations::fromPoFile(__DIR__ . "/../localization/$culture/strings.po");
		if (file_exists(__DIR__ . "/../localization/$culture/chore_assignment_types.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/chore_assignment_types.po"));
		}
		if (file_exists(__DIR__ . "/../localization/$culture/component_translations.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/component_translations.po"));
		}
		if (file_exists(__DIR__ . "/../localization/$culture/stock_transaction_types.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/stock_transaction_types.po"));
		}
		if (file_exists(__DIR__ . "/../localization/$culture/chore_period_types.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/chore_period_types.po"));
		}
		if (file_exists(__DIR__ . "/../localization/$culture/userfield_types.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/userfield_types.po"));
		}
		if (GROCY_MODE !== 'production' && file_exists(__DIR__ . "/../localization/$culture/demo_data.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/demo_data.po"));
		}

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

				$this->PoUserStrings[] = $translation;
			}
			$this->Po = $this->Po->mergeWith($this->PoUserStrings);
		}

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
			if ($this->Pot->find('', $text) === false && $this->PoUserStrings->find('', $text) === false && empty($text) === false)
			{
				$translation = new Translation('', $text);
				$this->PotMain[] = $translation;
				$this->PotMain->toPoFile(__DIR__ . '/../localization/strings.pot');
			}
		}
	}
}
