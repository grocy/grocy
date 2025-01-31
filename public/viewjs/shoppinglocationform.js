$('#save-shopping-location-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("shoppinglocation-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#shoppinglocation-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("shoppinglocation-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/shopping_locations', jsonData,
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
						window.location.href = U('/shoppinglocations');
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shoppinglocation-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/shopping_locations/' + Grocy.EditObjectId, jsonData,
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
						window.location.href = U('/shoppinglocations');
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shoppinglocation-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
			}
		);
	}
});

$('#shoppinglocation-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');
});

$('#shoppinglocation-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form'))
		{
			return false;
		}
		else
		{
			$('#save-shopping-location-button').click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);
Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');
