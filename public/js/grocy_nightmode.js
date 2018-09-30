$("#night-mode-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	
	jsonData = { };
	jsonData.value = value;
	console.log(jsonData);
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

if (Grocy.NightModeEnabled)
{
	$("#night-mode-enabled").prop("checked", true);
}
