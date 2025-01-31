$('.save-task-button').on('click', function(e)
{
	e.preventDefault();

	if (!Grocy.FrontendHelpers.ValidateForm("task-form", true))
	{
		return;
	}

	if ($(".combobox-menu-visible").length)
	{
		return;
	}

	var jsonData = $('#task-form').serializeJSON();
	jsonData.assigned_to_user_id = jsonData.user_id;
	delete jsonData.user_id;
	jsonData.due_date = Grocy.Components.DateTimePicker.GetValue();

	Grocy.FrontendHelpers.BeginUiBusy("task-form");

	if (Grocy.EditMode === 'create')
	{
		var addAnother = $(e.currentTarget).hasClass("add-another");

		Grocy.Api.Post('objects/tasks', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					if (GetUriParam("embedded") !== undefined)
					{
						if (addAnother)
						{
							window.location.href = U('/task/new?embedded');
						}
						else
						{
							window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
						}
					}
					else
					{
						if (addAnother)
						{
							window.location.href = U('/task/new');
						}
						else
						{
							window.location.href = U('/tasks');
						}
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
					if (GetUriParam("embedded") !== undefined)
					{
						window.parent.postMessage(WindowMessageBag("Reload"), Grocy.BaseUrl);
					}
					else
					{
						window.location.href = U('/tasks');
					}
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response);
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
	if (event.keyCode === 13) // Enter
	{
		event.preventDefault();

		if (!Grocy.FrontendHelpers.ValidateForm('task-form'))
		{
			return false;
		}
		else
		{
			$('.save-task-button').first().click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
setTimeout(function()
{
	$('#name').focus();
}, Grocy.FormFocusDelay);
Grocy.Components.DateTimePicker.GetInputElement().trigger('input');
Grocy.FrontendHelpers.ValidateForm('task-form');
