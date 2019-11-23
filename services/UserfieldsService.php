<?php

namespace Grocy\Services;

class UserfieldsService extends BaseService
{
	const USERFIELD_TYPE_SINGLE_LINE_TEXT = 'text-single-line';
	const USERFIELD_TYPE_SINGLE_MULTILINE_TEXT = 'text-multi-line';
	const USERFIELD_TYPE_INTEGRAL_NUMBER = 'number-integral';
	const USERFIELD_TYPE_DECIMAL_NUMBER = 'number-decimal';
	const USERFIELD_TYPE_DATE = 'date';
	const USERFIELD_TYPE_DATETIME = 'datetime';
	const USERFIELD_TYPE_CHECKBOX = 'checkbox';
	const USERFIELD_TYPE_PRESET_LIST = 'preset-list';
	const USERFIELD_TYPE_PRESET_CHECKLIST = 'preset-checklist';
	const USERFIELD_TYPE_LINK = 'link';

	public function __construct()
	{
		parent::__construct();
	}

	protected $OpenApiSpec = null;

	protected function getOpenApispec()
    {
        if($this->OpenApiSpec == null)
        {
            $this->OpenApiSpec = json_decode(file_get_contents(__DIR__ . '/../grocy.openapi.json'));
        }
        return $this->OpenApiSpec;
    }

	public function GetFields($entity)
	{
		if (!$this->IsValidEntity($entity))
		{
			throw new \Exception('Entity does not exist or is not exposed');
		}

		return $this->getDatabase()->userfields()->where('entity', $entity)->orderBy('name')->fetchAll();
	}

	public function GetField($fieldId)
	{
		return $this->getDatabase()->userfields($fieldId);
	}

	public function GetAllFields()
	{
		return $this->getDatabase()->userfields()->orderBy('name')->fetchAll();
	}

	public function GetValues($entity, $objectId)
	{
		if (!$this->IsValidEntity($entity))
		{
			throw new \Exception('Entity does not exist or is not exposed');
		}

		$userfields = $this->getDatabase()->userfield_values_resolved()->where('entity = :1 AND object_id = :2', $entity, $objectId)->orderBy('name')->fetchAll();
		$userfieldKeyValuePairs = array();
		foreach ($userfields as $userfield)
		{
			$userfieldKeyValuePairs[$userfield->name] = $userfield->value;
		}

		return $userfieldKeyValuePairs;
	}

	public function GetAllValues($entity)
	{
		if (!$this->IsValidEntity($entity))
		{
			throw new \Exception('Entity does not exist or is not exposed');
		}

		return $this->getDatabase()->userfield_values_resolved()->where('entity', $entity)->orderBy('name')->fetchAll();
	}

	public function SetValues($entity, $objectId, $userfields)
	{
		if (!$this->IsValidEntity($entity))
		{
			throw new \Exception('Entity does not exist or is not exposed');
		}

		foreach ($userfields as $key => $value)
		{
			$fieldRow = $this->getDatabase()->userfields()->where('entity = :1 AND name = :2', $entity, $key)->fetch();

			if ($fieldRow === null)
			{
				throw new \Exception("Field $key is not a valid userfield of the given entity");
			}

			$fieldId = $fieldRow->id;

			$alreadyExistingEntry = $this->getDatabase()->userfield_values()->where('field_id = :1 AND object_id = :2', $fieldId, $objectId)->fetch();
			if ($alreadyExistingEntry) // Update
			{
				$alreadyExistingEntry->update(array(
					'value' => $value
				));
			}
			else // Insert
			{
				$newRow = $this->getDatabase()->userfield_values()->createRow(array(
					'field_id' => $fieldId,
					'object_id' => $objectId,
					'value' => $value
				));
				$newRow->save();
			}
		}
	}

	public function GetEntities()
	{
		$exposedDefaultEntities = $this->getOpenApiSpec()->components->internalSchemas->ExposedEntity->enum;

		$userentities = array();
		foreach ($this->getDatabase()->userentities()->orderBy('name') as $userentity)
		{
			$userentities[] = 'userentity-' . $userentity->name;
		}

		return array_merge($exposedDefaultEntities, $userentities);
	}

	public function GetFieldTypes()
	{
		return GetClassConstants('\Grocy\Services\UserfieldsService');
	}

	private function IsValidEntity($entity)
	{
		return in_array($entity, $this->GetEntities());
	}
}
