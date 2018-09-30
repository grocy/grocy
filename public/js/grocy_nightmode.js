$("#night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	
	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Post('user/settings/night_mode_enabled', jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);

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
	
	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Post('user/settings/auto_night_mode_enabled', jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);

	if (value)
	{
		$("#auto-night-mode-time-range-from").prop("readonly", false);
		$("#auto-night-mode-time-range-to").prop("readonly", false);
	}
	else
	{
		$("#auto-night-mode-time-range-from").prop("readonly", true);
		$("#auto-night-mode-time-range-to").prop("readonly", true);
	}
});

$("#auto-night-mode-time-range-from").on("blur", function()
{
	var value = $(this).val();
	
	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Post('user/settings/auto_night_mode_time_range_from', jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);
});

$("#auto-night-mode-time-range-to").on("blur", function()
{
	var value = $(this).val();
	
	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Post('user/settings/auto_night_mode_time_range_to', jsonData,
		function(result)
		{
			// Nothing to do...
		},
		function(xhr)
		{
			Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
		}
	);
});

$("#night-mode-enabled").prop("checked", Grocy.NightModeEnabled);
$("#auto-night-mode-enabled").prop("checked", Grocy.AutoNightModeEnabled);
