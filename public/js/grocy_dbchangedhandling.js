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
// If a change is detected, reload the current page, but only if already idling for at least 50 seconds
// and when there is no unsaved form data
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
					if ($("form.is-dirty").length === 0)
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
