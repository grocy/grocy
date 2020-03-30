$('#save-shopping-location-button').on('click', function(e)
{
	e.preventDefault();

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
					window.location.href = U('/shoppinglocations');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shoppinglocation-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
					window.location.href = U('/shoppinglocations');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shoppinglocation-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#shoppinglocation-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');
});

$('#shoppinglocation-form input').keydown(function (event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('shoppinglocation-form').checkValidity() === false) //There is at least one validation error
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
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');
