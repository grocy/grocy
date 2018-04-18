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
