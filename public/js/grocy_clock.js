$(document).on("change", "#show-clock-in-header", function()
{
	CheckHeaderClockEnabled();
});

function RefreshHeaderClock()
{
	$("#clock-small").text(moment().format("l LT"));
	$("#clock-big").text(moment().format("LLLL"));
}

Grocy.HeaderClockInterval = null;
function CheckHeaderClockEnabled()
{
	if (Grocy.UserId === -1)
	{
		return;
	}

	// Refresh the clock in the header every second when enabled
	if (BoolVal(Grocy.UserSettings.show_clock_in_header))
	{
		RefreshHeaderClock();
		$("#clock-container").removeClass("d-none");

		Grocy.HeaderClockInterval = setInterval(function()
		{
			RefreshHeaderClock();
		}, 1000);
	}
	else
	{
		if (Grocy.HeaderClockInterval !== null)
		{
			clearInterval(Grocy.HeaderClockInterval);
			Grocy.HeaderClockInterval = null;
		}

		$("#clock-container").addClass("d-none");
	}
}
CheckHeaderClockEnabled();

if (Grocy.UserId !== -1 && BoolVal(Grocy.UserSettings.show_clock_in_header))
{
	$("#show-clock-in-header").prop("checked", true);
}
