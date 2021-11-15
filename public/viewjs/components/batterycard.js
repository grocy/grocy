Grocy.Components.BatteryCard = {};

Grocy.Components.BatteryCard.Refresh = function(batteryId)
{
	Grocy.Api.Get('batteries/' + batteryId,
		function(batteryDetails)
		{
			$('#batterycard-battery-name').text(batteryDetails.battery.name);
			$('#batterycard-battery-used_in').text(batteryDetails.battery.used_in);
			$('#batterycard-battery-last-charged').text((batteryDetails.last_charged || __t('never')));
			$('#batterycard-battery-last-charged-timeago').attr("datetime", batteryDetails.last_charged || '');
			$('#batterycard-battery-charge-cycles-count').text((batteryDetails.charge_cycles_count || '0'));

			$('#batterycard-battery-edit-button').attr("href", U("/battery/" + batteryDetails.battery.id.toString()));
			$('#batterycard-battery-journal-button').attr("href", U("/batteriesjournal?embedded&battery=" + batteryDetails.battery.id.toString()));
			$('#batterycard-battery-edit-button').removeClass("disabled");
			$('#batterycard-battery-journal-button').removeClass("disabled");

			RefreshContextualTimeago(".batterycard");
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
};
