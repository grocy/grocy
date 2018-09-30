$("#night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	if (value)
	{
		$("body").addClass("night-mode");
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
});

$("#night-mode-enabled").prop("checked", Grocy.NightModeEnabled);
$("#auto-night-mode-enabled").prop("checked", Grocy.UserSettings.auto_night_mode_enabled);
