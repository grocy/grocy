$('#save-task-category-button').on('click', function(e)
{
	e.preventDefault();

	var jsonData = $('#task-category-form').serializeJSON();
	Grocy.FrontendHelpers.BeginUiBusy("task-category-form");

	if (Grocy.EditMode === 'create')
	{
		Grocy.Api.Post('objects/task_categories', jsonData,
			function(result)
			{
				Grocy.EditObjectId = result.created_object_id;
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/taskcategories');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-category-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
	else
	{
		Grocy.Api.Put('objects/task_categories/' + Grocy.EditObjectId, jsonData,
			function(result)
			{
				Grocy.Components.UserfieldsForm.Save(function()
				{
					window.location.href = U('/taskcategories');
				});
			},
			function(xhr)
			{
				Grocy.FrontendHelpers.EndUiBusy("task-category-form");
				Grocy.FrontendHelpers.ShowGenericError('Error while saving, probably this item already exists', xhr.response)
			}
		);
	}
});

$('#task-category-form input').keyup(function (event)
{
	Grocy.FrontendHelpers.ValidateForm('task-category-form');
});

$('#task-category-form input').keydown(function (event)
{
	if (event.keyCode === 13) //Enter
	{
		event.preventDefault();

		if (document.getElementById('task-category-form').checkValidity() === false) //There is at least one validation error
		{
			return false;
		}
		else
		{
			$('#save-task-category-button').click();
		}
	}
});

Grocy.Components.UserfieldsForm.Load();
$('#name').focus();
Grocy.FrontendHelpers.ValidateForm('task-category-form');
