$('#save-shopping-list-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#shopping-list-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("shopping-list-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/shopping_lists', jsonData,
			function(result)
			{
				window.location.href = U('/shoppinglist');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shopping-list-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/shopping_lists/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/shoppinglist');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("shopping-list-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#shopping-list-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('shopping-list-form');
});

$('#shopping-list-form input').keydown(function (event)
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
			$('#save-shopping-list-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('shopping-list-form');
