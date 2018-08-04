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
		$batteryChargeCylcesCount = $this->Database->battery_charge_cycles()->where('battery_id', $batteryId)->count();
		$batteryLastChargedTime = $this->Database->battery_charge_cycles()->where('battery_id', $batteryId)->max('tracked_time');

		return array(
			'battery' => $battery,
			'last_charged' => $batteryLastChargedTime,
			'charge_cycles_count' => $batteryChargeCylcesCount
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

		return true;
	}

	private function BatteryExists($batteryId)
	{
		$batteryRow = $this->Database->batteries()->where('id = :1', $batteryId)->fetch();
		return $batteryRow !== null;
	}
}
