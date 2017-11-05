<?php

class GrocyLogicBatteries
{
	public static function GetCurrent()
	{
		$sql = 'SELECT * from batteries_current';
		return Grocy::ExecuteDbQuery(Grocy::GetDbConnectionRaw(), $sql)->fetchAll(PDO::FETCH_OBJ);
	}

	public static function GetNextChargeTime(int $batteryId)
	{
		$db = Grocy::GetDbConnection();

		$battery = $db->batteries($batteryId);
		$batteryLastLogRow = Grocy::ExecuteDbQuery(Grocy::GetDbConnectionRaw(), "SELECT * from batteries_current WHERE battery_id = $batteryId LIMIT 1")->fetch(PDO::FETCH_OBJ);

		if ($battery->charge_interval_days > 0)
		{
			return date('Y-m-d H:i:s', strtotime('+' . $battery->charge_interval_days . ' day', strtotime($batteryLastLogRow->last_tracked_time)));
		}
		else
		{
			return date('Y-m-d H:i:s');
		}

		return null;
	}

	public static function GetBatteryDetails(int $batteryId)
	{
		$db = Grocy::GetDbConnection();

		$battery = $db->batteries($batteryId);
		$batteryChargeCylcesCount = $db->battery_charge_cycles()->where('battery_id', $batteryId)->count();
		$batteryLastChargedTime = $db->battery_charge_cycles()->where('battery_id', $batteryId)->max('tracked_time');

		return array(
			'battery' => $battery,
			'last_charged' => $batteryLastChargedTime,
			'charge_cycles_count' => $batteryChargeCylcesCount
		);
	}

	public static function TrackChargeCycle(int $batteryId, string $trackedTime)
	{
		$db = Grocy::GetDbConnection();

		$logRow = $db->battery_charge_cycles()->createRow(array(
			'battery_id' => $batteryId,
			'tracked_time' => $trackedTime
		));
		$logRow->save();

		return true;
	}
}
