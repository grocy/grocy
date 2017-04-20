<?php

class GrocyPhpHelper
{
	public static function FindObjectInArrayByPropertyValue($array, $propertyName, $propertyValue)
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

	public static function FindAllObjectsInArrayByPropertyValue($array, $propertyName, $propertyValue, $operator = '==')
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
}
