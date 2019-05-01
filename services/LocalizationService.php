<?php

namespace Grocy\Services;

use Gettext\Translations;
use Gettext\Translator;

class LocalizationService
{
	public function __construct(string $culture)
	{
		$this->Culture = $culture;
		$this->LoadLocalizations($culture);
	}

	protected $PotTranslation;
	protected $PoTranslation;
	public $Translator;

	private function LoadLocalizations()
	{
		$culture = $this->Culture;

		$this->PotTranslation = Translations::fromPoFile(__DIR__ . '/../localization/chore_types.pot');
		$this->PotTranslation = $this->PotTranslation->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/component_translations.pot'));
		$this->PotTranslation = $this->PotTranslation->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/demo_data.pot'));
		$this->PotTranslation = $this->PotTranslation->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/stock_transaction_types.pot'));
		$this->PotTranslation = $this->PotTranslation->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/strings.pot'));
		$this->PotTranslation = $this->PotTranslation->mergeWith(Translations::fromPoFile(__DIR__ . '/../localization/userfield_types.pot'));

		$this->PoTranslation = Translations::fromPoFile(__DIR__ . "/../localization/$culture/chore_types.po");
		$this->PoTranslation = $this->PoTranslation->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/component_translations.po"));
		$this->PoTranslation = $this->PoTranslation->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/demo_data.po"));
		$this->PoTranslation = $this->PoTranslation->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/stock_transaction_types.po"));
		$this->PoTranslation = $this->PoTranslation->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/strings.po"));
		$this->PoTranslation = $this->PoTranslation->mergeWith(Translations::fromPoFile(__DIR__ . "/../localization/$culture/userfield_types.po"));

		$this->Translator = new Translator();
		$this->Translator->loadTranslations($this->PoTranslation);
		$this->Translator->register();
	}

	public function GetTranslationsForJavaScriptTranslator()
	{
		return $this->PoTranslation->toJsonString();
	}

	//TODO: Missing translations should be automatically added to the source POT file

	public function __t(string $text, ...$placeholderValues)
	{
		return __($text, ...$placeholderValues);
	}

	public function __n($number, $singularForm, $pluralForm)
	{
		return $this->Translator->ngettext($singularForm, $pluralForm, $number);
	}
}
