
$("input.user-setting-control:radio[name=night-mode]").on("change", function()
{
	Grocy.UserSettings.night_mode = $("input.user-setting-control:radio[name=night-mode]:checked").val();
	Grocy.FrontendHelpers.SaveUserSetting("night_mode", Grocy.UserSettings.night_mode, true);
	CheckNightMode();
});

$("#auto-night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	$("#auto-night-mode-time-range-from").prop("readonly", !value);
	$("#auto-night-mode-time-range-to").prop("readonly", !value);

	if (!value && !BoolVal(Grocy.UserSettings.night_mode_enabled_internal))
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

function CheckNightMode()
{
	if (Grocy.UserId === -1) // Not logged in => always use system preferred color scheme
	{
		Grocy.UserSettings.night_mode = "follow-system";
	}

	var nightModeEnabledInternalBefore = Grocy.UserSettings.night_mode_enabled_internal;

	if (Grocy.UserSettings.night_mode != "follow-system" && BoolVal(Grocy.UserSettings.auto_night_mode_enabled))
	{
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

		if (now.isBetween(start, end)) // We're INSIDE of night mode time range
		{
			Grocy.UserSettings.night_mode_enabled_internal = true;
		}
		else // We're OUTSIDE of night mode time range
		{
			Grocy.UserSettings.night_mode_enabled_internal = false;
		}
	}
	else
	{
		if (Grocy.UserSettings.night_mode == "on")
		{
			Grocy.UserSettings.night_mode_enabled_internal = true;
		}
		else if (Grocy.UserSettings.night_mode == "off")
		{
			Grocy.UserSettings.night_mode_enabled_internal = false;
		}
		else if (Grocy.UserSettings.night_mode == "follow-system")
		{
			Grocy.UserSettings.night_mode_enabled_internal = window.matchMedia("(prefers-color-scheme: dark)").matches;
		}
	}

	if (BoolVal(nightModeEnabledInternalBefore) != BoolVal(Grocy.UserSettings.night_mode_enabled_internal))
	{
		Grocy.FrontendHelpers.SaveUserSetting("night_mode_enabled_internal", BoolVal(Grocy.UserSettings.night_mode_enabled_internal), true);
	}

	if (BoolVal(Grocy.UserSettings.night_mode_enabled_internal))
	{
		if (!$("#night-mode-stylesheet").length)
		{
			$("<link>")
				.appendTo("head")
				.attr({
					rel: "stylesheet",
					href: U("/css/grocy_night_mode.css")
				});
		}

		$("body").addClass("night-mode");
	}
	else
	{
		$("body").removeClass("night-mode");
	}
}

if (Grocy.UserId !== -1)
{
	$("input.user-setting-control:radio[name=night-mode][value=" + Grocy.UserSettings.night_mode + "]").prop("checked", true);
	$("#auto-night-mode-enabled").prop("checked", BoolVal(Grocy.UserSettings.auto_night_mode_enabled));
	$("#auto-night-mode-time-range-goes-over-midgnight").prop("checked", BoolVal(Grocy.UserSettings.auto_night_mode_time_range_goes_over_midnight));
	$("#auto-night-mode-enabled").trigger("change");
	$("#auto-night-mode-time-range-from").val(Grocy.UserSettings.auto_night_mode_time_range_from);
	$("#auto-night-mode-time-range-from").trigger("keyup");
	$("#auto-night-mode-time-range-to").val(Grocy.UserSettings.auto_night_mode_time_range_to);
	$("#auto-night-mode-time-range-to").trigger("keyup");
}

if (Grocy.Mode === "production")
{
	setInterval(CheckNightMode, 60000);
}
else
{
	setInterval(CheckNightMode, 4000);
}

CheckNightMode();
