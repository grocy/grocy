$('#save-battery-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#battery-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("battery-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('object/batteries', jsonData,
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("battery-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('object/batteries/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = U('/batteries');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("battery-form");
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
		event.preventDefault();

		if (document.getElementById('battery-form').checkValidity() === false) //There is at least one validation error
		{
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
