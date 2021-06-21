import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'

function chorecard(Grocy)
{
	Grocy.Components.ChoreCard = {};

	Grocy.Components.ChoreCard.Refresh = function(choreId)
	{
		Grocy.Api.Get('chores/' + choreId,
			function(choreDetails)
			{
				$('#chorecard-chore-name').text(choreDetails.chore.name);
				$('#chorecard-chore-last-tracked').text((choreDetails.last_tracked || Grocy.translate('never')));
				$('#chorecard-chore-last-tracked-timeago').attr("datetime", choreDetails.last_tracked || '');
				$('#chorecard-chore-tracked-count').text((choreDetails.tracked_count || '0'));
				$('#chorecard-chore-last-done-by').text((choreDetails.last_done_by.display_name || Grocy.translate('Unknown')));

				$('#chorecard-chore-edit-button').attr("href", Grocy.FormatUrl("/chore/" + choreDetails.chore.id.toString()));
				$('#chorecard-chore-journal-button').attr("href", Grocy.FormatUrl("/choresjournal?embedded&chore=" + choreDetails.chore.id.toString()));
				$('#chorecard-chore-edit-button').removeClass("disabled");
				$('#chorecard-chore-journal-button').removeClass("disabled");

				if (choreDetails.chore.track_date_only == 1)
				{
					$("#chorecard-chore-last-tracked-timeago").addClass("timeago-date-only");
				}
				else
				{
					$("#chorecard-chore-last-tracked-timeago").removeClass("timeago-date-only");
				}

				EmptyElementWhenMatches('#chorecard-chore-last-tracked-timeago', Grocy.translate('timeago_nan'));
				RefreshContextualTimeago(".chorecard");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	};
}
export { chorecard }