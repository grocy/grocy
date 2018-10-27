<?php

namespace Grocy\Services;

class BatteriesService extends BaseService
{
	public function GetCurrent()
	{
		$sql = 'SELECT * from batteries_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetBatteryDetails(int $batteryId)
	{
		if (!$this->BatteryExists($batteryId))
		{
			throw new \Exception('Battery does not exist');
		}

		$battery = $this->Database->batteries($batteryId);
		$batteryChargeCyclesCount = $this->Database->battery_charge_cycles()->where('battery_id = :1 AND undone = 0', $batteryId)->count();
		$batteryLastChargedTime = $this->Database->battery_charge_cycles()->where('battery_id = :1 AND undone = 0', $batteryId)->max('tracked_time');
		$nextChargeTime = $this->Database->batteries_current()->where('battery_id', $batteryId)->min('next_estimated_charge_time');

		return array(
			'battery' => $battery,
			'last_charged' => $batteryLastChargedTime,
			'charge_cycles_count' => $batteryChargeCyclesCount,
			'next_estimated_charge_time' => $nextChargeTime
		);
	}

	public function TrackChargeCycle(int $batteryId, string $trackedTime)
	{
		if (!$this->BatteryExists($batteryId))
		{
			throw new \Exception('Battery does not exist');
		}

		$logRow = $this->Database->battery_charge_cycles()->createRow(array(
			'battery_id' => $batteryId,
			'tracked_time' => $trackedTime
		));
		$logRow->save();

		return $this->Database->lastInsertId();
	}

	private function BatteryExists($batteryId)
	{
		$batteryRow = $this->Database->batteries()->where('id = :1', $batteryId)->fetch();
		return $batteryRow !== null;
	}

	public function UndoChargeCycle($chargeCycleId)
	{
		$logRow = $this->Database->battery_charge_cycles()->where('id = :1 AND undone = 0', $chargeCycleId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Charge cycle does not exist or was already undone');
		}

		// Update log entry
		$logRow->update(array(
			'undone' => 1,
			'undone_timestamp' => date('Y-m-d H:i:s')
		));
	}
}
