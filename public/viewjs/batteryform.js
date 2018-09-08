$('#save-battery-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/batteries', $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/batteries/' + Grocy.EditObjectId, $('#battery-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
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
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('battery-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-battery-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('battery-form');
