Grocy.Components.BatteryCard = { };

Grocy.Components.BatteryCard.Refresh = function(batteryId)
{
	Grocy.Api.Get('batteries/get-battery-details/' + batteryId,
		function(batteryDetails)
		{
			$('#batterycard-battery-name').text(batteryDetails.battery.name);
			$('#batterycard-battery-used_in').text(batteryDetails.battery.used_in);
			$('#batterycard-battery-last-charged').text((batteryDetails.last_charged || 'never'));
			$('#batterycard-battery-last-charged-timeago').text($.timeago(batteryDetails.last_charged || ''));
			$('#batterycard-battery-charge-cycles-count').text((batteryDetails.charge_cycles_count || '0'));

			EmptyElementWhenMatches('#batterycard-battery-last-charged-timeago', L('timeago_nan'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
