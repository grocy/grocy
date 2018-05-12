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
