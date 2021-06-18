import { BoolVal } from './extensions'

class HeaderClock
{
	constructor(Grocy)
	{
		this.Grocy = Grocy;
		this.HeaderClockInterval = null;
		this.CheckHeaderClockEnabled();

		if (this.Grocy.UserId !== -1 && BoolVal(this.Grocy.UserSettings.show_clock_in_header))
		{
			$("#show-clock-in-header").prop("checked", true);
		}
	}

	RefreshHeaderClock()
	{
		$("#clock-small").text(moment().format("l LT"));
		$("#clock-big").text(moment().format("LLLL"));
	}

	CheckHeaderClockEnabled()
	{
		if (this.Grocy.UserId === -1)
		{
			return;
		}

		// Refresh the clock in the header every second when enabled
		if (BoolVal(this.Grocy.UserSettings.show_clock_in_header))
		{
			this.RefreshHeaderClock();
			$("#clock-container").removeClass("d-none");

			this.HeaderClockInterval = setInterval(this.RefreshHeaderClock, 1000);
		}
		else
		{
			if (this.HeaderClockInterval !== null)
			{
				clearInterval(this.HeaderClockInterval);
				this.HeaderClockInterval = null;
			}

			$("#clock-container").addClass("d-none");
		}
	}
}

export { HeaderClock }