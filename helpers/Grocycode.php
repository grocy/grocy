<?php

namespace Grocy\Helpers;

/**
 * A class that abstracts Grocycode.
 *
 * Grocycode is a simple, easily serializable format to reference
 * stuff within Grocy. It consists of n (n ≥ 3) double-colon seperated parts:
 *
 *  1. The magic `grcy`
 *  2. A type identifer, must match `[a-z]+` (i.e. only lowercase ascii, minimum length 1 character)
 *  3. An object id
 *  4. Any number of further data fields, double-colon seperated.
 *
 * @author Katharina Bogad <katharina@hacked.xyz>
 */
class Grocycode
{
	public const PRODUCT = 'p';
	public const BATTERY = 'b';
	public const CHORE = 'c';
	public const RECIPE = 'r';
	public const MAGIC = 'grcy';

	public function __construct(...$args)
	{
		$argc = count($args);
		if ($argc == 1)
		{
			$this->setFromCode($args[0]);
			return;
		}
		elseif ($argc == 2 || $argc == 3)
		{
			if ($argc == 2)
			{
				$args[] = [];
			}
			$this->setFromData($args[0], $args[1], $args[2]);
			return;
		}

		throw new \Exception('No suitable overload found.');
	}

	public static $Items = [self::PRODUCT, self::BATTERY, self::CHORE, self::RECIPE];
	private $type;
	private $id;
	private $extra_data = [];

	public static function Validate(string $code)
	{
		try
		{
			$gc = new self($code);
			return true;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	public function GetId()
	{
		return $this->id;
	}

	public function GetExtraData()
	{
		return $this->extra_data;
	}

	public function GetType()
	{
		return $this->type;
	}

	public function __toString(): string
	{
		$arr = array_merge([self::MAGIC, $this->type, $this->id], $this->extra_data);

		return implode(':', $arr);
	}

	private function setFromCode($code)
	{
		$parts = array_reverse(explode(':', $code));
		if (array_pop($parts) != self::MAGIC)
		{
			throw new \Exception('Not a Grocycode');
		}

		if (!in_array($this->type = array_pop($parts), self::$Items))
		{
			throw new \Exception('Unknown Grocycode type');
		}

		$this->id = array_pop($parts);
		$this->extra_data = array_reverse($parts);
	}

	private function setFromData($type, $id, $extra_data = [])
	{
		if (!is_array($extra_data))
		{
			throw new \Exception('Extra data must be array of string');
		}
		if (!in_array($type, self::$Items))
		{
			throw new \Exception('Unknown Grocycode type');
		}

		$this->type = $type;
		$this->id = $id;
		$this->extra_data = $extra_data;
	}
}
