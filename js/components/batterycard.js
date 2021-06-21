import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'

function batterycard(Grocy)
{

	Grocy.Components.BatteryCard = {};

	Grocy.Components.BatteryCard.Refresh = function(batteryId)
	{
		Grocy.Api.Get('batteries/' + batteryId,
			function(batteryDetails)
			{
				$('#batterycard-battery-name').text(batteryDetails.battery.name);
				$('#batterycard-battery-used_in').text(batteryDetails.battery.used_in);
				$('#batterycard-battery-last-charged').text((batteryDetails.last_charged || Grocy.translate('never')));
				$('#batterycard-battery-last-charged-timeago').attr("datetime", batteryDetails.last_charged || '');
				$('#batterycard-battery-charge-cycles-count').text((batteryDetails.charge_cycles_count || '0'));

				$('#batterycard-battery-edit-button').attr("href", Grocy.FormatUrl("/battery/" + batteryDetails.battery.id.toString()));
				$('#batterycard-battery-journal-button').attr("href", Grocy.FormatUrl("/batteriesjournal?embedded&battery=" + batteryDetails.battery.id.toString()));
				$('#batterycard-battery-edit-button').removeClass("disabled");
				$('#batterycard-battery-journal-button').removeClass("disabled");

				EmptyElementWhenMatches('#batterycard-battery-last-charged-timeago', Grocy.translate('timeago_nan'));
				RefreshContextualTimeago(".batterycard");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	};
}

export { batterycard }