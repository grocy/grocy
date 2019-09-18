$('#save-userentity-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#userentity-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("userentity-form");

	var redirectUrl = U("/userentities");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/userentities', jsonData,
			function(result)
			{
				window.location.href = redirectUrl;
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("userentity-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/userentities/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				window.location.href = redirectUrl;
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("userentity-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#userentity-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('userentity-form');
});

$('#userentity-form select').change(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('userentity-form');
});

$('#userentity-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('userentity-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-userentity-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('userentity-form');
