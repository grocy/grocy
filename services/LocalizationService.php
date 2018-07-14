<?php

namespace Grocy\Services;

class LocalizationService
{
	const DEFAULT_CULTURE = 'en';

	public function __construct(string $culture)
	{
		$this->Culture = $culture;

		$this->StringsDefaultCulture = $this->LoadLocalizationFile(self::DEFAULT_CULTURE);
		$this->StringsCurrentCulture = $this->LoadLocalizationFile($culture);
		$this->StringsMerged = array_merge($this->StringsDefaultCulture, $this->StringsCurrentCulture);
	}

	protected $Culture;
	protected $StringsDefaultCulture;
	protected $StringsCurrentCulture;
	protected $StringsMerged;

	private function LoadLocalizationFile(string $culture)
	{
		$file = __DIR__ . "/../localization/$culture.php";

		if (file_exists($file))
		{
			return require $file;
		}
		else
		{
			return array();
		}
	}

	private function LogMissingLocalization(string $culture, string $text)
	{
		$file = __DIR__ . "/../data/missing_translations_$culture.json";

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
		if (MODE === 'dev')
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
