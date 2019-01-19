Grocy.Components.BatteryCard = { };

Grocy.Components.BatteryCard.Refresh = function(batteryId)
{
	Grocy.Api.Get('batteries/' + batteryId,
		function(batteryDetails)
		{
			$('#batterycard-battery-name').text(batteryDetails.battery.name);
			$('#batterycard-battery-used_in').text(batteryDetails.battery.used_in);
			$('#batterycard-battery-last-charged').text((batteryDetails.last_charged || L('never')));
			$('#batterycard-battery-last-charged-timeago').text($.timeago(batteryDetails.last_charged || ''));
			$('#batterycard-battery-charge-cycles-count').text((batteryDetails.charge_cycles_count || '0'));

			$('#batterycard-battery-edit-button').attr("href", U("/battery/" + batteryDetails.battery.id.toString()));
			$('#batterycard-battery-edit-button').removeClass("disabled");

			EmptyElementWhenMatches('#batterycard-battery-last-charged-timeago', L('timeago_nan'));
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
