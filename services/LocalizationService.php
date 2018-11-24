<?php

namespace Grocy\Services;

class LocalizationService
{
	const DEFAULT_CULTURE = 'en';

	public function __construct(string $culture)
	{
		$this->Culture = $culture;

		$this->StringsDefaultCulture = $this->LoadLocalizations(self::DEFAULT_CULTURE);
		$this->StringsCurrentCulture = $this->LoadLocalizations($culture);
		$this->StringsMerged = array_merge($this->StringsDefaultCulture, $this->StringsCurrentCulture);
	}

	protected $Culture;
	protected $StringsDefaultCulture;
	protected $StringsCurrentCulture;
	protected $StringsMerged;

	private function LoadLocalizations(string $culture)
	{
		$folder = __DIR__ . "/../localization/$culture/";

		$localizationFiles = array(
			'strings.php',
			'stock_transaction_types.php',
			'chore_types.php',
			'component_translations.php',
			'demo_data.php'
		);

		$stringsCombined = array();
		foreach ($localizationFiles as $localizationFile)
		{
			$file = $folder . $localizationFile;
			if (file_exists($file))
			{
				$currentStrings = require $file;
				$stringsCombined = array_merge($stringsCombined, $currentStrings);
			}
		}

		return $stringsCombined;
	}

	public function LogMissingLocalization(string $culture, string $text)
	{
		$file = GROCY_DATAPATH . "/missing_translations_$culture.json";

		$missingTranslations = array();
		if (file_exists($file))
		{
			$missingTranslations = json_decode(file_get_contents($file), true);
		}

		if (!array_key_exists($text, $missingTranslations))
		{
			$missingTranslations[$text] = '#TranslationMissing#';
		}

		if (count($missingTranslations) > 0)
		{
			file_put_contents($file, json_encode($missingTranslations, JSON_PRETTY_PRINT));
		}
	}

	public function Localize(string $text, ...$placeholderValues)
	{
		if (GROCY_MODE === 'dev')
		{
			if (!array_key_exists($text, $this->StringsDefaultCulture))
			{
				$this->LogMissingLocalization(self::DEFAULT_CULTURE, $text);
			}

			if (!array_key_exists($text, $this->StringsCurrentCulture))
			{
				$this->LogMissingLocalization($this->Culture, $text);
			}
		}

		$localizedText = $text;
		if (array_key_exists($text, $this->StringsMerged))
		{
			$localizedText = $this->StringsMerged[$text];
		}

		for ($i = 0; $i < count($placeholderValues); $i++)
		{
			$localizedText = str_replace('#' . ($i + 1), $placeholderValues[$i], $localizedText);
		}

		return $localizedText;
	}

	public function LocalizeForSqlString(string $text, ...$placeholderValues)
	{
		$localizedText = $this->Localize($text, $placeholderValues);
		return str_replace("'", "''", $localizedText);
	}

	public function GetLocalizations()
	{
		return $this->StringsMerged;
	}

	public function GetDefaultCultureLocalizations()
	{
		return $this->StringsDefaultCulture;
	}

	public function GetCurrentCultureLocalizations()
	{
		return $this->StringsCurrentCulture;
	}
}
