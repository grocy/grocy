$('#save-task-button').on('click', function(e)
{
	e.preventDefault();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/tasks', $('#task-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/tasks');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Post('edit-object/tasks/' + Grocy.EditObjectId, $('#task-form').serializeJSON(),
			function(result)
			{
				window.location.href = U('/tasks');
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#task-form input').keyup(function(event)
{
	Grocy.FrontendHelpers.ValidateForm('task-form');
});

$('#task-form input').keydown(function(event)
{
	if (event.keyCode === 13) //Enter
	{
		if (document.getElementById('task-form').checkValidity() === false) //There is at least one validation error
		{
			event.preventDefault();
			return false;
		}
		else
		{
			$('#save-task-button').click();
		}
	}
});

$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('task-form');
