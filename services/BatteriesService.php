<?php

namespace Grocy\Services;

class BatteriesService extends BaseService
{
	public function GetBatteryDetails(int $batteryId)
	{
		if (!$this->BatteryExists($batteryId))
		{
			throw new \Exception('Battery does not exist');
		}

		$battery = $this->getDatabase()->batteries($batteryId);
		$batteryChargeCyclesCount = $this->getDatabase()->battery_charge_cycles()->where('battery_id = :1 AND undone = 0', $batteryId)->count();
		$batteryLastChargedTime = $this->getDatabase()->battery_charge_cycles()->where('battery_id = :1 AND undone = 0', $batteryId)->max('tracked_time');
		$nextChargeTime = $this->getDatabase()->batteries_current()->where('battery_id', $batteryId)->min('next_estimated_charge_time');

		return [
			'battery' => $battery,
			'last_charged' => $batteryLastChargedTime,
			'charge_cycles_count' => $batteryChargeCyclesCount,
			'next_estimated_charge_time' => $nextChargeTime
		];
	}

	public function GetCurrent()
	{
		$batteries = $this->getDatabase()->batteries()->where('active = 1')->orderBy('name', 'COLLATE NOCASE');
		$currentBatteries = $this->getDatabase()->batteries_current();
		foreach ($currentBatteries as $currentBattery)
		{
			$currentBattery->battery = FindObjectInArrayByPropertyValue($batteries, 'id', $currentBattery->battery_id);
		}

		return $currentBatteries;
	}

	public function TrackChargeCycle(int $batteryId, string $trackedTime)
	{
		if (!$this->BatteryExists($batteryId))
		{
			throw new \Exception('Battery does not exist');
		}

		$logRow = $this->getDatabase()->battery_charge_cycles()->createRow([
			'battery_id' => $batteryId,
			'tracked_time' => $trackedTime
		]);
		$logRow->save();

		return $this->getDatabase()->lastInsertId();
	}

	public function UndoChargeCycle($chargeCycleId)
	{
		$logRow = $this->getDatabase()->battery_charge_cycles()->where('id = :1 AND undone = 0', $chargeCycleId)->fetch();

		if ($logRow == null)
		{
			throw new \Exception('Charge cycle does not exist or was already undone');
		}

		// Update log entry
		$logRow->update([
			'undone' => 1,
			'undone_timestamp' => date('Y-m-d H:i:s')
		]);
	}

	private function BatteryExists($batteryId)
	{
		$batteryRow = $this->getDatabase()->batteries()->where('id = :1', $batteryId)->fetch();
		return $batteryRow !== null;
	}
}
