<?php

function FindObjectInArrayByPropertyValue($array, $propertyName, $propertyValue)
{
	foreach($array as $object)
	{
		if($object->{$propertyName} == $propertyValue)
		{
			return $object;
		}
	}

	return null;
}

function FindAllObjectsInArrayByPropertyValue($array, $propertyName, $propertyValue, $operator = '==')
{
	$returnArray = array();

	foreach($array as $object)
	{
		switch($operator)
		{
			case '==':
				if($object->{$propertyName} == $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
			case '>':
				if($object->{$propertyName} > $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
			case '<':
				if($object->{$propertyName} < $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
		}
	}

	return $returnArray;
}

function FindAllItemsInArrayByValue($array, $value, $operator = '==')
{
	$returnArray = array();

	foreach($array as $item)
	{
		switch($operator)
		{
			case '==':
				if($item == $value)
				{
					$returnArray[] = $item;
				}
				break;
			case '>':
				if($item > $value)
				{
					$returnArray[] = $item;
				}
				break;
			case '<':
				if($item < $value)
				{
					$returnArray[] = $item;
				}
				break;
		}
	}

	return $returnArray;
}

function SumArrayValue($array, $propertyName)
{
	$sum = 0;
	foreach($array as $object)
	{
		$sum += floatval($object->{$propertyName});
	}

	return $sum;
}

function GetClassConstants($className, $prefix = null)
{
	$r = new ReflectionClass($className);
	$constants = $r->getConstants();

	if ($prefix === null)
	{
		return $constants;
	}
	else
	{
		$matchingKeys = preg_grep('!^' . $prefix . '!', array_keys($constants));
		return array_intersect_key($constants, array_flip($matchingKeys));
	}
}

function RandomString($length, $allowedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$randomString = '';
	for ($i = 0; $i < $length; $i++)
	{
		$randomString .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
	}

	return $randomString;
}

function IsAssociativeArray(array $array)
{
	$keys = array_keys($array);
	return array_keys($keys) !== $keys;
}

function IsIsoDate($dateString)
{
	$d = DateTime::createFromFormat('Y-m-d', $dateString);
	return $d && $d->format('Y-m-d') === $dateString;
}

function IsIsoDateTime($dateTimeString)
{
	$d = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
	return $d && $d->format('Y-m-d H:i:s') === $dateTimeString;
}

function BoolToString(bool $bool)
{
	return $bool ? 'true' : 'false';
}

function Setting(string $name, $value)
{
	if (!defined('GROCY_' . $name))
	{
		// The content of a $name.txt file in /data/settingoverrides can overwrite the given setting (for embedded mode)
		$settingOverrideFile = GROCY_DATAPATH . '/settingoverrides/' . $name . '.txt';
		if (file_exists($settingOverrideFile))
		{
			define('GROCY_' . $name, file_get_contents($settingOverrideFile));
		}
		elseif (getenv('GROCY_' . $name) !== false) // An environment variable with the same name and prefix GROCY_ overwrites the given setting
		{
			if (strtolower(getenv('GROCY_' . $name)) === "true")
			{
				define('GROCY_' . $name, true);
			}
			elseif (strtolower(getenv('GROCY_' . $name)) === "false")
			{
				define('GROCY_' . $name, false);
			}
			else
			{
				define('GROCY_' . $name, getenv('GROCY_' . $name));
			}
		}
		else
		{
			define('GROCY_' . $name, $value);
		}
	}
}

global $GROCY_DEFAULT_USER_SETTINGS;
$GROCY_DEFAULT_USER_SETTINGS = array();
function DefaultUserSetting(string $name, $value)
{
	global $GROCY_DEFAULT_USER_SETTINGS;
	if (!array_key_exists($name, $GROCY_DEFAULT_USER_SETTINGS))
	{
		$GROCY_DEFAULT_USER_SETTINGS[$name] = $value;
	}
}

function GetUserDisplayName($user)
{
	$displayName = '';

	if (empty($user->first_name) && !empty($user->last_name))
	{
		$displayName = $user->last_name;
	}
	elseif (empty($user->last_name) && !empty($user->first_name))
	{
		$displayName = $user->first_name;
	}
	elseif (!empty($user->last_name) && !empty($user->first_name))
	{
		$displayName = $user->first_name . ' ' . $user->last_name;
	}
	else
	{
		$displayName = $user->username;
	}

	return $displayName;
}

function IsValidFileName($fileName)
{
	if(preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $fileName))
	{
		return true;
	}

	return false;
}

function IsJsonString($text)
{
	json_decode($text);
	return (json_last_error() == JSON_ERROR_NONE);
}

function string_starts_with($haystack, $needle)
{
	return (substr($haystack, 0, strlen($needle)) === $needle);
}

function string_ends_with($haystack, $needle)
{
	$length = strlen($needle);
	if ($length == 0)
	{
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

function FractionToDecimal($fraction)
{
	// Split fraction into whole number and fraction components
	preg_match('/^(?P<whole>\d+)?\s?((?P<numerator>\d+)\/(?P<denominator>\d+))?$/', $fraction, $components);

	// Extract whole number, numerator, and denominator components
	$whole = $components['whole'] ?: 0;
	$numerator = $components['numerator'] ?: 0;
	$denominator = $components['denominator'] ?: 0;

	// Create decimal value
	$decimal = $whole;
	$numerator && $denominator && $decimal += ($numerator/$denominator);

	return $decimal;
}

function DecimalToFraction($decimal)
{
	// Determine decimal precision and extrapolate multiplier required to convert to integer
	$precision = strpos(strrev($decimal), '.') ?: 0;
	$multiplier = pow(10, $precision);

	// Calculate initial numerator and denominator
	$numerator = $decimal * $multiplier;
	$denominator = 1 * $multiplier;

	// Extract whole number from numerator
	$whole = floor($numerator / $denominator);
	$decimal = $decimal - $whole;

	//round to manage 1/3
	$tolerance = 1.e-4;

	$h2 = 0;
	$numerator = 1;
	$denominator = 0;
	$k2 = 1;
	$b = 1 / $decimal;
	do {
		$b = 1 / $b;
		$a = floor($b);
		$aux = $numerator;
		$numerator = $a * $numerator + $h2;
		$h2 = $aux;
		$aux = $denominator;
		$denominator = $a * $denominator + $k2;
		$k2 = $aux;
		$b = $b - $a;
	} while (abs($decimal - $numerator / $denominator) > $decimal * $tolerance);

	// Create fraction value
	$fraction = [];
	$whole && $fraction[] = $whole;
	$numerator && $fraction[] = "{$numerator}/{$denominator}";

	return implode(' ', $fraction);
}
