$('#save-battery-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("battery-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#battery-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("battery-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/batteries', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{
						window.location.href = U('/batteries');
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("battery-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/batteries/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{
						window.location.href = U('/batteries');
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("battery-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#battery-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('battery-form');
});

$('#battery-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('battery-form'))
		{
			return false;
		}
		else
		{
			$('#save-battery-button').click();
		}
	}
});

$(document).on('click', '.battery-grocycode-label-print', function(e)
{
	e.preventDefault();

	var batteryId = $(e.currentTarget).attr('data-battery-id');
	Grocy.Api.Get('batteries/' + batteryId + '/printlabel', function(labelData)
	{
		if (Grocy.Webhooks.labelprinter !== undefined)
		{
			Grocy.FrontendHelpers.RunWebhook(Grocy.Webhooks.labelprinter, labelData);
		}
	});
});

Grocy.Components.UserfieldsForm.Load();
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);
Grocy.FrontendHelpers.ValidateForm('battery-form');
