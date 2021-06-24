import { EmptyElementWhenMatches } from '../helpers/extensions'
import { RefreshContextualTimeago } from '../configs/timeago'

class chorecard
{
	constructor(Grocy, scopeSelector = null)
	{
		this.Grocy = Grocy;

		this.scopeSelector = scopeSelector;
		this.scope = scopeSelector != null ? $(scopeSelector) : $(document);
		var jScope = this.scope;
		this.$ = scopeSelector != null ? (selector) => jScope.find(selector) : $;

		this.Grocy.PreloadView("choresjournal");
	}

	Refresh(choreId)
	{
		var self = this;
		this.Grocy.Api.Get('chores/' + choreId,
			function(choreDetails)
			{
				self.$('#chorecard-chore-name').text(choreDetails.chore.name);
				self.$('#chorecard-chore-last-tracked').text((choreDetails.last_tracked || self.Grocy.translate('never')));
				self.$('#chorecard-chore-last-tracked-timeago').attr("datetime", choreDetails.last_tracked || '');
				self.$('#chorecard-chore-tracked-count').text((choreDetails.tracked_count || '0'));
				self.$('#chorecard-chore-last-done-by').text((choreDetails.last_done_by.display_name || self.Grocy.translate('Unknown')));

				self.$('#chorecard-chore-edit-button').attr("href", self.Grocy.FormatUrl("/chore/" + choreDetails.chore.id.toString()));
				self.$('#chorecard-chore-journal-button').attr("href", self.Grocy.FormatUrl("/choresjournal?embedded&chore=" + choreDetails.chore.id.toString()));
				self.$('#chorecard-chore-edit-button').removeClass("disabled");
				self.$('#chorecard-chore-journal-button').removeClass("disabled");

				if (choreDetails.chore.track_date_only == 1)
				{
					self.$("#chorecard-chore-last-tracked-timeago").addClass("timeago-date-only");
				}
				else
				{
					self.$("#chorecard-chore-last-tracked-timeago").removeClass("timeago-date-only");
				}

				EmptyElementWhenMatches(self.$('#chorecard-chore-last-tracked-timeago'), self.Grocy.translate('timeago_nan'));
				RefreshContextualTimeago(".chorecard");
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
}
export { chorecard }