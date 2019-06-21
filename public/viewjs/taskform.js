$('#save-task-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#task-form').serializeJSON();
	jsonData.assigned_to_user_id = jsonData.user_id;
	delete jsonData.user_id;
	jsonData.due_date = Grocy.Components.DateTimePicker.GetValue();

	Grocy.FrontendHelpers.BeginUiBusy("task-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/tasks', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/tasks');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/tasks/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/tasks');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-form");
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

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
Grocy.FrontendHelpers.ValidateForm('task-form');
