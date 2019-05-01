<?php

namespace Grocy\Services;

use Gettext\Translations;
use Gettext\Translator;

class LocalizationService
{
	public function __construct(string $culture)
	{
		if (GROCY_MODE === 'dev')
		{
			$this->MigrateTranslationsToGettext();
		}

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

	public function __t(string $text, ...$placeholderValues)
	{
		return __($text, ...$placeholderValues);
	}

	public function __n($number, $singularForm, $pluralForm)
	{
		return $this->Translator->ngettext($singularForm, $pluralForm, $number);
	}

	private function MigrateTranslationsToGettext()
	{
		foreach (glob(__DIR__ . '/../localization/*', GLOB_ONLYDIR) as $langDir)
		{
			$lang = basename($langDir);

			foreach (glob(__DIR__ . "/../localization/$lang/*.php") as $phpArrayFile)
			{
				$langStrings = require $phpArrayFile;

				$translations = new \Gettext\Translations();
				$translations->setDomain('grocy/' . pathinfo($phpArrayFile)['filename']);
				$translations->setHeader('Last-Translator', 'Translation migration from old PHP array files');
				$translations->setHeader('Language', $lang);
				$translations->setHeader('Language-Team', "http://www.transifex.com/grocy/grocy/language/$lang");

				$poFileName = basename($phpArrayFile);
				$poFileName = str_replace('.php', '.po', $poFileName);
				$poFilePath = __DIR__ . "/../localization/$lang/$poFileName";
				if (!file_exists($poFilePath))
				{
					$translations->toPoFile($poFilePath);
				}

				$translations = \Gettext\Translations::fromPoFile($poFilePath);
				foreach ($langStrings as $langString => $langStringTranslated)
				{
					$translation = new \Gettext\Translation('', str_replace('#1', '%s', str_replace('#2', '%s', str_replace('#3', '%s', $langString))));
					$translation->setTranslation(str_replace('#1', '%s', str_replace('#2', '%s', str_replace('#3', '%s', $langStringTranslated))));
					$translations[] = $translation;
				}
				$translations->toPoFile($poFilePath);

				if ($lang == 'en')
				{
					$translations = new \Gettext\Translations();
					$translations->setDomain('grocy/' . pathinfo($phpArrayFile)['filename']);
					$translations->setHeader('Last-Translator', 'Translation migration from old PHP array files');
					$translations->setHeader('Language', $lang);
					$translations->setHeader('Language-Team', "http://www.transifex.com/grocy/grocy/language/$lang");

					$potFileName = basename($phpArrayFile);
					$potFileName = str_replace('.php', '.pot', $potFileName);
					$potFileName = __DIR__ . "/../localization/$potFileName";

					foreach ($langStrings as $langString => $langStringTranslated)
					{
						$translation = new \Gettext\Translation('', str_replace('#1', '%s', str_replace('#2', '%s', str_replace('#3', '%s', $langString))));
						$translations[] = $translation;
					}

					if (!file_exists($potFileName))
					{
						$translations->toPoFile($potFileName);
					}
				}
			}
		}
	}
}
