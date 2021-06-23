import { WindowMessageBag } from '../helpers/messagebag';

function shoppinglocationformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-shopping-location-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#shoppinglocation-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("shoppinglocation-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/shopping_locations', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
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
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
		else
		{
			Grocy.Api.Put('objects/shopping_locations/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					userfields.Save(function()
					{
						if (Grocy.GetUriParam("embedded") !== undefined)
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
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
				}
			);
		}
	});

	$scope('#shoppinglocation-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');
	});

	$scope('#shoppinglocation-form input').keydown(function(event)
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
				$scope('#save-shopping-location-button').click();
			}
		}
	});

	userfields.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('shoppinglocation-form');

}

window.shoppinglocationformView = shoppinglocationformView;