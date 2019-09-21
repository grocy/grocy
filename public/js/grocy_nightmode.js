$("#night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	if (value)
	{
		$("body").addClass("night-mode");

		// Force disable auto night mode when night mode is enabled
		$("#auto-night-mode-enabled").prop("checked", false);
		$("#auto-night-mode-enabled").trigger("change");
	}
	else
	{
		$("body").removeClass("night-mode");
	}
});

$("#auto-night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	$("#auto-night-mode-time-range-from").prop("readonly", !value);
	$("#auto-night-mode-time-range-to").prop("readonly", !value);

	if (!value && !BoolVal(Grocy.UserSettings.night_mode_enabled))
	{
		$("body").removeClass("night-mode");
	}

	// Force disable night mode when auto night mode is enabled
	if (value)
	{
		$("#night-mode-enabled").prop("checked", false);
		$("#night-mode-enabled").trigger("change");
	}
});

$(document).on("keyup", "#auto-night-mode-time-range-from, #auto-night-mode-time-range-to", function()
{
	var value = $(this).val();
	var valueIsValid = moment(value, "HH:mm", true).isValid();
	
	if (valueIsValid)
	{
		$(this).removeClass("bg-danger");
	}
	else
	{
		$(this).addClass("bg-danger");
	}

	CheckNightMode();
});

$("#auto-night-mode-time-range-goes-over-midgnight").on("change", function()
{
	CheckNightMode();
});

if (Grocy.UserId !== -1)
{
	$("#night-mode-enabled").prop("checked", BoolVal(Grocy.UserSettings.night_mode_enabled));
	$("#auto-night-mode-enabled").prop("checked", BoolVal(Grocy.UserSettings.auto_night_mode_enabled));
	$("#auto-night-mode-time-range-goes-over-midgnight").prop("checked", BoolVal(Grocy.UserSettings.auto_night_mode_time_range_goes_over_midnight));
	$("#auto-night-mode-enabled").trigger("change");
	$("#auto-night-mode-time-range-from").val(Grocy.UserSettings.auto_night_mode_time_range_from);
	$("#auto-night-mode-time-range-from").trigger("keyup");
	$("#auto-night-mode-time-range-to").val(Grocy.UserSettings.auto_night_mode_time_range_to);
	$("#auto-night-mode-time-range-to").trigger("keyup");
}

function CheckNightMode()
{
	if (Grocy.UserId === -1 || !BoolVal(Grocy.UserSettings.auto_night_mode_enabled))
	{
		return;
	}

	var start = moment(Grocy.UserSettings.auto_night_mode_time_range_from, "HH:mm", true);
	var end = moment(Grocy.UserSettings.auto_night_mode_time_range_to, "HH:mm", true);
	var now = moment();

	if (!start.isValid() || !end.isValid)
	{
		return;
	}

	if (BoolVal(Grocy.UserSettings.auto_night_mode_time_range_goes_over_midnight))
	{
		end.add(1, "day");
	}

	if (start.isSameOrBefore(now) && end.isSameOrAfter(now)) // We're INSIDE of night mode time range
	{
		if (!$("body").hasClass("night-mode"))
		{
			$("body").addClass("night-mode");
			$("#currently-inside-night-mode-range").prop("checked", true);
			$("#currently-inside-night-mode-range").trigger("change");
		}
	}
	else // We're OUTSIDE of night mode time range
	{
		if ($("body").hasClass("night-mode"))
		{
			$("body").removeClass("night-mode");
			$("#currently-inside-night-mode-range").prop("checked", false);
			$("#currently-inside-night-mode-range").trigger("change");
		}		
	}
}
if (Grocy.UserId !== -1)
{
	CheckNightMode();
}

if (Grocy.Mode === "production")
{
	setInterval(CheckNightMode, 60000);
}
else
{
	setInterval(CheckNightMode, 4000);
}
