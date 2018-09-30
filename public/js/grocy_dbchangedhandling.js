Grocy.Api.Get('system/get-db-changed-time',
	function(result)
	{
		Grocy.DatabaseChangedTime = moment(result.changed_time);
	},
	function(xhr)
	{
		console.error(xhr);
	}
);

// Check if the database has changed once a minute
// If a change is detected, reload the current page, but only if already idling for at least 50 seconds,
// when there is no unsaved form data and when the user enabled auto reloading
setInterval(function()
{
	Grocy.Api.Get('system/get-db-changed-time',
		function(result)
		{
			var newDbChangedTime = moment(result.changed_time);
			if (newDbChangedTime.isAfter(Grocy.DatabaseChangedTime))
			{
				if (Grocy.IdleTime >= 50)
				{
					if (Grocy.AutoReloadOnDatabaseChangeEnabled && $("form.is-dirty").length === 0)
					{
						window.location.reload();
					}
				}

				Grocy.DatabaseChangedTime = newDbChangedTime;
			}
		},
		function(xhr)
		{
			console.error(xhr);
		}
	);
}, 60000);

Grocy.IdleTime = 0;
Grocy.ResetIdleTime = function()
{
	Grocy.IdleTime = 0;
}
window.onmousemove = Grocy.ResetIdleTime;
window.onmousedown = Grocy.ResetIdleTime;
window.onclick = Grocy.ResetIdleTime;
window.onscroll = Grocy.ResetIdleTime;
window.onkeypress = Grocy.ResetIdleTime;

// Increase the idle time once every second
// On any interaction it will be reset to 0 (see above)
setInterval(function()
{
	Grocy.IdleTime += 1;
}, 1000);

$("#auto-reload-enabled").on("change", function()
{
	var value = $(this).is(":checked");
	
	Grocy.AutoReloadOnDatabaseChangeEnabled = value;

	jsonData = { };
	jsonData.value = value;
	Grocy.Api.Post('user/settings/auto_reload_on_db_change', jsonData,
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

if (Grocy.AutoReloadOnDatabaseChangeEnabled)
{
	$("#auto-reload-enabled").prop("checked", true);
}
