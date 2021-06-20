<?php

namespace Grocy\Services;

use Gettext\Translation;
use Gettext\Translations;
use Gettext\Translator;

class LocalizationService
{
	const DOMAIN = 'grocy/userstrings';

	protected $Po;

	protected $PoUserStrings;

	protected $Pot;

	protected $PotMain;

	protected $Translator;

	private static $instanceMap = [];

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

	public function GetPoAsJsonString()
	{
		return $this->Po->toJsonString();
	}

	public function __construct(string $culture, $deferLoading = false)
	{
		$this->Culture = $culture;
		$this->PoUserStrings = new Translations();
		$this->PoUserStrings->setDomain(self::DOMAIN);

		if(!$deferLoading) {
			$this->LoadLocalizations($culture);
		}	
	}

	public function __n($number, $singularForm, $pluralForm)
	{
		$this->CheckAndAddMissingTranslationToPot($singularForm);

		return sprintf($this->Translator->ngettext($singularForm, $pluralForm, $number), $number);
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

	public static function getInstance(string $culture)
	{
		if (!in_array($culture, self::$instanceMap))
		{
			self::$instanceMap[$culture] = new self($culture);
		}

		return self::$instanceMap[$culture];
	}

	protected function getDatabaseService()
	{
		return DatabaseService::getInstance();
	}

	protected function getdatabase()
	{
		return $this->getDatabaseService()->GetDbConnection();
	}

	public function LoadLocalizations($includeDynamic = true)
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
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/permissions.pot'));
			$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/locales.pot'));

			if (GROCY_MODE !== 'production')
			{
				$this->Pot = $this->Pot->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/demo_data.pot'));
			}
		}

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

		if (file_exists(__DIR__ . "/../localization/$culture/permissions.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/permissions.po"));
		}

		if (file_exists(__DIR__ . "/../localization/$culture/locales.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/locales.po"));
		}

		if (GROCY_MODE !== 'production' && file_exists(__DIR__ . "/../localization/$culture/demo_data.po"))
		{
			$this->Po = $this->Po->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/demo_data.po"));
		}

		if($includeDynamic)
		{
			$this->LoadDynamicLocalizations();
		}

		$this->Translator = new Translator();
		$this->Translator->loadTranslations($this->Po);
	}

	public function LoadDynamicLocalizations() {
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

			if($this->Po == null)
			{
				$this->Po = new Translations();
				$this->Po->setDomain(self::DOMAIN);
			}

			$this->Po = $this->Po->mergeWith($this->PoUserStrings);
		}
	}
}
