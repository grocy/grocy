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
		$sum += $object->{$propertyName};
	}

	return $sum;
}

function GetClassConstants($className)
{
	$r = new ReflectionClass($className);
	return $r->getConstants();
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
	if (!defined($name))
	{
		// The content of a $name.txt file in /data/settingoverrides can overwrite the given setting (for embedded mode)
		$settingOverrideFile = DATAPATH . '/settingoverrides/' . $name . '.txt';
		if (file_exists($settingOverrideFile))
		{
			define($name, file_get_contents($settingOverrideFile));
		}
		else
		{
			define($name, $value);
		}
	}
}
