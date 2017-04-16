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
}
