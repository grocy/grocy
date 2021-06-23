import { WindowMessageBag } from '../helpers/messagebag';

function shoppinglistformView(Grocy, scope = null)
{
	var $scope = $;
	if (scope != null)
	{
		$scope = $(scope).find;
	}

	var userfields = Grocy.Use("userfieldsform");

	$scope('#save-shopping-list-button').on('click', function(e)
	{
		e.preventDefault();

		if ($scope(".combobox-menu-visible").length)
		{
			return;
		}

		var jsonData = $scope('#shopping-list-form').serializeJSON();
		Grocy.FrontendHelpers.BeginUiBusy("shopping-list-form");

		if (Grocy.EditMode === 'create')
		{
			Grocy.Api.Post('objects/shopping_lists', jsonData,
				function(result)
				{
					Grocy.EditObjectId = result.created_object_id;
					userfields.Save(function()
					{
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", result.created_object_id), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					});
				},
				function(xhr)
				{
					Grocy.FrontendHelpers.EndUiBusy("shopping-list-form");
					Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
				}
			);
		}
		else
		{
			userfields.Save(function()
			{
				Grocy.Api.Put('objects/shopping_lists/' + Grocy.EditObjectId, jsonData,
					function(result)
					{
						window.parent.postMessage(WindowMessageBag("ShoppingListChanged", Grocy.EditObjectId), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
						window.parent.postMessage(WindowMessageBag("CloseAllModals"), Grocy.BaseUrl);
					},
					function(xhr)
					{
						Grocy.FrontendHelpers.EndUiBusy("shopping-list-form");
						Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
					}
				);
			});
		}
	});

	$scope('#shopping-list-form input').keyup(function(event)
	{
		Grocy.FrontendHelpers.ValidateForm('shopping-list-form');
	});

	$scope('#shopping-list-form input').keydown(function(event)
	{
		if (event.keyCode === 13) //Enter
		{
			event.preventDefault();

			if (document.getElementById('shopping-list-form').checkValidity() === false) //There is at least one validation error
			{
				return false;
			}
			else
			{
				$scope('#save-shopping-list-button').click();
			}
		}
	});

	userfields.Load();
	$scope('#name').focus();
	Grocy.FrontendHelpers.ValidateForm('shopping-list-form');

}


window.shoppinglistformView = shoppinglistformView
