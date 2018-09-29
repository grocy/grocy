$('#save-task-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#task-form').serializeJSON();
	jsonData.assigned_to_user_id = jsonData.user_id;
	delete jsonData.user_id;
	jsonData.due_date = Grocy.Components.DateTimePicker.GetValue();

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('add-object/tasks', jsonData,
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
		Grocy.Api.Post('edit-object/tasks/' + Grocy.EditObjectId, jsonData,
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
		event.preventDefault();
		
		if (document.getElementById('task-form').checkValidity() === false) //There is at least one validation error
		{
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
