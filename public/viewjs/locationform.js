$('#save-location-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/locations', $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/locations');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/locations/' + Grocy.EditObjectId, $('#location-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/locations');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#location-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('location-form');
});

$('#location-form input').keydown(function (event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('location-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-location-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('location-form');
