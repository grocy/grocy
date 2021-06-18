/* global __t */

function RefreshContextualTimeago(rootSelector = "#page-content")
{
	$.timeago.settings.allowFuture = true;
	$(rootSelector + " time.timeago").each(function()
	{
		var element = $(this);

		if (!element.hasAttr("datetime"))
		{
			element.text("")
			return
		}

		var timestamp = element.attr("datetime");

		if (timestamp.isEmpty())
		{
			element.text("")
			return
		}

		var isNever = timestamp && timestamp.substring(0, 10) == "2999-12-31";
		var isToday = timestamp && timestamp.substring(0, 10) == moment().format("YYYY-MM-DD");
		var isDateWithoutTime = element.hasClass("timeago-date-only");

		if (isNever)
		{
			element.prev().text(__t("Never"));
			element.text("");
		}
		else if (isToday)
		{
			element.text(__t("Today"));
		}
		else
		{
			element.timeago("update", timestamp);
		}

		if (isDateWithoutTime)
		{
			element.prev().text(element.prev().text().substring(0, 10));
		}
	});
}

export { RefreshContextualTimeago };