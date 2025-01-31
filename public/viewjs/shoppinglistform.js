$('#save-shopping-list-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("shopping-list-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#shopping-list-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("shopping-list-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/shopping_lists', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.parent.postMessage(WindowMessageBag("ShoppingListChanged", result.created_object_id), Grocy.BaseUrl);
					window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
					window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
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
		Grocy.Components.UserfieldsForm.Save(function()
		{
			Grocy.Api.Put('objects/shopping_lists/' + Grocy.EditObjectId, jsonData,
				function(result)
				{
					window.parent.postMessage(WindowMessageBag("ShoppingListChanged", Grocy.EditObjectId), Grocy.BaseUrl);
					window.parent.postMessage(WindowMessageBag("Ready"), Grocy.BaseUrl);
					window.parent.postMessage(WindowMessageBag("CloseLastModal"), Grocy.BaseUrl);
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

$('#shopping-list-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('shopping-list-form');
});

$('#shopping-list-form input').keydown(function(event)
{
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('shopping-list-form'))
		{
			return false;
		}
		else
		{
			$('#save-shopping-list-button').click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);
Grocy.FrontendHelpers.ValidateForm('shopping-list-form');
